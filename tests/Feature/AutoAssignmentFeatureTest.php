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
 * AutoAssignmentFeatureTest (Feature Test)
 *
 * Menguji endpoint POST /manager/projects/{project}/assignment/auto
 * secara end-to-end dengan database sungguhan (SQLite in-memory).
 *
 * Setiap test menggunakan RefreshDatabase agar kondisi DB bersih.
 *
 * Endpoint: POST /manager/projects/{project}/assignment/auto
 * Auth    : role:manager (Spatie Permission)
 */
class AutoAssignmentFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;

    /**
     * Setup: buat role manager dan user manager sebelum setiap test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles karena Spatie Permission butuh role di DB
        $this->seed(RoleSeeder::class);

        // Buat user dengan role manager
        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');
    }

    /**
     * Helper: buat project dengan client yang valid.
     */
    private function makeProject(array $attributes = []): Project
    {
        $client = Client::factory()->create();
        return Project::factory()->create(array_merge(
            ['client_id' => $client->id, 'status' => 'ongoing'],
            $attributes
        ));
    }

    /**
     * Helper: buat karyawan dengan user, skill opsional, dan tambahkan work log.
     */
    private function makeKaryawan(array $data = []): Karyawan
    {
        $user = User::factory()->create();
        return Karyawan::factory()->create(array_merge(['user_id' => $user->id], $data));
    }

    /**
     * Helper: tambahkan task work log untuk mensimulasikan beban kerja karyawan.
     */
    private function addWorkLog(int $taskId, int $karyawanId, float $hours, string $date = null): void
    {
        DB::table('task_work_logs')->insert([
            'task_id'     => $taskId,
            'karyawan_id' => $karyawanId,
            'work_date'   => $date ?? now()->toDateString(),
            'hours'       => $hours,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    // =========================================================================
    // TEST GRUP 1: Auto Assignment – Skenario Sukses
    // =========================================================================

    /**
     * TEST 1.1 – Auto Assign Berhasil (Skill Match)
     *
     * Skenario:
     *   - Project membutuhkan skill 'Laravel'
     *   - Ada 1 karyawan dengan skill Laravel, workload rendah
     *
     * Expected:
     *   - HTTP 200
     *   - success = true
     *   - 1 record di tabel karyawan_projects
     *   - 1 record di tabel tasks (auto-generated)
     */
    public function test_auto_assign_success_with_skill_match(): void
    {
        $project  = $this->makeProject(['required_skill' => 'Laravel', 'status' => 'ongoing']);
        $karyawan = $this->makeKaryawan(['skills' => ['Laravel', 'PHP'], 'max_workload' => 40]);

        $response = $this->actingAs($this->manager)
            ->postJson(
                route('manager.projects.assignment.auto', $project),
                ['limit' => 1]
            );

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success', 'message', 'assigned', 'fallback_used'
            ]);

        // Karyawan harus tercatat di tabel pivot
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
        ]);

        // Task harus di-generate otomatis
        $this->assertDatabaseHas('tasks', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
            'status'      => 'pending',
            'catatan'     => 'Auto-assigned berdasarkan workload balancing',
        ]);
    }

    /**
     * TEST 1.2 – Auto Assign Memilih Karyawan dengan Workload Terkecil
     *
     * Skenario:
     *   - 3 karyawan semua punya skill Laravel
     *   - Workload berbeda: k1=30, k2=10 (teringan), k3=20
     *
     * Expected:
     *   - k2 yang dipilih (workload terkecil = Workload Balancing)
     */
    public function test_auto_assign_selects_employee_with_lowest_workload(): void
    {
        $project = $this->makeProject(['required_skill' => 'Laravel', 'status' => 'ongoing']);

        // Buat 3 karyawan dengan skill Laravel
        $k1 = $this->makeKaryawan(['skills' => ['Laravel']]);
        $k2 = $this->makeKaryawan(['skills' => ['Laravel']]);
        $k3 = $this->makeKaryawan(['skills' => ['Laravel']]);

        // k1: 0 tasks, 0 projects -> workload = 0 (TERINGAN)

        // k2: 2 tasks, 1 project -> workload = (2*2) + 5 = 9
        $otherProject2 = $this->makeProject(['status' => 'ongoing']);
        DB::table('karyawan_projects')->insert([
            ['project_id' => $otherProject2->id, 'karyawan_id' => $k2->id,
             'cost_snapshot' => 200000, 'job_title_snapshot' => 'Dev', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $task2_1 = DB::table('tasks')->insertGetId([
            'project_id' => $otherProject2->id, 'karyawan_id' => $k2->id,
            'progress' => 0, 'status' => 'inwork', 'catatan' => '-',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $task2_2 = DB::table('tasks')->insertGetId([
            'project_id' => $otherProject2->id, 'karyawan_id' => $k2->id,
            'progress' => 0, 'status' => 'inwork', 'catatan' => '-',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // k3: 1 task, 1 project -> workload = (1*2) + 5 = 7
        $otherProject3 = $this->makeProject(['status' => 'ongoing']);
        DB::table('karyawan_projects')->insert([
            ['project_id' => $otherProject3->id, 'karyawan_id' => $k3->id,
             'cost_snapshot' => 200000, 'job_title_snapshot' => 'Dev', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $task3_1 = DB::table('tasks')->insertGetId([
            'project_id' => $otherProject3->id, 'karyawan_id' => $k3->id,
            'progress' => 0, 'status' => 'inwork', 'catatan' => '-',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // k1 yang harus dipilih karena workload paling kecil (0)
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $k1->id,
        ]);

        // k2 dan k3 TIDAK boleh di-assign
        $this->assertDatabaseMissing('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $k2->id,
        ]);
    }

    /**
     * TEST 1.3 – Fallback Digunakan Jika Tidak Ada Skill-Match
     *
     * Skenario:
     *   - Project butuh skill 'Python'
     *   - Karyawan yang ada hanya punya skill 'Laravel'
     *
     * Expected:
     *   - fallback_used = true dalam response
     *   - Karyawan tetap di-assign (fallback berhasil)
     */
    public function test_auto_assign_uses_fallback_when_no_skill_match(): void
    {
        $project  = $this->makeProject(['required_skill' => 'Python', 'status' => 'ongoing']);
        $karyawan = $this->makeKaryawan(['skills' => ['Laravel'], 'max_workload' => 40]);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'fallback_used' => true]);

        // Meski fallback, karyawan tetap di-assign
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $project->id,
            'karyawan_id' => $karyawan->id,
        ]);
    }

    /**
     * TEST 1.4 – Assign dengan Limit > 1
     *
     * Skenario: limit=2, ada 3 karyawan tersedia.
     * Expected: Hanya 2 karyawan yang di-assign.
     */
    public function test_auto_assign_respects_limit_parameter(): void
    {
        $project = $this->makeProject(['required_skill' => null, 'status' => 'ongoing']);

        $k1 = $this->makeKaryawan(['skills' => null, 'max_workload' => 40]);
        $k2 = $this->makeKaryawan(['skills' => null, 'max_workload' => 40]);
        $k3 = $this->makeKaryawan(['skills' => null, 'max_workload' => 40]);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 2]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // Tepat 2 record di pivot table
        $count = DB::table('karyawan_projects')
            ->where('project_id', $project->id)
            ->count();

        $this->assertEquals(2, $count, 'Tepat 2 karyawan yang harus di-assign sesuai limit');
    }

    // =========================================================================
    // TEST GRUP 2: Auto Assignment – Skenario Gagal/Error
    // =========================================================================

    /**
     * TEST 2.1 – Gagal: Tidak Ada Karyawan Tersedia (Semua Overloaded)
     *
     * Skenario:
     *   - Karyawan max_workload=1 (sangat mudah overload)
     *   - Karyawan punya project ongoing → workload = 5 >= 1 → OVERLOADED
     *
     * Expected:
     *   - HTTP 422
     *   - success = false
     *   - Pesan error bahwa tidak ada karyawan tersedia
     */
    public function test_auto_assign_fails_when_all_employees_overloaded(): void
    {
        $project  = $this->makeProject(['required_skill' => null, 'status' => 'ongoing']);
        $karyawan = $this->makeKaryawan(['skills' => null]);

        // Beri 5 project ongoing dengan task pending agar workload = 5 >= 4 -> OVERLOADED
        for ($i = 0; $i < 5; $i++) {
            $p = $this->makeProject(['status' => 'ongoing']);
            DB::table('karyawan_projects')->insert([
                'project_id'         => $p->id,
                'karyawan_id'        => $karyawan->id,
                'cost_snapshot'      => 200000,
                'job_title_snapshot' => 'Dev',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            DB::table('tasks')->insert([
                'project_id'  => $p->id,
                'karyawan_id' => $karyawan->id,
                'status'      => 'pending',
                'progress'    => 0,
                'catatan'     => 'test task',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Sekarang coba auto-assign ke project BARU
        $newProject = $this->makeProject(['required_skill' => null, 'status' => 'ongoing']);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $newProject), ['limit' => 1]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);

        // Tidak ada record baru di pivot
        $this->assertDatabaseMissing('karyawan_projects', [
            'project_id' => $newProject->id,
        ]);
    }

    /**
     * TEST 2.2 – Tidak Assign Dua Kali ke Karyawan yang Sama
     *
     * Skenario: Auto-assign dijalankan 2 kali ke project yang sama.
     * Expected: Karyawan hanya muncul SEKALI di pivot (idempoten).
     */
    public function test_auto_assign_does_not_duplicate_assignment(): void
    {
        $project  = $this->makeProject(['required_skill' => null, 'status' => 'ongoing']);
        $karyawan = $this->makeKaryawan(['skills' => null, 'max_workload' => 40]);

        // Assign pertama
        $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        // Assign kedua (seharusnya tidak duplicate)
        $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 1]);

        // Harus hanya ada 1 record di pivot
        $count = DB::table('karyawan_projects')
            ->where('project_id', $project->id)
            ->where('karyawan_id', $karyawan->id)
            ->count();

        $this->assertEquals(1, $count, 'Duplicate assignment harus dicegah');
    }

    /**
     * TEST 2.3 – Validasi: Parameter limit Tidak Valid
     *
     * Input  : limit = 0 (di bawah minimum)
     * Expected: HTTP 422 (validation error)
     */
    public function test_auto_assign_rejects_invalid_limit(): void
    {
        $project = $this->makeProject();

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $project), ['limit' => 0]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['limit']);
    }

    /**
     * TEST 2.4 – Guest Tidak Bisa Akses Endpoint
     *
     * Expected: Redirect ke login (401/302)
     */
    public function test_unauthenticated_user_cannot_auto_assign(): void
    {
        $project = $this->makeProject();

        $response = $this->postJson(route('manager.projects.assignment.auto', $project));

        $response->assertStatus(401);
    }
}
