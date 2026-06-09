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
 * WorkloadBalancingAlgorithmTest (Feature Test)
 *
 * Test utama yang memvalidasi algoritma Workload Balancing secara end-to-end.
 *
 * Ini adalah test terpenting untuk skripsi karena membuktikan bahwa:
 * 1. Sistem benar-benar memilih karyawan dengan workload terendah
 * 2. Formula workload (jam + project×5) diterapkan dengan benar
 * 3. Distribusi beban kerja terbagi rata (bukan random)
 *
 * Perbedaan dengan unit test:
 * - Unit test  : menguji komponen terisolasi (service, formula)
 * - Feature test: menguji sistem secara lengkap dari HTTP request hingga database
 */
class WorkloadBalancingAlgorithmTest extends TestCase
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

    private function addWorkLog(int $taskId, int $karyawanId, float $hours): void
    {
        DB::table('task_work_logs')->insert([
            'task_id'     => $taskId,
            'karyawan_id' => $karyawanId,
            'work_date'   => now()->toDateString(),
            'hours'       => $hours,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    private function assignKaryawanToProject(int $karyawanId, int $projectId): void
    {
        DB::table('karyawan_projects')->insert([
            'project_id'         => $projectId,
            'karyawan_id'        => $karyawanId,
            'cost_snapshot'      => 200000,
            'job_title_snapshot' => 'Dev',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Secara default buat 1 task pending agar project terhitung ACTIVE
        DB::table('tasks')->insert([
            'project_id'  => $projectId,
            'karyawan_id' => $karyawanId,
            'status'      => 'pending',
            'progress'    => 0,
            'catatan'     => 'test task',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    private function createActiveTask(int $karyawanId, int $projectId): int
    {
        return DB::table('tasks')->insertGetId([
            'project_id' => $projectId,
            'karyawan_id' => $karyawanId,
            'progress'   => 0,
            'status'     => 'inwork',
            'catatan'    => '-',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // =========================================================================
    // TEST INTI: Workload Balancing Algorithm
    // =========================================================================

    /**
     * TEST UTAMA – Skenario Lengkap Workload Balancing
     *
     * Setup lengkap:
     *   - 4 karyawan dengan profil workload berbeda-beda
     *   - k1: 1 project ongoing → workload = 1
     *   - k2: 0 project ongoing → workload = 0  ← TERINGAN
     *   - k3: 3 project ongoing → workload = 3
     *   - k4: 4 project ongoing → workload = 4 (OVERLOADED, max=4)
     *
     * Expected:
     *   - Hanya k1, k2, k3 yang jadi kandidat (k4 overloaded)
     *   - k2 yang dipilih (workload terendah = 0)
     *   - Prinsip: workload yang merata, bukan random
     */
    public function test_workload_balancing_selects_least_loaded_employee(): void
    {
        $targetProject = $this->makeProject(['required_skill' => null]);
        $helperProject = $this->makeProject(['status' => 'ongoing']);

        // k1: 1 project ongoing = workload 1
        $k1 = $this->makeKaryawan();
        $this->assignKaryawanToProject($k1->id, $helperProject->id);

        // k2: 0 project ongoing = workload 0 ← HARUSNYA DIPILIH
        $k2 = $this->makeKaryawan();

        // k3: 3 project ongoing = workload 3
        $k3 = $this->makeKaryawan();
        for ($i = 0; $i < 3; $i++) {
            $p = $this->makeProject(['status' => 'ongoing']);
            $this->assignKaryawanToProject($k3->id, $p->id);
        }

        // k4: 4 project ongoing = workload 4 → OVERLOADED (max=4)
        $k4 = $this->makeKaryawan();
        for ($i = 0; $i < 4; $i++) {
            $p = $this->makeProject(['status' => 'ongoing']);
            $this->assignKaryawanToProject($k4->id, $p->id);
        }

        // Jalankan auto-assign
        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $targetProject), ['limit' => 1]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // k2 HARUS yang terpilih (workload terendah = 0)
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $targetProject->id,
            'karyawan_id' => $k2->id,
        ]);

        // k1, k3, k4 TIDAK boleh terpilih
        foreach ([$k1->id, $k3->id, $k4->id] as $notSelectedId) {
            $this->assertDatabaseMissing('karyawan_projects', [
                'project_id'  => $targetProject->id,
                'karyawan_id' => $notSelectedId,
            ]);
        }
    }

    /**
     * TEST – Penalti Project Mempengaruhi Pemilihan
     *
     * Membuktikan bahwa jumlah project ongoing mempengaruhi pemilihan:
     * Karyawan dengan project ongoing paling sedikit akan selalu dipilih.
     *
     * Setup:
     *   - k1: 4 project ongoing → workload = 4 (overloaded)
     *   - k2: 1 project ongoing → workload = 1 ← DIPILIH
     */
    public function test_project_penalty_affects_candidate_selection(): void
    {
        $targetProject = $this->makeProject(['required_skill' => null]);

        // k1: 4 project ongoing → workload = 4
        $k1 = $this->makeKaryawan();
        for ($i = 0; $i < 4; $i++) {
            $p = $this->makeProject(['status' => 'ongoing']);
            $this->assignKaryawanToProject($k1->id, $p->id);
        }

        // k2: 1 project ongoing → workload = 1
        $k2 = $this->makeKaryawan();
        $helperProject = $this->makeProject(['status' => 'ongoing']);
        $this->assignKaryawanToProject($k2->id, $helperProject->id);

        // k2 seharusnya dipilih
        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $targetProject), ['limit' => 1]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $targetProject->id,
            'karyawan_id' => $k2->id,
        ]);
        $this->assertDatabaseMissing('karyawan_projects', [
            'project_id'  => $targetProject->id,
            'karyawan_id' => $k1->id,
        ]);
    }

    /**
     * TEST – Task yang complete tidak dihitung dalam Workload (Auto decrease)
     *
     * Ketika seluruh task milik seorang karyawan pada suatu project sudah complete:
     * Maka active project berkurang (workload berkurang).
     *
     * Setup:
     *   - k1: punya 1 project ongoing, tetapi seluruh task di project tersebut COMPLETE → workload = 0
     *   - k2: punya 1 project ongoing, dan punya task pending/inwork → workload = 1
     *
     * Expected: k1 yang dipilih (workload = 0, lebih ringan dari k2)
     */
    public function test_completed_tasks_not_counted_in_workload(): void
    {
        $targetProject = $this->makeProject(['required_skill' => null]);
        $helperProject1 = $this->makeProject(['status' => 'ongoing']);
        $helperProject2 = $this->makeProject(['status' => 'ongoing']);

        // k1: punya 1 project, tapi tasknya complete semua → workload = 0
        $k1 = $this->makeKaryawan();
        $this->assignKaryawanToProject($k1->id, $helperProject1->id);
        // Set task di helperProject1 agar complete semua
        DB::table('tasks')->where('karyawan_id', $k1->id)->where('project_id', $helperProject1->id)->update([
            'status' => 'complete',
            'progress' => 100,
        ]);

        // k2: punya 1 project dengan task incomplete → workload = 1
        $k2 = $this->makeKaryawan();
        $this->assignKaryawanToProject($k2->id, $helperProject2->id);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $targetProject), ['limit' => 1]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // k1 harus dipilih
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $targetProject->id,
            'karyawan_id' => $k1->id,
        ]);
    }

    /**
     * TEST – Project Status 'complete' Tidak Dihitung sebagai Project Aktif
     *
     * Karyawan yang ada di project dengan status 'complete' tidak
     * mendapat penalti workload. Hanya project 'ongoing' yang dihitung.
     *
     * Setup:
     *   - k1: assign ke 3 project COMPLETE → workload = 0
     *   - k2: assign ke 1 project ONGOING → workload = 1
     *
     * Expected: k1 dipilih karena workload = 0 < k2 workload = 1
     */
    public function test_completed_projects_not_counted_in_workload(): void
    {
        $targetProject = $this->makeProject(['required_skill' => null]);

        // k1: 3 project COMPLETE → workload = 0
        $k1 = $this->makeKaryawan();
        for ($i = 0; $i < 3; $i++) {
            $p = $this->makeProject(['status' => 'complete']); // ← COMPLETE
            $this->assignKaryawanToProject($k1->id, $p->id);
        }

        // k2: 1 project ONGOING → workload = 1
        $k2 = $this->makeKaryawan();
        $ongoingProject = $this->makeProject(['status' => 'ongoing']);
        $this->assignKaryawanToProject($k2->id, $ongoingProject->id);

        $response = $this->actingAs($this->manager)
            ->postJson(route('manager.projects.assignment.auto', $targetProject), ['limit' => 1]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        // k1 yang dipilih: project complete tidak dihitung
        $this->assertDatabaseHas('karyawan_projects', [
            'project_id'  => $targetProject->id,
            'karyawan_id' => $k1->id,
        ]);
    }

    /**
     * TEST – Workload Balancing via Suggest Endpoint
     *
     * Verifikasi bahwa endpoint GET suggest juga menerapkan
     * workload balancing (kandidat diurutkan workload terkecil).
     */
    public function test_suggest_endpoint_returns_balanced_candidates(): void
    {
        $project = $this->makeProject(['required_skill' => null]);

        $k1 = $this->makeKaryawan(); // workload = 0
        $k2 = $this->makeKaryawan(); // workload = 2
        $k3 = $this->makeKaryawan(); // workload = 1

        // k2: 2 project ongoing
        $p1 = $this->makeProject(['status' => 'ongoing']);
        $p2 = $this->makeProject(['status' => 'ongoing']);
        $this->assignKaryawanToProject($k2->id, $p1->id);
        $this->assignKaryawanToProject($k2->id, $p2->id);

        // k3: 1 project ongoing
        $p3 = $this->makeProject(['status' => 'ongoing']);
        $this->assignKaryawanToProject($k3->id, $p3->id);

        $response = $this->actingAs($this->manager)
            ->getJson(route('manager.projects.assignment.suggest', $project));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'project', 'candidates', 'total_candidates', 'required_skill'
            ]);

        $candidates = $response->json('candidates');

        // Urutan yang benar: k1(0) → k3(1) → k2(2)
        $this->assertEquals($k1->id, $candidates[0]['id'],
            'Kandidat pertama harus karyawan dengan workload=0'
        );
        $this->assertEquals($k3->id, $candidates[1]['id'],
            'Kandidat kedua harus karyawan dengan workload=1'
        );
        $this->assertEquals($k2->id, $candidates[2]['id'],
            'Kandidat ketiga harus karyawan dengan workload=2'
        );
    }
}
