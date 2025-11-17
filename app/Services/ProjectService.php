<?php 

namespace App\Services;

use App\Models\ProjectRequest;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Models\Project;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;


class ProjectService{
    protected ProjectRepositoryInterface $repository;

    public function __construct(ProjectRepositoryInterface $repository){
        $this->repository = $repository;
    }

    private function getRequiredRoles(){
        return [
            'Analisis Proses Bisnis',
            'Database Functional',
            'Programmer',
            'Quality Test',
            'SysAdmin',
        ];
    }

    public function listProjects(?string $search = null){
        return $this->repository->getAllWithFilter($search);
    }

    public function getCreateData($requestId = null){
        $request = $requestId ? ProjectRequest::findOrFail($requestId) : null;
        return [
            'projectRequest' => $request,
            'pending_request' => ProjectRequest::with('client')
            ->where('status', 'pending')
            ->get(['id', 'name_project', 'client_id']),
            'karyawans' => Karyawan::all(['id', 'name', 'job_title', 'cost']),
            'requiredRoles' => $this->getRequiredRoles(),
        ];
    }

    public function getEditData(Project $project){
        $project->load(['client', 'projectRequest', 'karyawans']);
        return [
            'project' => $project,
            'karyawans' => Karyawan::all(['id', 'name', 'job_title', 'cost']),
            'selectedKaryawanIds' => $project->karyawans->pluck('id')->toArray(),
            'requiredRoles' => $this->getRequiredRoles(),
        ];
    }

    public function create(array $data){
        return DB::transaction(function () use ($data){
            $projectRequest = ProjectRequest::findOrFail($data['request_id']);
            
            $project = $this->repository->create([
                'client_id' => $projectRequest->client_id,
                'request_id' => $projectRequest->id,
                'start_date_project' => $data['start_date_project'],
                'finish_date_project' => $data['finish_date_project'],
                'status' => 'ongoing',
                'created_by' => Auth::id(),
                'approved_by' => Auth::id(),
                'is_approved' => true,
                'total_cost' => 0,
            ]);

            $syncData = [];
            foreach($data['karyawan_ids'] as $id){
                $karyawan = Karyawan::findOrFail($id);
                $syncData[$id] = ['cost_snapshot' => $karyawan->cost];
            }
            $project->karyawans()->attach($syncData);

            foreach($data['karyawan_ids'] as $id){
                $karyawan = Karyawan::findOrFail($id);
                $project->tasks()->create([
                    'karyawan_id' => $id,
                    'catatan' => '-',
                    'status' => 'pending',
                    'progress' => 0,
                ]);
            }

            $projectRequest->update(['status' => 'approve']);

            return $project->load(['client', 'projectRequest', 'karyawans']);
        });
    }

    public function showProject(int $id): array{
        // ambil data projcet
        $project = $this->repository->findWithRelations($id);

        $tasks = $project->tasks->map(function ($task){
            $costPerHour = $task->project->getKaryawanCost($task->karyawan_id);
            $hours = $task->workLogs->sum('hours');
            $totalCost = $hours * $costPerHour;
            $days = $task->workLogs
            ->pluck('work_date')
            ->unique()
            ->filter(fn($d) => !in_array(Carbon::parse($d)->dayOfWeek, [0,6]))
            ->count();

            return[
                'karyawan' => $task->karyawan->name,
                'job_title' => $task->karyawan->job_title,
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


        return compact('project', 'tasks', 'totalProgress', 'grandTotal','durationDays');
    }

    public function update(Project $project, array $data){
        return DB::transaction (function () use ($project, $data){
            $this->repository->update($project,[
                'approved_by' =>Auth::id(),
                'is_approved' =>true,
                'status' => 'ongoing',
                'start_date_project' => $data['start_date_project'],
                'finish_date_project' => $data['finish_date_project'],
            ]);
            $project->projectRequest->update(['name_project' => $data['name_project']]);

            $oldKaryawanIds = $project->tasks()->pluck('karyawan_id')->toArray();
            $newKaryawanIds = collect($data['karyawan_ids'])->diff($oldKaryawanIds);

            $syncData = [];
            foreach($data['karyawan_ids'] as $id){
                $karyawan  = Karyawan::findOrFail($id);
                $pivotCost = $project->karyawans()->where('karyawan_id', $id)->first()?->pivot->cost_snapshot ?? $karyawan->cost;
                $syncData[$id] = ['cost_snapshot' => $pivotCost];
            }
            $project->karyawans()->sync($syncData);

            foreach($newKaryawanIds as $id){
                $karyawan = Karyawan::findOrFail($id);
                $project->tasks()->create([
                    'karyawan_id' =>$id,
                    'catatan' => '-',
                    'status' => 'pending',
                    'progress' => 0,
                ]);
            }

            $removedKaryawanIds = collect($oldKaryawanIds)->diff($data['karyawan_ids']);
            if($removedKaryawanIds->isNotEmpty()){
                $project->task()
                ->whereIn('karyawan_id', $removedKaryawanIds)
                ->where('status', 'pending')
                ->delete();
            }
            return $project->refresh()->load(['client', 'projectRequest', 'karyawans']);
        });
    }

    public function delete(Project $project){
        return DB::transaction(fn() => $this->repository->delete($project));
    }

    // hitung totalCOst
    public function calculateTotalCost(Project $project): float{
        return $project->tasks()
        ->with(['workLogs.karyawan'])
        ->get()
        ->sum(function ($task) use ($project){
            $hours = $task->workLogs->sum('hours');
            $rate = $project->getKaryawanCost($task->karyawan_id);
            return $hours * $rate;
        });
    }

    // update totalCost di database
    public function updateTotalCost(Project $project): void{
        $total = $this->calculateTotalCost($project);
        if((float) $project->total_cost !== (float) $total){
            $project->update(['total_cost' => $total]);
        }
    }

    // update status project
    public function updateStatus(Project $project){
        $unfinished = $project->tasks()->where('status', '!=', 'complete')->exists();
        $today = now()->toDateString();

        if(!$unfinished){
            $project->update([
                'status' => 'complete',
                'finish_date_project' => now(),
            ]);
        }elseif($project->finish_date_project && $today > $project->finish_date_project){
            $project->update(['status' => 'overdue']);
        }else{
            $project->update(['status'=> 'ongoing']);
        }
    }

    // hitung hari kerja
    public function getDurationDays(Project $project){
        if(!$project->start_date_project) return 0;
        
        $start = Carbon::parse($project->start_date_project);
        $end = $project->finish_date_project ? Carbon::parse($project->finish_date_project) : now();

        return max(1, ceil($start->diffInHours($end) /24));
    }
}