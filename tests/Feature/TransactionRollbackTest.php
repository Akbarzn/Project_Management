<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * TransactionRollbackTest (Feature Test)
 *
 * Menguji mekanisme transaction rollback dalam ProjectAssignmentService.
 *
 * KONSEP TRANSAKSI DATABASE:
 * Dalam sistem critical seperti assignment karyawan, setiap operasi yang
 * melibatkan multiple tabel harus bersifat ATOMIK:
 * - Semua berhasil → COMMIT (data tersimpan)
 * - Ada yang gagal → ROLLBACK (semua dikembalikan ke kondisi awal)
 *
 * Dengan DB::transaction(), jika terjadi exception di tengah proses,
 * Laravel otomatis membatalkan semua perubahan.
 *
 * Mengapa penting?
 * Bayangkan: attach karyawan ke project berhasil, tapi create task gagal.
 * Tanpa transaksi → data inconsistent (karyawan di-assign tapi tidak ada task).
 * Dengan transaksi → kedua operasi dibatalkan, data tetap konsisten.
 */
class TransactionRollbackTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');
    }

    private function makeProject(array $attributes = []): Project
    {
        $client = Client::factory()->create();
        return Project::factory()->create(array_merge(
            ['client_id' => $client->id, 'status' => 'ongoing'],
            $attributes
        ));
    }

    private function makeKaryawan(array $data = []): Karyawan
    {
        $user = User::factory()->create();
        return Karyawan::factory()->create(array_merge(['user_id' => $user->id], $data));
    }

    // =========================================================================
    // TEST: Transaction Integrity
    // =========================================================================

    /**
     * TEST 1.1 – Data Konsisten Setelah Assign Berhasil
     *
     * Memastikan bahwa ketika auto-assign berhasil:
     * 1. Pivot table (karyawan_projects) terisi
     * 2. Tabel tasks terisi (task auto-generated)
     * Keduanya harus ada atau tidak ada sama sekali (atomik).
     */
    public function test_both_pivot_and_task_created_on_successful_assign(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan(['max_workload' => 40]);

        $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        // Keduanya HARUS ada (bukan salah satu)
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
            'status'      => 'pending',
        ]);
    }

    /**
     * TEST 1.2 – Rollback: Tidak Ada Data Tersimpan Jika Semua Gagal
     *
     * Skenario:
     *   - Tidak ada karyawan tersedia (semua overloaded)
     *   - Auto-assign melempar RuntimeException
     *   - DB::transaction → rollback otomatis
     *
     * Expected:
     *   - Tidak ada record di karyawan_projects
     *   - Tidak ada record di tasks
     */
    public function test_no_data_persisted_when_no_candidates_available(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan();

        // Buat overload: karyawan masuk 5 project ongoing → workload = 5 >= 4
        for ($i = 0; $i < 5; $i++) {
            $otherProject = $this->makeProject(['status' => 'ongoing']);
            DB::table('karyawan_projects')->insert([
                'project_id'         => $otherProject->id,
                'karyawan_id'        => $karyawan->id,
                'cost_snapshot'      => 200000,
                'job_title_snapshot' => 'Dev',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            DB::table('tasks')->insert([
                'project_id'  => $otherProject->id,
                'karyawan_id' => $karyawan->id,
                'status'      => 'pending',
                'progress'    => 0,
                'catatan'     => 'test task',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Auto-assign ke project baru – harus gagal
        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        $response->assertStatus(422)->assertJson(['success' => false]);

        // ROLLBACK: tidak ada data yang tersimpan di kedua tabel
        $this->assertDatabaseMissing('karyawan_projects', [
            'project_id' => $project->id,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $project->id,
        ]);
    }

    /**
     * TEST 1.3 – Snapshot Data Tersimpan di Pivot Table
     *
     * Ketika karyawan di-assign, sistem menyimpan snapshot:
     * - cost_snapshot      : gaji karyawan saat ini
     * - job_title_snapshot : jabatan karyawan saat ini
     *
     * Ini memastikan laporan historis tidak berubah walau data
     * karyawan diupdate di masa depan.
     */
    public function test_cost_and_job_title_snapshot_saved_in_pivot(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan([
            'max_workload' => 40,
            'cost'         => 350000,
            'job_title'    => 'Senior Laravel Dev',
        ]);

        $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'         => $project->id,
            'karyawan_id'        => $karyawan->id,
            'cost_snapshot'      => 350000,
            'job_title_snapshot' => 'Senior Laravel Dev',
        ]);
    }

    /**
     * TEST 1.4 – Task Auto-Generated dengan Status 'pending'
     *
     * Setiap auto-assign harus menghasilkan task baru dengan:
     * - status   = 'pending' (belum dikerjakan)
     * - progress = 0
     * - catatan  = 'Auto-assigned berdasarkan workload balancing'
     */
    public function test_auto_assign_generates_task_with_correct_default_values(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan(['max_workload' => 40]);

        $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        $this->assertDatabaseHas('tasks', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
            'status'      => 'pending',
            'progress'    => 0,
            'catatan'     => 'Auto-assigned berdasarkan workload balancing',
        ]);
    }

    /**
     * TEST 1.5 – Manual Assign Task Default Values
     *
     * Manual assign menghasilkan task dengan catatan '-' (bukan auto-assign).
     */
    public function test_manual_assign_generates_task_with_dash_catatan(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan(['max_workload' => 40]);

        $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [$karyawan->id],
            ]);

        $this->assertDatabaseHas('tasks', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
            'status'      => 'pending',
            'progress'    => 0,
            'catatan'     => '-',
        ]);
    }
}
