<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface{
    public function __construct(Project $model){
        parent::__construct($model);
    }

    public function getAllWithFilter(?string $search = null){
        $query = $this->model->with(['client', 'projectRequest', 'karyawans', 'approver']);
        if($search){
            $query->where(function ($q) use ($search){
                $q->where('status', 'like', "%$search%")
                ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%$search%"))
                ->orWhereHas('projectRequest', fn($p) => $p->where('name_project', 'like', "%$search%"));
            });
        }

        return $query->latest()->paginate(10);
    }

    public function findWithRelations(int $id): Project{
        return $this->model->with(['client', 'projectRequest', 'karyawans', 'tasks.karyawan'])->findOrFail($id);
    }

    public function createProject(array $data) :Project{
        // return $this->create($data);
        return parent::create($data);
    }

    public function updateProject(Project $project, array $data): Project{
        return $this->update($project, $data);
    }

    public function deleteProject(Project $project): bool{
        return $this->delete($project);
    }
}