<?php 

namespace App\Repositories\Eloquent;

use App\Models\{Task, Karyawan};
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface{
    // protected Task $model;

    public function __construct(Task $model){
        parent::__construct($model);
    }

    public function query(){
        return $this->model->newQuery();
    }
    
    public function getAllForManager(array $filters = []) :mixed{
        $query = $this->model->with(['karyawan.user', 'project.projectRequest']);

        if(!empty($filters['project_id'])){
            $query->where('project_id', $filters['project_id']);
        }

        if(!empty($filters['karyawan_id'])){
            $query->where('karyawan_id', $filters['karyawan_id']);
        }

        if(!empty($filters['status'])){
            $query->where('status', $filters['status']);
        }

        if(!empty($filters['search'])){
            $search = $filters['search'];
            $query->where(function ($q) use ($search){
                $q->whereHas('project.projectRequest', fn($q2) => $q2->where('name_project', 'like', "%$search%"))
                ->orWhereHas('karyawan.user', fn($q3) => $q3->where('name', 'like', "%$search%"));
            });
        }

        return $query->latest()->paginate(10);
    }

    public function getAllForKaryawan(int $karyawanId, array $filters = []): mixed{
        $query = $this->model->with(['project.client', 'project.projectRequest'])
        ->where('karyawan_id', $karyawanId);

         if(!empty($filters['search'])){
            $search = $filters['search'];
            $query->where(function ($q) use ($search){
                $q->whereHas('project.projectRequest', fn($q2) => $q2->where('name_project', 'like', "%$search%"))
                ->orWhereHas('project.client.user', fn($q3) => $q3->where('name', 'like', "%$search%"));
            });
        }

        if(!empty($filters['project_id'])){
            $query->where('project_id', $filters['project_id']);
        }
        return $query->orderBy('updated_at', 'desc')->paginate(10);
    }

    public function findById(int $id, array $relations = []): ?Task{
        return parent::findById($id,['project', 'karyawan', 'workLogs','logs']);
    }


    public function updateTask(Task $task, array $data): Task{
        return parent::update($task, $data);
    }

    public function deleteTask(Task $task): bool{
        return parent::delete($task);
    }
}