<?php 

namespace App\Repositories\Contracts;

use App\Models\Task;

interface TaskRepositoryInterface extends BaseRepositoryInterface{
    public function getAllForManager(array $filters = []): mixed;
    public function getAllForKaryawan(int $karyawanId, array $filters = []): mixed;
}