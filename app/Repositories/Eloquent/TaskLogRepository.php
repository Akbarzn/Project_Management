<?php

namespace App\Repositories\Eloquent;

use App\Models\TaskLog;
use App\Repositories\Contracts\TaskLogRepositoryInterface;

class TaskLogRepository implements TaskLogRepositoryInterface
{
    public function getByTaskId(int $taskId)
    {
        return TaskLog::with('user')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function delete(int $id): bool
    {
        return TaskLog::findOrFail($id)->delete();
    }
}
