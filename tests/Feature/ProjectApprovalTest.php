<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProjectApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected ProjectRequest $projectRequest;
    protected array $employees = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $client = Client::factory()->create();
        $this->projectRequest = ProjectRequest::create([
            'client_id'          => $client->id,
            'status'             => 'pending',
            'name_project'       => 'Sistem Informasi Baru',
            'tiket'              => 'TKT-98765',
            'kategori'           => 'New Aplikasi',
            'description'        => 'Deskripsi sistem baru',
            'priority'           => 3,
            'difficulty'         => 3,
            'estimated_duration' => 10, // hours
        ]);

        // Create exactly one employee for each required role
        // Roles must match LeastLoadAssignmentService::ROLES (used by ProjectController 'otomatis')
        $roles = [
            'Business Analyst',
            'Programmer',
            'Database Functional',
            'Quality Test',
            'SysAdmin',
        ];

        foreach ($roles as $role) {
            $user = User::factory()->create();
            $this->employees[$role] = Karyawan::factory()->create([
                'user_id'      => $user->id,
                'job_title'    => $role,
                'max_workload' => 80,
                'cost'         => 150000,
                'level'        => 'Intermediate',
            ]);
        }
    }

    /**
     * Test approval with "otomatis" allocation method.
     */
    public function test_approve_with_otomatis_allocation_method(): void
    {
        $response = $this->actingAs($this->manager)
            ->post(route('manager.projects.store'), [
                'request_id'          => $this->projectRequest->id,
                'start_date_project'  => '2026-06-01',
                'finish_date_project' => '2026-06-15',
                'assignment_method'   => 'otomatis',
            ]);

        $project = Project::where('request_id', $this->projectRequest->id)->first();
        $this->assertNotNull($project);

        $response->assertRedirect(route('manager.projects.show', $project->id));

        // The auto assignment should have run and assigned the 5 employees with their roles
        $this->assertEquals(5, $project->karyawans()->count());

        foreach ($this->employees as $role => $employee) {
            $this->assertDatabaseHas('karyawan_projects', [
                'project_id'         => $project->id,
                'karyawan_id'        => $employee->id,
                'role'               => $role,
                'assigned_by_system' => true,
            ]);

            $this->assertDatabaseHas('tasks', [
                'project_id'  => $project->id,
                'karyawan_id' => $employee->id,
                'status'      => 'pending',
            ]);
        }

        // Project request status should be updated to approve
        $this->assertEquals('approve', $this->projectRequest->fresh()->status);
    }

    /**
     * Test approval with "manual" allocation method.
     */
    public function test_approve_with_manual_allocation_method(): void
    {
        $requiredRoles = [
            'Business Analyst',
            'Database Functional',
            'Programmer',
            'Quality Test',
            'SysAdmin',
        ];
        $karyawanIds = [];
        foreach ($requiredRoles as $role) {
            $karyawanIds[] = $this->employees[$role]->id;
        }

        $response = $this->actingAs($this->manager)
            ->post(route('manager.projects.store'), [
                'request_id'          => $this->projectRequest->id,
                'start_date_project'  => '2026-06-01',
                'finish_date_project' => '2026-06-15',
                'assignment_method'   => 'manual',
                'karyawan_ids'        => $karyawanIds,
            ]);

        $project = Project::where('request_id', $this->projectRequest->id)->first();
        $this->assertNotNull($project);

        $response->assertRedirect(route('manager.projects.show', $project->id));

        // Selected employees should be assigned manually with roles matching their index/positions
        $this->assertEquals(5, $project->karyawans()->count());

        $requiredRoles = [
            'Business Analyst',
            'Database Functional',
            'Programmer',
            'Quality Test',
            'SysAdmin',
        ];

        foreach ($karyawanIds as $index => $id) {
            $expectedRole = $requiredRoles[$index];
            $this->assertDatabaseHas('karyawan_projects', [
                'project_id'         => $project->id,
                'karyawan_id'        => $id,
                'role'               => $expectedRole,
                'assigned_by_system' => false,
            ]);

            $this->assertDatabaseHas('tasks', [
                'project_id'  => $project->id,
                'karyawan_id' => $id,
                'status'      => 'pending',
                'catatan'     => '-',
            ]);
        }

        // Project request status should be updated to approve
        $this->assertEquals('approve', $this->projectRequest->fresh()->status);
    }

    /**
     * Test manual approval validation fails if karyawan_ids is missing.
     */
    public function test_approve_manual_validation_fails_without_karyawans(): void
    {
        $response = $this->actingAs($this->manager)
            ->post(route('manager.projects.store'), [
                'request_id'          => $this->projectRequest->id,
                'start_date_project'  => '2026-06-01',
                'finish_date_project' => '2026-06-15',
                'assignment_method'   => 'manual',
            ]);

        $response->assertSessionHasErrors(['karyawan_ids']);
    }
}
