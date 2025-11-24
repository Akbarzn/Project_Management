<?php

namespace App\Services;

use App\Models\ProjectRequest;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\Client;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class ProjectService
{
    protected ProjectRepositoryInterface $repository;

    public function __construct(ProjectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    private function getRequiredJobTitle()
    {
        return [
            'Analisis Proses Bisnis',
            'Database Functional',
            'Programmer',
            'Quality Test',
            'SysAdmin',
        ];
    }

    // ambil semua project dgn fitur search
    public function listProjects(?string $search = null)
    {
        return $this->repository->getAllWithFilter($search);
    }

    /**
     * Summary of getCreateData
     * data untuk halaman create project
     * ambil data project request + daftar karyawan
     * @param mixed $requestId
     * @return array{karyawans: \Illuminate\Database\Eloquent\Collection<int, Karyawan>, pending_request: \Illuminate\Database\Eloquent\Collection<int, ProjectRequest>, projectRequest: ProjectRequest|\Illuminate\Database\Eloquent\Collection<int, ProjectRequest>|null, requiredRoles: string[]}
     */
    public function getCreateData($requestId = null)
    {
        $request = $requestId ? ProjectRequest::findOrFail($requestId) : null;
        return [
            'projectRequest' => $request,
            'pending_request' => ProjectRequest::with('client')
                ->where('status', 'pending')
                ->get(['id', 'name_project', 'client_id']),
            'karyawans' => Karyawan::all(['id', 'name', 'job_title', 'cost']),
            'requiredRoles' => $this->getRequiredJobTitle(),
        ];
    }

    /**
     * Summary of getEditData
     * data untuk halaman edit project
     * @param Project $project
     * @return array{clients: \Illuminate\Database\Eloquent\Collection<int, Client>, karyawans: \Illuminate\Database\Eloquent\Collection<int, Karyawan>, project: Project, requiredRoles: string[], selectedKaryawanIds: array}
     */
    public function getEditData(Project $project)
    {
        $project->load(['client', 'projectRequest', 'karyawans']);
       
        // Semua karyawan untuk dropdown
    $allKaryawans = Karyawan::all(['id', 'name', 'job_title']);

    // Mapping role → karyawan_id berdasarkan snapshot
    $assigned = [];

    foreach ($project->karyawans as $k) {
        $role = $k->pivot->job_title_snapshot;
        $assigned[$role] = $k->id;
    }

        return [
            'project' => $project,
            // 'karyawans' => Karyawan::all(['id', 'name', 'job_title', 'cost','job_title_snapshot']),
            'assigned' =>$assigned,
            'allKaryawans' => $allKaryawans,
            'clients' => Client::all(['id', 'name']),
            'selectedKaryawanIds' => $project->karyawans->pluck('id')->toArray(),
            'requiredJobTitle' => $this->getRequiredJobTitle(),
        ];
    }

    /**
     * Summary of create
     * approve project
     * simpan snapshot cost dan job_title ke pivot
     * generate task otomatis untuk tiap karyawan
     * ubah status request jadi approve
     * @param array $data
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            // ambil data client dari project request
            $projectRequest = ProjectRequest::findOrFail($data['request_id']);

            // buat data project
            $project = $this->repository->create([
                'client_id' => $projectRequest->client_id,
                'request_id' => $projectRequest->id,
                'start_date_project' => $data['start_date_project'],
                'finish_date_project' => $data['finish_date_project'],
                'status' => 'ongoing',
                'approved_by' => Auth::id(),
                'is_approved' => true,
                'total_cost' => 0,
            ]);

            // simpanpivot snapshhot utk setiap karyawan
            $syncData = [];
            foreach ($data['karyawan_ids'] as $id) {
                $karyawan = Karyawan::findOrFail($id);
                $syncData[$id] = [
                    'cost_snapshot' => $karyawan->cost,
                    'job_title_snapshot' => $karyawan->job_title
                ];
            }
            $project->karyawans()->attach($syncData);

            // generate task untuk setiap karyawan
            foreach ($data['karyawan_ids'] as $id) {
                $karyawan = Karyawan::findOrFail($id);
                $project->tasks()->create([
                    'karyawan_id' => $id,
                    'catatan' => '-',
                    'status' => 'pending',
                    'progress' => 0,
                ]);
            }

            // update status project request jadi approve
            $projectRequest->update(['status' => 'approve']);

            return $project->load(['client', 'projectRequest', 'karyawans']);
        });
    }

    /**
     * Summary of showProject
     * urutkan task berdasarkan job_title snapshot
     * hitung cost, jam kerja, hari kerja
     * hitung total cost project
     * @param int $id
     * @return array
     */
    public function showProject(int $id): array
    {
        // ambil data projcet
        $project = $this->repository->findById($id);
        $requiredRoles = $this->getRequiredJobTitle();

        // ambil data jobtitle
        $jobTitleSnapshot = $project->karyawans->pluck('pivot.job_title_snapshot', 'id')->toArray(); //map id karyawan ke jobtitle snapshot

        // sort berdasarkan jobtitle
        $sortedJobTitle = $project->tasks->sortBy(function ($task) use ($requiredRoles, $jobTitleSnapshot) {
            $jobTitle = $jobTitleSnapshot[$task->karyawan_id] ?? $task->karyawan->job_title;
            return array_search($jobTitle, $requiredRoles);
        });

        // format data task untuk tampilan
        $tasks = $sortedJobTitle->map(function ($task) use ($jobTitleSnapshot) {
            $costPerHour = $task->project->getKaryawanCost($task->karyawan_id);
            $hours = $task->workLogs->sum('hours');
            $totalCost = $hours * $costPerHour;
            $days = $task->workLogs
                ->pluck('work_date')
                ->unique()
                ->filter(fn($d) => !in_array(Carbon::parse($d)->dayOfWeek, [0, 6]))
                ->count();

            $jobTitle = $jobTitleSnapshot[$task->karyawan_id] ?? $task->karyawan->job_title;

            return [
                'karyawan' => $task->karyawan->name,
                'job_title' => $jobTitle,
                'catatan' => $task->catatan,
                'status' => $task->status,
                'progress' => $task->progress,
                'hours' => $hours,
                'days' => $days,
                'costPerHour' => $costPerHour,
                'totalCost' => $totalCost,
            ];
        });

        // hitung total progres dan biaya
        $totalProgress = round($project->tasks->avg('progress') ?? 0);
        $grandTotal = $this->calculateTotalCost($project);
        $durationDays = $this->getDurationDays($project);

        // sync total cost ke database
        $this->updateTotalCost($project);
        // $grandTotal = $project->tasks->sum(fn($t) => $t->workLogs->sum('hours') * $project->getKaryawanCost($t->karyawan_id));


        // if((float) $project->total_cost !== (float) $grandTotal){
        //     $project->update(['total_cost' => $grandTotal]);
        // }


        return compact('project', 'tasks', 'totalProgress', 'grandTotal', 'durationDays');
    }

    /**
     * Summary of update
     * update project + snapshot + task + karyawan
     * @param Project $project
     * @param array $data
     */
    public function update(Project $project, array $data)
    {
        return DB::transaction(function () use ($project, $data) {

            // update data project
            $this->repository->update($project, [
                'client_id' => $data['client_id'],
                'approved_by' => Auth::id(),
                'is_approved' => true,
                'status' => 'ongoing',
                'start_date_project' => $data['start_date_project'],
                'finish_date_project' => $data['finish_date_project'],
            ]);

            // update data project request
            $project->projectRequest->update([
                'name_project' => $data['name_project'],
                'description' => $data['description'],
                'client_id' => $data['client_id'],
            ]);

            $oldKaryawanIds = $project->tasks()->pluck('karyawan_id')->toArray();
            $newKaryawanIds = collect($data['karyawan_ids'])->diff($oldKaryawanIds);

            // sync karyawan + snapshot pivot
            $syncData = [];
            foreach ($data['karyawan_ids'] as $id) {
                $pivotData = $project->karyawans()->where('karyawan_id', $id)->first();
                $karyawan = Karyawan::findOrFail($id);
                // $pivotCost = $pivotData->cost_snapshot ?? $karyawan->cost;
                // $pivotJobTitle = $pivotData->job_title_snapshot ?? $karyawan->job_title; 
                $syncData[$id] = [
                    'cost_snapshot' => $pivotData?->pivot->cost_snapshot ?? $karyawan->cost,
                    'job_title_snapshot' => $pivotData?->pivot->job_title_snapshot ?? $karyawan->job_title,
                ];
            }
            $project->karyawans()->sync($syncData);

            // tambahkan task  untuk karyawan baru
            foreach ($newKaryawanIds as $id) {
                $karyawan = Karyawan::findOrFail($id);
                $project->tasks()->create([
                    'karyawan_id' => $id,
                    'catatan' => '-',
                    'status' => 'pending',
                    'progress' => 0,
                ]);
            }

            // hapus task karyawan 
            $removedKaryawanIds = collect($oldKaryawanIds)->diff($data['karyawan_ids']);
            if ($removedKaryawanIds->isNotEmpty()) {
                $project->tasks()
                    ->whereIn('karyawan_id', $removedKaryawanIds)
                    ->where('status', 'pending')
                    ->delete();
            }
            return $project->refresh()->load(['client', 'projectRequest', 'karyawans']);
        });
    }

    /**
     * Summary of delete
     * hapus project + pivot dan task juga ikut terhapus
     * @param Project $project
     */
    public function delete(Project $project)
    {
        return DB::transaction(fn() => $this->repository->delete($project));
    }

    /**
     * Summary of calculateTotalCost
     * hitung total cost berdasarkan workLogs * cost_snapshot
     * @param Project $project
     * @return float|int|mixed
     */
    public function calculateTotalCost(Project $project): float
    {
        return $project->tasks()
            ->with(['workLogs.karyawan'])
            ->get()
            ->sum(function ($task) use ($project) {
                $hours = $task->workLogs->sum('hours');
                $rate = $project->getKaryawanCost($task->karyawan_id);
                return $hours * $rate;
            });
    }

    /**
     * Summary of updateTotalCost
     * update total_cost di database
     * @param Project $project
     * @return void
     */
    public function updateTotalCost(Project $project): void
    {
        $total = $this->calculateTotalCost($project);
        if ((float) $project->total_cost !== (float) $total) {
            $project->update(['total_cost' => $total]);
        }
    }

    /**
     * Summary of updateStatus
     * update status project
     * @param Project $project
     * @return void
     */
    public function updateStatus(Project $project)
    {
        $unfinished = $project->tasks()->where('status', '!=', 'complete')->exists();
        $today = now()->toDateString();

        if (!$unfinished) {
            $project->update([
                'status' => 'complete',
                'finish_date_project' => now(),
            ]);
        } elseif ($project->finish_date_project && $today > $project->finish_date_project) {
            $project->update(['status' => 'overdue']);
        } else {
            $project->update(['status' => 'ongoing']);
        }
    }

    /**
     * Summary of getDurationDays
     * hitung durasi hari kerja
     * @param Project $project
     * @return float|int
     */
    public function getDurationDays(Project $project)
    {
        if (!$project->start_date_project)
            return 0;

        $start = Carbon::parse($project->start_date_project);
        $end = $project->finish_date_project ? Carbon::parse($project->finish_date_project) : now();

        return max(1, ceil($start->diffInHours($end) / 24));
    }
}