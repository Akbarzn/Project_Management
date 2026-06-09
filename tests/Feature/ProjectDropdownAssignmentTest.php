<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Models\User;
use App\Services\ProjectService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProjectDropdownAssignmentTest extends TestCase
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

    /**
     * Test project create data returns karyawans with correct workload score, sorted ASC
     */
    public function test_project_create_data_returns_sorted_karyawans_with_workload(): void
    {
        $client = Client::factory()->create();
        $projectRequest = ProjectRequest::create([
            'client_id' => $client->id,
            'status' => 'pending',
            'name_project' => 'Test Project',
            'tiket' => 'TKT-12345',
            'kategori' => 'Development',
            'description' => 'Test Description',
        ]);

        $k1 = $this->makeKaryawan(['name' => 'High Workload', 'max_workload' => 40]);
        $k2 = $this->makeKaryawan(['name' => 'Low Workload', 'max_workload' => 40]);

        // Assign k1 to multiple projects to increase workload
        $p1 = $this->makeProject(['status' => 'ongoing']);
        $p2 = $this->makeProject(['status' => 'ongoing']);
        foreach ([$p1, $p2] as $p) {
            DB::table('karyawan_projects')->insert([
                'project_id'         => $p->id,
                'karyawan_id'        => $k1->id,
                'cost_snapshot'      => 100000,
                'job_title_snapshot' => $k1->job_title,
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

        // Resolve ProjectService and call getCreateData
        $service = app(ProjectService::class);
        $data = $service->getCreateData($projectRequest->id);

        $karyawans = $data['karyawans'];

        $this->assertCount(2, $karyawans);
        // k2 should be first because workload score is 0, k1 workload score is 2
        $this->assertEquals($k2->id, $karyawans[0]->id);
        $this->assertEquals(0, $karyawans[0]->workload_score);
        $this->assertFalse($karyawans[0]->is_overloaded);

        $this->assertEquals($k1->id, $karyawans[1]->id);
        $this->assertEquals(2, $karyawans[1]->workload_score);
        $this->assertFalse($karyawans[1]->is_overloaded);
    }

    /**
     * Test project edit data returns karyawans with correct workload score, sorted ASC
     */
    public function test_project_edit_data_returns_sorted_karyawans_with_workload(): void
    {
        $project = $this->makeProject();

        $k1 = $this->makeKaryawan(['name' => 'Karyawan 1', 'max_workload' => 10]);
        $k2 = $this->makeKaryawan(['name' => 'Karyawan 2', 'max_workload' => 40]);

        // Assign k1 to 3 projects (workload = 3, not overloaded since 3 < 4)
        $p1 = $this->makeProject(['status' => 'ongoing']);
        $p2 = $this->makeProject(['status' => 'ongoing']);
        $p3 = $this->makeProject(['status' => 'ongoing']);
        foreach ([$p1, $p2, $p3] as $p) {
            DB::table('karyawan_projects')->insert([
                'project_id'         => $p->id,
                'karyawan_id'        => $k1->id,
                'cost_snapshot'      => 100000,
                'job_title_snapshot' => $k1->job_title,
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

        $service = app(ProjectService::class);
        $data = $service->getEditData($project);

        $groupKaryawan = $data['groupKaryawan'];
        $allKaryawans = collect($groupKaryawan)->flatten();

        $k1FromData = $allKaryawans->firstWhere('id', $k1->id);
        $k2FromData = $allKaryawans->firstWhere('id', $k2->id);

        $this->assertNotNull($k1FromData);
        $this->assertNotNull($k2FromData);

        // Verify workload and overloaded flag
        $this->assertEquals(3, $k1FromData->workload_score);
        $this->assertFalse($k1FromData->is_overloaded);

        $this->assertEquals(0, $k2FromData->workload_score);
        $this->assertFalse($k2FromData->is_overloaded);
    }
}
