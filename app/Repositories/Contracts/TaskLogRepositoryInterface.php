<?php

namespace App\Repositories\Contracts;

use App\models\TaskLog;

interface TaskLogRepositoryInterface{
    public function getByTaskId(int $taskId);
    public function delete(int $id): bool;
}