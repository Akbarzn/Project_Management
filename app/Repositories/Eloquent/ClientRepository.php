<?php

namespace App\Repositories\Eloquent;

use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{

    public function __construct(Client $model){
        parent::__construct($model);
    }
    public function getAllClient(?string $search = null):mixed{
        $query  = $this->model->with('user');
        if(!empty($search)){
            $query->where(function ($q) use($search){
                $q->where('name', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%")
                ->orWhere('kode_organisasi', 'like', "%$search%")
                ->orWhereHas('user', function ($u) use($search){
                    $u->where('email','like', "%$search%");
                });
            });
        }

        return $query->orderBy("created_at","desc")->paginate(10);
    }

    public function findById(int $id, $relations = []): ?Client{
        return parent::findById($id, $relations);
    }

    public function create(array $data): Client{
        return parent::create($data);
    }
    
    // update dgn signatur model agar sama dgn yg ada di baseRepository
    public function update(Model $model, array $data): Model
    {
        return parent::update($model, $data);
    }
    // delete dgn signatur model agar sama dgn yg ada di baseRepository

    public function delete(Model $model): bool
    {
        return parent::delete($model);
    }

}