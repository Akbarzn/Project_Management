<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Services\ProjectService;
use App\Services\LeastLoadAssignmentService;
use App\Models\Project;
use Illuminate\Http\Request;


class ProjectController extends Controller
{
    /**
     * Inject ProjectService + LeastLoadAssignmentService.
     * LeastLoadAssignment dijalankan otomatis saat manager approve (store).
     */
    public function __construct(
        protected ProjectService $service,
        protected LeastLoadAssignmentService $leastLoadAssignmentService
    ) {}

    public function index(Request $request)
    {
        $search   = $request->get("search");
        $projects = $this->service->listProjects($search);
        return view('manager.projects.index', compact('projects'));
    }

    public function create($requestId)
    {
        $projects = $this->service->getCreateData($requestId);
        return view('manager.projects.create', $projects);
    }

    public function show($id)
    {
        return view('manager.projects.show', $this->service->showProject($id));
    }

    /**
     * Simpan project baru (approve project request) + jalankan Auto Assignment.
     *
     * Alur:
     * 1. ProjectService::create()            → buat project, set status request = 'approve'
     * 2. AutoAssignmentService::assignProjectTeam() → bentuk tim 5 role (DB::transaction)
     * 3. Redirect ke show project dengan flash message
     */
    public function store(StoreProjectRequest $request)
    {
        $validatedData = $request->validated();
        $project = $this->service->create($validatedData);

        if ($validatedData['assignment_method'] === 'otomatis') {
            // Jalankan Full Auto Assignment setelah project tersimpan
            try {
                $result  = $this->leastLoadAssignmentService->assignTeam($project);
                $message = $result['message'];

                if (!empty($result['fallbacks'])) {
                    $roles   = implode(', ', array_keys($result['fallbacks']));
                    $message .= " (Fallback pada: {$roles})";
                }

                return redirect()
                    ->route('manager.projects.show', $project->id)
                    ->with('success', 'Project berhasil dibuat & tim otomatis terbentuk. ' . $message);

            } catch (\RuntimeException $e) {
                // Project tersimpan tapi assignment gagal (tidak ada karyawan untuk role tertentu)
                return redirect()
                    ->route('manager.projects.show', $project->id)
                    ->with('warning', 'Project berhasil dibuat, namun auto assignment gagal: ' . $e->getMessage());
            }
        }

        // Jika manual, direct langsung ke detail project dengan flash message sukses
        return redirect()
            ->route('manager.projects.show', $project->id)
            ->with('success', 'Project berhasil dibuat dengan tim yang ditentukan secara manual.');
    }

    public function edit(Project $project)
    {
        $data = $this->service->getEditData($project);
        return view('manager.projects.edit', $data);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->service->update($project, $request->validated());
        return redirect()->route('manager.projects.index')->with('success', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $this->service->delete($project);
        return redirect()->route('manager.projects.index')->with('success', 'Project berhasil dihapus.');
    }
}