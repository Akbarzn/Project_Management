<?php

namespace App\Services;

use App\Models\TaskLog;
use App\Repositories\Contracts\TaskLogRepositoryInterface; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Collection; 

class TaskLogService
{
    public function __construct(protected TaskLogRepositoryInterface $repository)
    {
    }

    /**
     * Summary of listLogs
     * ambil semua log dari suatu task lalu di kelompokkan berdasarkan waktu update
     * @param int $taskId
     * @return Collection
     */
    public function listLogs(int $taskId): Collection
    {
        // Ambil semua log dari repository berdasarkan ID task
        $logs = $this->repository->getByTaskId($taskId);

        // kelompokkan log berdasarkan waktu created_at
        return $logs->groupBy(fn($log) => $log->created_at->format('Y-m-d H:i:s'))
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'time' => $first->created_at, 
                    'user' => $first->user->name ?? 'System', 
                    'fields' => $group->pluck('field'), 
                    'old_values' => $group->pluck('old_value'),
                    'new_values' => $group->pluck('new_value'),
                    'id' => $first->id, 
                ];
            });
    }
}
