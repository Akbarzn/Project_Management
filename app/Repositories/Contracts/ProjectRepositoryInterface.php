<?php

namespace App\Repositories\Contracts;

use App\Models\Project;

interface ProjectRepositoryInterface{
    public function getAllWithFilter(?string $search = null);
    public function findWithRelations(int $id): Project;
    public function create(array $data);
    public function update(Project $project, array $data);
    public function delete(Project $project): bool;
}