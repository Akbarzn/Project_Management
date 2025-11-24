<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Models\Project;

interface ProjectRepositoryInterface extends BaseRepositoryInterface{
    public function getAllWithFilter(?string $search = null);
   
    /**
     * Summary of findWithRelations
     * nampilin detail project
     * tidak pake findById karena butuh relasi lengkap
     * @param int $id
     * @return Project
     */
    public function findWithRelations(int $id): Project;
}