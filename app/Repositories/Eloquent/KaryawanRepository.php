<?php 

namespace App\Repositories\Eloquent;

use App\Models\Karyawan;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class KaryawanRepository extends BaseRepository implements KaryawanRepositoryInterface{

    // contruct utk inject model karyawan ke baserepository
    public function __construct(Karyawan $model){
        parent::__construct($model);
    }

    public function getAllKaryawan(?string $search = null)
    {
        $query = $this->model->with('user');
        if(!empty($search)){
            $query->where(function ($q) use ($search){
                $q->where('name','like','%'.$search.'%')
                ->orWhere('nik','like','%'.$search.'%')
                ->orWhere('job_title','like','%'.$search.'%')
                ->orWhere('jabatan','like','%'.$search.'%')
                ->orWhereHas('user', function ($u) use ($search){
                    $u->where('email','like','%'.$search.'%');
                });
            });
        }
        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    // ambil data berdasarkan Id
    public function findById(int $id, $relations = []): ?Karyawan{
        return parent::findById($id, $relations);
    }

    public function update(Model $karyawan, array $data): Karyawan{
        return parent::update($karyawan, $data);
    }

    public function delete(Model $karyawan): bool{
        return parent::delete($karyawan);
    }
} 