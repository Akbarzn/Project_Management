<?php

namespace App\Repositories\Contracts;

use App\Models\ProjectRequest;

interface ProjectRequestRepositoryInterface
{
    public function getAllWithFilter(?string $search, ?string $status = null, ?int $clientId = null);
    public function findWithRelations(int $id): ?ProjectRequest;
    public function find(int $id): ProjectRequest;
    public function getLastTicketLike(string $pattern): ?ProjectRequest;
    public function create(array $data);
    public function update(ProjectRequest $projectRequest, array $data): ProjectRequest;
    public function delete(ProjectRequest $projectRequest): bool;
}
