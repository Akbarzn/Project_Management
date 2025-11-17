<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Task;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectRequest;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardService;

class DashboardController extends Controller
{

    // public function index()
    // {
    //     $totalKaryawan = Karyawan::count();
    //     $totalClient = Client::count();
    //     $totalProject = Project::count();
    //     $totalTask = Task::count();

    //     // === Hitung Karyawan yang sudah & belum memiliki task ===
    //     $sudahMemilikiTask = Karyawan::whereHas('tasks')->count();
    //     $belumMemilikiTask = Karyawan::whereDoesntHave('tasks')->count();


    //     $karyawans = Karyawan::withCount('projects')->get();

    //     $names = $karyawans->pluck('name');
    //     $projectCounts = $karyawans->pluck('projects_count');
    //     $idKaryawan = $karyawans->pluck('id');

    //     // === Data Project Progress per Role (TRANSFORMASI UTAMA) ===
    //     $projectData = Project::with(['projectRequest', 'tasks.karyawan'])
    //         ->get()
    //         ->map(function ($project) {
    //             // 1. Ambil semua Task yang terkait dengan Project ini
    //             $tasks = $project->tasks;

    //             // 2. Kelompokkan Task berdasarkan job_title Karyawan yang mengerjakannya
    //             // Menggunakan groupBy pada collection tasks.
    //             $rolesGrouped = $tasks->groupBy('karyawan.job_title');

    //             $roles = [];

    //             foreach ($rolesGrouped as $jobTitle => $tasksByRole) {
    //                 // Pastikan jobTitle tidak kosong (walaupun harusnya tidak jika relasi Karyawan ada)
    //                 if (empty($jobTitle)) {
    //                     continue;
    //                 }

    //                 // Hitung rata-rata progress HANYA untuk tasks di jobTitle ini
    //                 $progressRole = $tasksByRole->avg('progress') ?? 0;

    //                 $roles[] = [
    //                     'job_title' => $jobTitle,
    //                     'progress' => round($progressRole, 0), // Progress spesifik untuk Role ini
    //                 ];
    //             }

    //             // 3. Hitung total cost project (tidak berubah)
    //             $totalProgressProject = $tasks->avg('progress') ?? 0;

    //             return [
    //                 'nama_project' => $project->projectRequest->name_project ?? 'Tanpa Nama',
    //                 'total_project_progress' => round($totalProgressProject, 0), // Progress keseluruhan Project
    //                 'total_cost' => $project->total_cost ?? 0,
    //                 // KUNCI KRUSIAL: Array bertingkat yang berisi progress per peran
    //                 'roles' => $roles,
    //             ];
    //         });

    //         $projects = Project::with(['client', 'projectRequest', 'karyawans', 'approver'])
    //         ->latest()
    //         ->get();

    //     return view('manager.dashboard', compact('totalKaryawan', 'totalClient', 'totalProject', 'totalTask', 'names', 'projectCounts', 'projectData', 'sudahMemilikiTask', 'belumMemilikiTask', 'karyawans', 'idKaryawan','projects'));
    // }


    // public function showKaryawanProject($id)
    // {
    //     $karyawan = Karyawan::findOrFail($id);
    //     $projects = $karyawan->tasks()->with('project')->get(); // contoh relasi
    //     return view('manager.karyawans.project', compact('karyawan', 'projects'));
    // }


    protected $dashboardService;

    /**
     * Summary of __construct
     * injection dashboard dgn constructor
     * @param \App\Services\DashboardService $dashboardService
     */
    public function __construct(DashboardService $dashboardService){
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request){
        // ambil semua data yg dibutuhkan dashboard
        $data = $this->dashboardService->getDashboardData();
        // dd($data);
        if($request->has('project_id')){
            $data['projectDetail'] = $this->dashboardService->getProjectDetail($request->project_id);
        }
        return view("manager.dashboard", $data);
    }

    public function getProjectDetail($id){
        return response()->json(
            $this->dashboardService->getProjectDetail($id)
        );
    }
    
    public function showKaryawanProject($id){
        // ambil data karyawan dan project
        $data = $this->dashboardService->getKaryawanProjects($id);

        return view("manager.karyawans.project", $data);
    }

}

