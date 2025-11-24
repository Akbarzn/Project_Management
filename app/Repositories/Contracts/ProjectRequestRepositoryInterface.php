<?php

namespace App\Repositories\Contracts;

use App\Models\ProjectRequest;

interface ProjectRequestRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Summary of getAllWithFilter
     * ambil semua project request dgn filter 
     * @param mixed $search
     * @param mixed $status
     * @param mixed $clientId
     * @return mixed
     */
    public function getAllWithFilter(?string $search, ?string $status = null, ?int $clientId = null);
    
    /**
     * Summary of findWithRelations
     * ambil satu project request + relasinya
     * @param int $id
     * @return ?ProjectRequest
     */
    public function findWithRelations(int $id): ?ProjectRequest;
    
    /**
     * Summary of getLastTicketLike
     * ambil project request terakgir berdasarkan nomor tiket
     * @param string $pattern
     * @return ?ProjectRequest
     */
    public function getLastTicketLike(string $pattern): ?ProjectRequest;
    // public function create(array $data);
    // public function update(ProjectRequest $projectRequest, array $data): ProjectRequest;
    // public function delete(ProjectRequest $projectRequest): bool;
}
