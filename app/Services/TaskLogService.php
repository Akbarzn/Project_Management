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

    /**
     * Summary of deleteLog
     * hapus log berdasarka id 
     * hanya karyawan yang punya taska miliknya yang bisa dihapus
     * @param int $id
     * @return bool
     */
    public function deleteLog(int $id): bool
    {
        // Cari log berdasarkan ID, kalau tidak ketemu bakal error 404 otomatis
        $log = TaskLog::findOrFail($id);

        // Ambil task yang berhubungan dengan log ini
        $task = $log->task;

        // cek karyawan yang login adalah pemilik task ini
        // Kalau bukan, langsung abort dengan error 403 (tidak diizinkan)
        if (Auth::user()->karyawan->id !== $task->karyawan_id) {
            abort(403, 'Anda tidak memiliki izin menghapus log ini.');
        }

        // Kalau lolos validasi, hapus log lewat repository
        return $this->repository->delete($id);
    }
}
