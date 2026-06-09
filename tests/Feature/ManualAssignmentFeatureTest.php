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
 * ManualAssignmentFeatureTest (Feature Test)
 *
 * Menguji endpoint POST /manager/projects/{project}/assignment/manual
 *
 * Manual assignment berbeda dari auto:
 * - Manager yang menentukan siapa yang di-assign
 * - Sistem memvalidasi overload (kecuali force_assign=true)
 * - Bisa assign beberapa karyawan sekaligus
 * - Response berisi 'assigned' dan 'rejected' secara terpisah
 */
class ManualAssignmentFeatureTest extends TestCase
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
    // TEST GRUP 1: Manual Assignment – Skenario Sukses
    // =========================================================================

    /**
     * TEST 1.1 – Manual Assign Berhasil untuk Karyawan Valid
     *
     * Input   : karyawan_ids = [id_karyawan], karyawan tidak overload
     * Expected:
     *   - HTTP 200
     *   - success = true
     *   - assigned berisi 1 karyawan
     *   - rejected kosong
     *   - record di karyawan_projects dan tasks
     */
    public function test_manual_assign_success(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan(['max_workload' => 40]);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [$karyawan->id],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success', 'message', 'assigned', 'rejected'
            ]);

        // Pastikan assigned berisi data dan rejected kosong
        $response->assertJsonCount(1, 'assigned');
        $response->assertJsonCount(0, 'rejected');

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
     * TEST 1.2 – Assign Beberapa Karyawan Sekaligus
     *
     * Input   : karyawan_ids = [id1, id2, id3]
     * Expected:
     *   - 3 assigned, 0 rejected
     *   - 3 record di karyawan_projects
     *   - 3 record di tasks
     */
    public function test_manual_assign_multiple_employees(): void
    {
        $project = $this->makeProject();

        $k1 = $this->makeKaryawan(['max_workload' => 40]);
        $k2 = $this->makeKaryawan(['max_workload' => 40]);
        $k3 = $this->makeKaryawan(['max_workload' => 40]);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [$k1->id, $k2->id, $k3->id],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'assigned')
            ->assertJsonCount(0, 'rejected');

        $this->assertEquals(3,
            DB::table('karyawan_projects')->where('project_id', $project->id)->count()
        );
    }

    // =========================================================================
    // TEST GRUP 2: Overload Validation
    // =========================================================================

    /**
     * TEST 2.1 – Karyawan Overloaded Ditolak
     *
     * Skenario (formula baru):
     *   - workload_score = (active_tasks * 2) + (active_projects * 5)
     *   - Buat 5 project ongoing → score = 0 + (5×5) = 25 > 20 → OVERLOADED
     *
     * Expected:
     *   - HTTP 422 (semua ditolak)
     *   - rejected berisi karyawan tersebut dengan alasan 'Karyawan sudah overloaded'
     */
    public function test_overloaded_employee_is_rejected(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan();

        // Buat 5 project ongoing → workload = 5 >= 4 → OVERLOADED
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

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [$karyawan->id],
                'force_assign' => false,
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonCount(0, 'assigned')
            ->assertJsonCount(1, 'rejected');

        // Cek alasan penolakan
        $rejected = $response->json('rejected.0');
        $this->assertStringContainsString('overloaded', strtolower($rejected['reason']));

        // Tidak boleh ada record di pivot
        $this->assertDatabaseMissing('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
        ]);
    }

    /**
     * TEST 2.2 – Force Assign: Bypass Validasi Overload
     *
     * Skenario:
     *   - Karyawan overloaded (5 project → score = 25 > 20)
     *   - Manager memaksa dengan force_assign=true
     *
     * Expected:
     *   - HTTP 200
     *   - Karyawan berhasil di-assign meski overloaded
     */
    public function test_force_assign_bypasses_overload_validation(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan();

        // Buat 5 project ongoing → workload = 5 >= 4 → OVERLOADED
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

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [$karyawan->id],
                'force_assign' => true,   // ← bypass overload
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'assigned')
            ->assertJsonCount(0, 'rejected');

        // Meski overloaded, karyawan berhasil di-assign
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
        ]);
    }

    /**
     * TEST 2.3 – Partial Assign: Campuran Valid dan Overloaded
     *
     * Skenario: 2 karyawan dipilih, 1 valid, 1 overloaded.
     * k1 = tidak ada project aktif → score = 0 (Normal)
     * k2 = 5 project aktif → score = 25 > 20 (Overload)
     *
     * Expected:
     *   - 1 assigned, 1 rejected
     *   - HTTP 200 (karena ada yang berhasil)
     */
    public function test_partial_assign_when_some_overloaded(): void
    {
        $project = $this->makeProject();

        $k1 = $this->makeKaryawan(); // Valid – tidak ada project aktif
        $k2 = $this->makeKaryawan(); // Akan overloaded

        // Buat k2 overloaded: 5 project ongoing → workload = 5 >= 4
        for ($i = 0; $i < 5; $i++) {
            $otherProject = $this->makeProject(['status' => 'ongoing']);
            DB::table('karyawan_projects')->insert([
                'project_id'         => $otherProject->id,
                'karyawan_id'        => $k2->id,
                'cost_snapshot'      => 200000,
                'job_title_snapshot' => 'Dev',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            DB::table('tasks')->insert([
                'project_id'  => $otherProject->id,
                'karyawan_id' => $k2->id,
                'status'      => 'pending',
                'progress'    => 0,
                'catatan'     => 'test task',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [$k1->id, $k2->id],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(1, 'assigned')
            ->assertJsonCount(1, 'rejected');

        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $k1->id,
        ]);
        $this->assertDatabaseMissing('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $k2->id,
        ]);
    }

    // =========================================================================
    // TEST GRUP 3: Validasi Input
    // =========================================================================

    /**
     * TEST 3.1 – karyawan_ids Wajib Diisi
     *
     * Input   : karyawan_ids tidak dikirim
     * Expected: HTTP 422 (validation error)
     */
    public function test_karyawan_ids_is_required(): void
    {
        $project = $this->makeProject();

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['karyawan_ids']);
    }

    /**
     * TEST 3.2 – ID Karyawan Tidak Exists di Database → Masuk Rejected
     *
     * Input   : karyawan_ids = [9999] (ID tidak ada di DB)
     * Expected: Masuk ke rejected dengan reason 'Karyawan tidak ditemukan'
     */
    public function test_nonexistent_karyawan_id_is_rejected(): void
    {
        $project = $this->makeProject();

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [9999],
            ]);

        // Validasi Laravel akan menolak (exists rule)
        $response->assertStatus(422);
    }

    /**
     * TEST 3.3 – Karyawan Sudah Di-assign Sebelumnya → Masuk Rejected
     *
     * Skenario: Assign karyawan yang sudah ada di project ini.
     * Expected: Karyawan masuk rejected dengan alasan duplikat.
     */
    public function test_already_assigned_employee_is_rejected(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan(['max_workload' => 40]);

        // Assign pertama kali
        DB::table('karyawan_projects')->insert([
            'project_id'         => $project->id,
            'karyawan_id'        => $karyawan->id,
            'cost_snapshot'      => 200000,
            'job_title_snapshot' => 'Dev',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Coba assign lagi
        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.manual', $project), [
                'karyawan_ids' => [$karyawan->id],
            ]);

        $response->assertJsonCount(0, 'assigned')
            ->assertJsonCount(1, 'rejected');

        $rejected = $response->json('rejected.0');
        $this->assertStringContainsString('sudah diassign', strtolower($rejected['reason']));
    }
}
