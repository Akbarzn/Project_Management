<?php

namespace App\Repositories\Eloquent;

use App\Models\ProjectRequest;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRequestRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Eloquent\BaseRepository;

class ProjectRequestRepository extends BaseRepository implements ProjectRequestRepositoryInterface
{
    public function __construct(ProjectRequest $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(?string $search = null,?string $status = null, ?int $clientId = null)
    {
        $query = $this->model->with('client', 'projects');
    
        if ($status) {
            $query->where('status', $status);
        }

        if ($clientId) {
            $query->where('client_id', $clientId);
        }


         if($search){
            $query->where(function ($q) use ($search){
                $q->where('name_project', 'like', "%$search%")
                ->orWhere('kategori', 'like', "%$search%")
                ->orWhereHas('client', function ($qc) use ($search) {
                  $qc->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('projects', function ($qp) use ($search) {
                  $qp->where('status', 'like', "%{$search}%");
              });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function findWithRelations(int $id): ?ProjectRequest
    {
        return parent::findById($id, ['client', 'projects']);
    }

    public function find(int $id): ProjectRequest
    {
        return $this->model->findOrFail($id);
    }

    public function getLastTicketLike(string $pattern): ?ProjectRequest
    {
        return $this->model
            ->where('tiket', 'like', $pattern . '%')
            ->orderBy('tiket', 'desc')
            ->first();
    }

    public function update(Model $projectRequest, array $data): ProjectRequest
    {
        return parent::update($projectRequest, $data);
    }

    public function delete(Model $projectRequest): bool
    {
        return parent::delete($projectRequest);
    }
}
