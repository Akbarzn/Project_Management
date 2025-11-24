<?php 

namespace App\Repositories\Eloquent;

use App\Models\{Karyawan,Task,Client,Project};
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardRepository extends BaseRepository implements DashboardRepositoryInterface{
   
    public function __construct(Project $project){
        parent::__construct($project);
    }

    /**
     * ambil data karyawan,project,client dan task
     * ambil data total entity dan statistik task karyawan
     */
    public function getCounts(){
        // hitung total entitas
        $totalKaryawan = Karyawan::count();
        $totalClient = Client::count();
        $totalProject = Project::count();
        $totalTask = Task::count();

        // hitung jumlah karyawan yg sudah punya task
        $sudahMemilikiTask = Karyawan::whereHas('tasks')->count();
        
        // hitung jumlah karyawan yg belum punya task
        $belumMemilikiTask = Karyawan::whereDoesntHave('tasks')->count();

        return compact(
            'totalKaryawan',
            'totalClient',
            'totalProject',
            'totalTask',
            'sudahMemilikiTask',
            'belumMemilikiTask'
        );
    }

    /**
     * ambil daftar karyawan dan jumlah project yg dikerjakan
     * @return array
     */
    public function getKaryawanProjectInfo(){
        // ambil semua karyawan dan hitunf jumlah project yg dikaitkan
        $karyawans = Karyawan::withCount('projects')->get();
        return[
            'karyawans' => $karyawans,
            'names' => $karyawans->pluck('name'),
            'projectCounts' => $karyawans->pluck('projects_count'),
            'idKaryawan' => $karyawans->pluck('id'),
        ];
    }

    /**
     * Summary of getProjectData
     * ambil semua project dan progress berdasrkan job_title
     * tdk fungsi
     */
    public function getProjectData(){
        return Project::with(['projectRequest', 'tasks.karyawan'])
            ->get()
            ->map(function($project){
                // ambil semua task pada project yg terkait
                $tasks = $project->tasks;

                // kelompokan berdasarkan job_title
                $rolesGrouped = $tasks->groupBy('karyawan.job_title');

                $roles [] = [];

                foreach($rolesGrouped as $jobTitle => $tasksByRole){
                    // jika tdk ada job title maka lanjut
                    if(empty($jobTitle)) continue;

                    // hitung rat-rata progres per role
                    $progressRole = $tasksByRole->avg('progress') ?? 0;

                    $roles[] = [
                        'job_title' => $jobTitle,
                        'progress' => round($progressRole, 0),
                    ];
                }

                // hitung total progress project semua task
                $totalProgress = round($tasks->avg('progress') ?? 0,0);

                return [
                    'nama_project' => $project->projectRequest->name_project ?? 'Tanpa Nama',
                    'total_project_progress' => $totalProgress,
                    'total_cost' => $project->total_cost ?? 0,
                    'roles' => $roles,
                ];
            });
    }

    /**
     * ambil semua data project
     */
    public function getAllProjects(){
        return Project::with('client','projectRequest','karyawans','approver')
        ->latest()
        ->get();
    }

    /**
     * ambil data project berdasarkan idKaryawan
     */
    public function getKaryawanProjects($id){
          $karyawan = Karyawan::with(['tasks.project.projectRequest', 'tasks.workLogs'])->findOrFail($id);

    $tasks = $karyawan->tasks;

    $projects = $tasks->groupBy('project_id')->map(function ($tasksByProject) use ($karyawan) {

        $project = $tasksByProject->first()->project;

        // Total jam dari semua work logs milik role ini untuk project ini
        $totalHours = $tasksByProject->sum(function ($task) {
            return $task->workLogs->sum('hours');
        });

        return (object) [
            'project_id'   => $project->id,
            'project_name' => $project->projectRequest->name_project ?? 'Tanpa Nama',
            'status'       => $tasksByProject->last()->status,
            'total_hours'  => $totalHours,
            'total_cost'   => $totalHours * $karyawan->cost,
        ];
    });

    return [
        'karyawan' => $karyawan,
        'projects' => $projects,
    ];
    }

    public function getProjectDetail(int $id){
        $project = Project::with(['projectRequest', 'tasks.karyawan'])
        ->findOrFail($id);

        $tasks = $project->tasks;

        /**
         * group berdasarkan job_ztitle
         */
        $jobTitle = $tasks->groupBy('karyawan.job_title')
            ->map(function ($tasksByJobTitle, $jobTitle){
                return[
                    'job_title' => $jobTitle,
                    'progress' => round($tasksByJobTitle->avg('progress') ?? 0) 
                ];
            })->values();

            return [
                'name_project' => $project->projectRequest->name_project ??'-',
                'total_cost' => $project->total_cost ?? 0,
                'total_progress' => round($tasks->avg('progress') ?? 0),
                'jobTitle' => $jobTitle
            ];
    }
}

