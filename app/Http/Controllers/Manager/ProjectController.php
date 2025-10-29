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

        return view('manager.projects.index', compact('projects'));
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

        return view('manager.projects.show-projects', compact('requests'));
    }

    /**
     * Form untuk approve project request.
     */
    public function create($requestId)
    {
        $request = ProjectRequest::findOrFail($requestId);
        $karyawans = Karyawan::all();

        $requiredRoles = [
            'Analisis Proses Bisnis',
            'Database Functional',
            'Programmer',
            'Quality Test',
            'SysAdmin',
        ];

        return view('manager.projects.create', compact('request', 'karyawans', 'requiredRoles'));
    }

    /**
     * Simpan project yang sudah di-approve oleh manager.
     */
    public function store(StoreProjectRequest $request)
    {
        DB::transaction(function () use ($request) {
                $projectRequest = ProjectRequest::findOrFail($request->request_id);
                
                $project = Project::create([
                    'name_project'        => $request->name_project,
                    'client_id'           => $projectRequest->client_id,
                    'request_id'          => $projectRequest->id,
                    'start_date_project'  => $request->start_date_project,
                    'finish_date_project' => $request->finish_date_project,
                    'status'              => 'ongoing',
                    'created_by'          => Auth::id(),
                    'approved_by'         => Auth::id(),
                    'is_approved'         => true,
                    'total_cost'          =>0,
                ]);

                // assign karyawan ke project (tabel pivot)
                $project->karyawans()->attach($request->karyawan_ids);

                $assignedKaryawans = Karyawan::whereIn('id', $request->karyawan_ids)->get();

                foreach ($assignedKaryawans as $karyawan) {
                // $taskName = $karyawan->job_title . ' Assignment'; 
                
                Task::create([
                    'project_id'      => $project->id,
                    'karyawan_id'     => $karyawan->id, 
                    'description_task'            => 'Initial Assignment for ' . $karyawan->job_title . '.', 
                    'status' =>'pending',
                    'progress'        => 0,
                    // 'start_date_task' => $request->start_date_project,
                ]);
            }

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
    
    $project->load(['client', 'projectRequest', 'karyawans', 'tasks.karyawan']);
    // Ambil ID karyawan yang sudah ditugaskan
   
    return view('manager.projects.show', compact('project'));
}


    /**
     * Form edit project.
     */
    public function edit(Project $project)
    {
        $project->load(['client', 'projectRequest', 'karyawans']);
        $karyawans = Karyawan::all();
         $selectedKaryawanIds = $project->karyawans->pluck('id')->toArray();
     $requiredRoles = [
            'Analisis Proses Bisnis',
            'Database Functional',
            'Programmer',
            'Quality Test',
            'SysAdmin',
        ];

        return view('manager.projects.edit', compact('project', 'karyawans','requiredRoles', 'selectedKaryawanIds'));
    }

    /**
     * Update project.
    */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        // dd($request->al  l());

       DB::transaction(function () use ($request, $project) {

            $project->update([
                'name_project'          => $request->name_project,
                'approved_by'           => Auth::id(),
                'is_approved'           => true,
                'status'                => 'ongoing',
                'start_date_project'    => $request->start_date_project,
                'finish_date_project'   => $request->finish_date_project,
            ]);

            // Sinkronisasi Karyawan di tabel pivot
            $project->karyawans()->sync($request->karyawan_ids);

            // 1. Dapatkan Karyawan yang Baru Ditambahkan (Perlu dibuatkan Task)
            $oldKaryawanIds = $project->tasks()->pluck('karyawan_id')->toArray();
            $newKaryawanIds = collect($request->karyawan_ids)->diff($oldKaryawanIds);
            
            if ($newKaryawanIds->isNotEmpty()) {
                $newKaryawans = Karyawan::whereIn('id', $newKaryawanIds)->get();
                foreach ($newKaryawans as $karyawan) {
                    Task::create([
                        'project_id'        => $project->id,
                        'karyawan_id'       => $karyawan->id, 
                        // 'job_title'         => $karyawan->job_title . ' Assignment',
                        'description_task'  => 'Initial Assignment for ' . $karyawan->job_title . '.', 
                        'status'            => 'pending',
                        'progress'          => 0,
                    ]);
                }
            }

            // 2. Dapatkan Karyawan yang Dihapus (Perlu dihapus Task-nya jika status masih pending)
            $removedKaryawanIds = collect($oldKaryawanIds)->diff($request->karyawan_ids);
            
            if ($removedKaryawanIds->isNotEmpty()) {
                // Hapus task hanya jika statusnya masih 'pending' atau 'inwork' (untuk menghindari penghapusan log kerja penting)
                // Jika ingin lebih aman, batasi hanya status 'pending'
                $project->tasks()
                    ->whereIn('karyawan_id', $removedKaryawanIds)
                    ->where('status', 'pending')
                    ->delete();
            }
        });


        return redirect()
            ->route('manager.projects.index')
            ->with('success', 'Project berhasil diperbarui dan karyawan telah disinkronkan.');
    }

    /**
     * Hapus project.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('manager.projects.index')
            ->with('success', 'Project berhasil dihapus.');
    }
}
