<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\Project\StoreProjectRequest;
use App\Http\Requests\Manager\Project\UpdateProjectRequest;
use App\Models\{Project, Karyawan, ProjectRequest, Task};
use Illuminate\Support\Facades\{Auth, DB};

class ProjectController extends Controller
{
    /**
     * Tampilkan daftar project.
     */
    public function index()
    {
        $projects = Project::with(['client', 'projectRequest', 'karyawans', 'approver'])
            ->latest()
            ->get();

        return view('managers.projects.index', compact('projects'));
    }

    /**
     * Tampilkan daftar project request yang statusnya masih pending.
     */
    public function showRequest()
    {
        $requests = ProjectRequest::with('client')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('managers.projects.show-projects', compact('requests'));
    }

    /**
     * Form untuk approve project request.
     */
    public function create($requestId)
    {
        $request = ProjectRequest::findOrFail($requestId);
        $karyawans = Karyawan::all();

        $requiredRoles = [
            'Analis',
            'Desainer',
            'Programmer',
            'Tester',
            'SysAdmin',
        ];

        return view('managers.projects.create', compact('request', 'karyawans', 'requiredRoles'));
    }

    /**
     * Simpan project yang sudah di-approve oleh manager.
     */
    public function store(StoreProjectRequest $request)
    {
        DB::transaction(function () use ($request) {
            $projectRequest = ProjectRequest::findOrFail($request->request_id);

            // 1️⃣ Buat data project
            $project = Project::create([
                'project_name'        => $request->project_name,
                'client_id'           => $projectRequest->client_id,
                'request_id'          => $projectRequest->id,
                'start_date_project'  => $request->start_date_project,
                'finish_date_project' => $request->finish_date_project,
                'status'              => 'ongoing',
                'created_by'          => Auth::id(),
                'approved_by'         => Auth::id(),
                'is_approved'         => true,
            ]);

            // 2️⃣ Assign karyawan ke project (tabel pivot)
            $project->karyawans()->attach($request->karyawan_ids);

            // 3️⃣ Optional: Buat task awal (kalau tabel Task sudah ada)
            // foreach ($request->karyawan_ids as $karyawanId) {
            //     Task::create([
            //         'project_id'      => $project->id,
            //         'karyawan_id'     => $karyawanId,
            //         'task'            => 'Initial Assignment',
            //         'status'          => 'pending',
            //         'progress'        => 0,
            //         'start_date_task' => $request->start_date_project,
            //     ]);
            // }

            // 4️⃣ Ubah status request menjadi approved
            $projectRequest->update(['status' => 'approve']);
        });

        return redirect()
            ->route('manager.projects.index')
            ->with('success', 'Project berhasil disetujui dan dibuat.');
    }

    /**
     * Detail project.
     */
    public function show(Project $project)
    {
        $project->load(['client', 'projectRequest', 'karyawans', 'tasks']);

        return view('managers.projects.show', compact('project'));
    }

    /**
     * Form edit project.
     */
    public function edit(Project $project)
    {
        $project->load(['client', 'projectRequest', 'karyawans']);
        $karyawans = Karyawan::all();

        return view('managers.projects.edit', compact('project', 'karyawans'));
    }

    /**
     * Update project.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update([
            'project_name'        => $request->project_name,
            'approved_by'         => Auth::id(),
            'is_approved'         => true,
            'status'              => 'ongoing',
            'start_date_project'  => $request->start_date_project,
            'finish_date_project' => $request->finish_date_project,
        ]);

        $project->karyawans()->sync($request->karyawan_ids);

        return redirect()
            ->route('managers.projects.index')
            ->with('success', 'Project berhasil diperbarui dan karyawan telah disinkronkan.');
    }

    /**
     * Hapus project.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('managers.projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }
}
