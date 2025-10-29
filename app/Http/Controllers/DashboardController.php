<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Task;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    // public function index(Request $request){
    //     $totalKaryawan = Karyawan::count();
    //     $totalClient = Client::count();
    //     $totalProject = Project::count();
    
    // // return view ('manager.dashboard', compact('totalKaryawan','totalClient', 'totalProject'));

    // // --- Data Project per Bulan (chart lama) ---
    //     $projectsPerMonth = Project::select(
    //         DB::raw('MONTH(start_date_project) as month'),
    //         DB::raw('COUNT(*) as total')
    //     )
    //     ->whereYear('start_date_project', now()->year)
    //     ->groupBy('month')
    //     ->orderBy('month')
    //     ->get();

    //     $chartLabelsMonth = $projectsPerMonth->pluck('month')->map(fn($m) => date('F', mktime(0,0,0,$m,1)));
    //     $chartDataMonth   = $projectsPerMonth->pluck('total');

    //     // --- Data Project per Karyawan (chart baru) ---
    //     $karyawans = Karyawan::all();

    //     // Asumsi Project punya kolom karyawan_id
    //    $projectsPerKaryawan = DB::table('karyawan_project')
    // ->select('karyawan_id', DB::raw('COUNT(*) as total'))
    // ->groupBy('karyawan_id')
    // ->pluck('total', 'karyawan_id');


    //     $chartLabelsKaryawan = $karyawans->map(fn($k) => $k->name);
    //     $chartDataKaryawan   = $karyawans->map(fn($k) => $projectsPerKaryawan[$k->id] ?? 0);

    //     return view('manager.dashboard', compact(
    //         'totalKaryawan','totalClient','totalProject',
    //         'chartLabelsMonth','chartDataMonth',
    //         'chartLabelsKaryawan','chartDataKaryawan'
    //     ));
    // }

    public function index(){
        $totalKaryawan = Karyawan::count();
        $totalClient = Client::count();
        $totalProject = Project::count();
        $totalTask = Task::count();

         // === Hitung Karyawan yang sudah & belum memiliki task ===
        $sudahMemilikiTask = Karyawan::whereHas('tasks')->count();
        $belumMemilikiTask = Karyawan::whereDoesntHave('tasks')->count();

          
           $karyawans = Karyawan::withCount('projects')->get();

        $names = $karyawans->pluck('name');
        $projectCounts = $karyawans->pluck('projects_count');

        // === Data Project Progress per Role (TRANSFORMASI UTAMA) ===
        $projectData = Project::with(['projectRequest', 'tasks.karyawan'])
            ->get()
            ->map(function ($project) {
                // 1. Ambil semua Task yang terkait dengan Project ini
                $tasks = $project->tasks;
                
                // 2. Kelompokkan Task berdasarkan job_title Karyawan yang mengerjakannya
                // Menggunakan groupBy pada collection tasks.
                $rolesGrouped = $tasks->groupBy('karyawan.job_title');
                
                $roles = [];

                foreach ($rolesGrouped as $jobTitle => $tasksByRole) {
                    // Pastikan jobTitle tidak kosong (walaupun harusnya tidak jika relasi Karyawan ada)
                    if (empty($jobTitle)) {
                        continue;
                    }

                    // Hitung rata-rata progress HANYA untuk tasks di jobTitle ini
                    $progressRole = $tasksByRole->avg('progress') ?? 0;

                    $roles[] = [
                        'job_title' => $jobTitle,
                        'progress' => round($progressRole, 0), // Progress spesifik untuk Role ini
                    ];
                }

                // 3. Hitung total cost project (tidak berubah)
                $totalProgressProject = $tasks->avg('progress') ?? 0;

                return [
                    'nama_project' => $project->projectRequest->name_project ?? 'Tanpa Nama',
                    'total_project_progress' => round($totalProgressProject, 0), // Progress keseluruhan Project
                    'total_cost' => $project->total_cost ?? 0,
                    // KUNCI KRUSIAL: Array bertingkat yang berisi progress per peran
                    'roles' => $roles,
                ];
            });
       
        return view('manager.dashboard', compact('totalKaryawan', 'totalClient', 'totalProject', 'totalTask', 'names', 'projectCounts', 'projectData','sudahMemilikiTask','belumMemilikiTask')); 
    }

     public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name_project' => 'required|string|max:255',
            'kategori' => 'required|in:New Aplikasi,Update Aplikasi',
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
        ]);

        // Generate nomor tiket unik
        $ticketNumber = 'RQ-' . now()->format('YmdHis'); 

        // Upload dokumen
        $documentPath = null;
        if ($request->hasFile('document')) {
            // Simpan file di storage/app/public/project-documents
            $documentPath = $request->file('document')->store('project-documents', 'public');
        }

        ProjectRequest::create([
            'client_id' => $validated['client_id'],
            'ticket_number' => $ticketNumber,
            'name_project' => $validated['name_project'],
            'kategori' => $validated['kategori'],
            'description' => $validated['description'],
            'document_path' => $documentPath,
            'status' => 'Pending', // Status awal
        ]);

        return redirect()->route('manager.dashboard')->with('success', 'Project Request berhasil dibuat atas nama client yang dipilih.');
    }
}

