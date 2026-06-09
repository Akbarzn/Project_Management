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
 * WorkloadOverviewFeatureTest (Feature Test)
 *
 * Menguji endpoint GET /manager/projects/{project}/assignment/workload-overview
 *
 * Endpoint ini digunakan manager untuk memonitor distribusi beban kerja
 * seluruh karyawan dalam satu tampilan.
 *
 * Response berisi:
 * - total_karyawan    : total semua karyawan
 * - overloaded_count  : jumlah yang overloaded
 * - available_count   : jumlah yang masih available
 * - karyawans         : daftar dengan workload summary, diurutkan asc
 */
class WorkloadOverviewFeatureTest extends TestCase
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
    // TEST GRUP 1: Workload Overview
    // =========================================================================

    /**
     * TEST 1.1 – Response Struktur Benar
     *
     * Expected:
     *   - HTTP 200
     *   - Memiliki keys: total_karyawan, overloaded_count, available_count, karyawans
     */
    public function test_workload_overview_returns_correct_structure(): void
    {
        $project = $this->makeProject();

        $response = $this->actingAs($this->manager)
            ->getJson(route('manager.projects.assignment.workload-overview', $project));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_karyawan',
                'overloaded_count',
                'available_count',
                'karyawans' => [
                    '*' => [
                        'id', 'name', 'job_title', 'skills',
                        'active_hours', 'active_projects', 'workload_score',
                        'max_workload', 'is_overloaded', 'capacity_pct',
                    ]
                ]
            ]);
    }

    /**
     * TEST 1.2 – Menghitung Total, Overloaded, dan Available dengan Benar
     *
     * Setup:
     *   - k1: tidak ada beban → available
     *   - k2: 5 project ongoing (workload=5×5=25>20) → overloaded
     *
     * Expected:
     *   - total_karyawan   = 2
     *   - overloaded_count = 1
     *   - available_count  = 1    public function test_workload_overview_counts_overloaded_correctly(): void
    {
        $project = $this->makeProject();

        $k1 = $this->makeKaryawan(); // Available
        $k2 = $this->makeKaryawan(); // Akan overloaded

        // Buat k2 overloaded: assign ke 5 project ongoing → workload = 5
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
            ->getJson(route('manager.projects.assignment.workload-overview', $project));

        $response->assertStatus(200)
            ->assertJson([
                'total_karyawan'   => 2,
                'overloaded_count' => 1,
                'available_count'  => 1,
            ]);
    }

    /**
     * TEST 1.3 – Diurutkan Berdasarkan Workload Terkecil
     *
     * Setup:
     *   - k1: workload tinggi (punya banyak project)
     *   - k2: workload rendah
     *
     * Expected: k2 muncul di posisi pertama dalam array karyawans.
     */
    public function test_workload_overview_sorted_ascending(): void
    {
        $project = $this->makeProject();

        $k1 = $this->makeKaryawan(['max_workload' => 40]);
        $k2 = $this->makeKaryawan(['max_workload' => 40]);

        // Buat k1 punya lebih banyak project aktif
        $p1 = $this->makeProject(['status' => 'ongoing']);
        $p2 = $this->makeProject(['status' => 'ongoing']);
        $p3 = $this->makeProject(['status' => 'ongoing']);

        foreach ([$p1, $p2, $p3] as $p) {
            DB::table('karyawan_projects')->insert([
                'project_id'         => $p->id,
                'karyawan_id'        => $k1->id,
                'cost_snapshot'      => 200000,
                'job_title_snapshot' => 'Dev',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            DB::table('tasks')->insert([
                'project_id'  => $p->id,
                'karyawan_id' => $k1->id,
                'status'      => 'pending',
                'progress'    => 0,
                'catatan'     => 'test task',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        // k1: workload = 3
        // k2: workload = 0

        $response = $this->actingAs($this->manager)
            ->getJson(route('manager.projects.assignment.workload-overview', $project));

        $response->assertStatus(200);
        $karyawans = $response->json('karyawans');

        // k2 harus di posisi pertama (workload=0 < 3)
        $this->assertEquals($k2->id, $karyawans[0]['id'],
            'Karyawan dengan workload terkecil harus muncul pertama'
        );
        $this->assertEquals($k1->id, $karyawans[1]['id']);
    }

    /**
     * TEST 1.4 – workload_score Dihitung Benar (Verifikasi Formula di Response)
     *
     * Tujuan: Memastikan formula workload yang muncul di response konsisten.
     *
     * Setup:
     *   - Karyawan punya 2 project ongoing → workload = 2
     *
     * Expected:
     *   - workload_score = 2
     *   - capacity_pct   = 50.0% (2/4×100)
     *   - is_overloaded  = false (2 <= 4)
     */
    public function test_workload_score_in_response_matches_formula(): void
    {
        $project  = $this->makeProject();
        $karyawan = $this->makeKaryawan(['max_workload' => 40]);

        // Assign ke 2 project ongoing
        $p1 = $this->makeProject(['status' => 'ongoing']);
        $p2 = $this->makeProject(['status' => 'ongoing']);

        foreach ([$p1, $p2] as $p) {
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

        $response = $this->actingAs($this->manager)
            ->getJson(route('manager.projects.assignment.workload-overview', $project));

        $karyawans = $response->json('karyawans');
        $found     = collect($karyawans)->firstWhere('id', $karyawan->id);

        $this->assertNotNull($found, 'Karyawan harus muncul di overview');
        $this->assertEquals(2.0, $found['workload_score'],
            'workload_score = 2'
        );
        $this->assertEquals(50.0, $found['capacity_pct'],
            'capacity_pct = 2/4×100 = 50%'
        );
        $this->assertFalse($found['is_overloaded'], '2 <= 4 → not overloaded');
    }

    /**
     * TEST 1.5 – Tanpa Karyawan → Response Kosong
     *
     * Expected:
     *   - total_karyawan   = 0
     *   - overloaded_count = 0
     *   - available_count  = 0
     *   - karyawans        = []
     */
    public function test_workload_overview_empty_when_no_employees(): void
    {
        $project = $this->makeProject();

        $response = $this->actingAs($this->manager)
            ->getJson(route('manager.projects.assignment.workload-overview', $project));

        $response->assertStatus(200)
            ->assertJson([
                'total_karyawan'   => 0,
                'overloaded_count' => 0,
                'available_count'  => 0,
                'karyawans'        => [],
            ]);
    }

    /**
     * TEST 1.6 – Guest Tidak Bisa Akses Endpoint
     *
     * Expected: HTTP 401
     */
    public function test_unauthenticated_cannot_access_workload_overview(): void
    {
        $project = $this->makeProject();

        $response = $this->getJson(
            route('manager.projects.assignment.workload-overview', $project)
        );

        $response->assertStatus(401);
    }
}
