<?php 

namespace App\Repositories\Contracts;

use App\Models\Task;

interface TaskRepositoryInterface{
    public function getAllForManager(array $filters = []): mixed;
    public function getAllForKaryawan(int $karyawanId, array $filters = []): mixed;
    public function findById(int $id): ?Task;
    public function createTask(array $data): Task;
    public function updateTask(Task $task, array $data): Task;
    public function delete(Task $task): bool;
}