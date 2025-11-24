<?php

namespace App\Services;

use App\Models\{Task, Project, TaskLog, TaskWorkLog};
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService
{
    protected ProjectService $projectService;
    public function __construct(
        protected TaskRepositoryInterface $repository,
        ProjectService $projectService
        )
    {
        $this->projectService = $projectService;
    }

    public function listTask(array $filters = []): mixed
    {
        $user = Auth::user();

        return match (true) {
            $user->hasRole('manager') => $this->repository->getAllForManager($filters),
            $user->hasRole('karyawan') => $this->repository->getAllForKaryawan($user->karyawan->id, $filters),
            default => abort(403, 'Unauthorized role'),
        };
    }


    public function update(Task $task, array $data): Task
    {
        // dd($data);
        $karyawan = Auth::user()->karyawan;
         abort_if(!$karyawan || $task->karyawan_id !== $karyawan->id, 403, 'Anda tidak memiliki akses ke task ini.');

         
         
         return DB::transaction(function () use ($task, $data, $karyawan) {
           
            // simpan data lama untuk keperluan log
            $originalData = $task->only(['progress', 'catatan', 'start_date_task', 'finish_date_task']);
           
            $today = now()->toDateString();
            $isFirstUpdate = $task->status === 'pending';

            // update progres dan statu
            $this->applyProgressUpdate($task, $data);

            // simpan jam kerja
            if(!empty($data['hours'])){
                $this->logWorkHours($task,$karyawan->id, $today, (float) $data['hours'], $isFirstUpdate);
            }
            // simpan di taskLog

            $this->logChanges($task, $data, $originalData);
            
            // update total biaya + status project
            if($task->project){
                $this->projectService->updateTotalCost($task->project);
                $this->projectService->updateStatus($task->project);
            }

            return $task->fresh(['workLogs', 'karyawan', 'project']);
        });
    }
    

    /**
     * Summary of applyProgressUpdate
     * atur update progress + status task
     * @param Task $task
     * @param array $data
     * @return void
     */
    private function applyProgressUpdate(Task $task, array $data): void{
        $progress = $data['progress'] ?? $task->progress;
        $catatan = $data['catatan'] ?? $task->catatan;

        $task->fill([
            'progress' => $progress,
            'catatan' => $catatan,
            'start_date_task' => $data['start_date_task'] ?? $task->start_date_task ,
            'finish_date_task' => $data['finish_date_task'] ?? $task->finish_date_task ,
        ]);
    
        // tentukna status berdasarkan progress
        if ($progress >= 100) {
            $task->status = 'complete';
            $task->finish_date_task = now();
        } elseif ($progress > 0 && $progress < 100) {
            $task->status = 'inwork';
            if(!$task->start_date_task){
                $task->start_date_task = now();
            }
        }else{
            $task->status = 'pending';
        }
        
        $task->save();

    }

    /**
     * Summary of logWorkHours
     * simpan jam kerja task per tanggal atauh hari 
     * maks 7 jam kerja per hari
     * @param Task $task
     * @param int $karyawanId
     * @param string $today
     * @param float $hours
     * @param bool $firstUpdate
     * @return void
     */
    private function logWorkHours(Task $task,int $karyawanId, string $today, float $hours, bool $firstUpdate ): void{
      
        //tentukan batas jam kerja per hari
       $endOfWorkTime = now()->setTime(24,0);

    //   jika waktu skrng sudah melewati jam kerja maka akan ditolak
    abort_if(
        now()->greaterThan($endOfWorkTime),
        400,
        'Update jam kerja sudah melewati batas (24:00).'
    );
       
        // hitung total sisa jam kerja
        $usedHours = TaskWorkLog::where('karyawan_id', $karyawanId)
        ->whereDate('work_date', $today)
        ->sum('hours');

        $remainingHours = 7 - $usedHours;

        abort_if($remainingHours <= 0, 400,'Batas 7 jam kerja sudah tercapai hari ini.');
        abort_if($hours > $remainingHours, 400, "Jam kerja melebihi sisa {$remainingHours} jam hari ini.");

        // simpan atau update jam kerja
        TaskWorkLog::updateOrCreate([
            'task_id' => $task->id,
            'karyawan_id' => $karyawanId,
            'work_date' => $today,
        ],
        ['hours' => $hours]);

        // kalo pertama update ubah status dan start date
        if($firstUpdate){
            $task->update([
                'status' => 'inwork',
                'start_date_task' => now(),
            ]);
        }
    }

    /**
     * Summary of logChanges
     * simpan perubahan field ke tabel task_logs
     * @param Task $task
     * @param array $data
     * @param array $originalData
     * @return void
     */
    protected function logChanges(Task $task, array $data, array $originalData): void{
        // dd('logChanges dipanggil', $data);
        $logFields = ['progress', 'catatan', 'start_date_task', 'finish_date_task'];
        foreach($logFields as $field){
            if(isset($data[$field])){
                $old = (string) ($originalData[$field] ?? '');
                $new = (string) ($data[$field]);

                if($old !== $new){
                   $log = TaskLog::create([
                        'task_id' => $task->id,
                        'field' => $field,
                        'old_value' => $old,
                        'new_value' => $new,
                        'updated_by' => Auth::id(),
                    ]);
                    //  dd('LOG BERHASIL DIBUAT:', $log->toArray());
                }
            }
        }
    }

    public function delete(Task $task): bool
    {
        return $this->repository->delete($task);
    }
}