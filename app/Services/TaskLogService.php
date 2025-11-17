<?php

namespace App\Services;

// Import class dan helper yang dibutuhkan
use App\Repositories\Contracts\TaskLogRepositoryInterface; // Interface repository untuk mengakses data log
use Illuminate\Support\Facades\Auth; // Untuk ambil user yang sedang login
use Illuminate\Support\Collection; // Tipe data koleksi Laravel (mirip array tapi lebih powerful)

class TaskLogService
{
    // Constructor, pakai dependency injection agar repository otomatis diisi saat class dibuat
    public function __construct(protected TaskLogRepositoryInterface $repository)
    {
        // protected artinya bisa diakses oleh class ini dan turunannya (lebih aman dari public)
    }

    /**
     * Ambil semua log berdasarkan ID task, lalu kelompokkan berdasarkan waktu perubahan.
     */
    public function listLogs(int $taskId): Collection
    {
        // Ambil semua log dari repository berdasarkan ID task
        $logs = $this->repository->getByTaskId($taskId);

        // Group log berdasarkan waktu perubahan (created_at)
        // Jadi semua log yang dibuat di waktu yang sama dikelompokkan bareng
        return $logs->groupBy(fn($log) => $log->created_at->format('Y-m-d H:i:s'))
            ->map(function ($group) {
                // Ambil log pertama di grup ini (semua di waktu sama)
                $first = $group->first();

                // Ubah bentuk data supaya lebih enak ditampilkan di view
                return [
                    'time' => $first->created_at, // waktu perubahan
                    'user' => $first->user->name ?? 'System', // siapa yang ubah (kalau null, tampil "System")
                    'fields' => $group->pluck('field'), // kolom apa saja yang berubah
                    'old_values' => $group->pluck('old_value'), // nilai sebelum diubah
                    'new_values' => $group->pluck('new_value'), // nilai sesudah diubah
                    'id' => $first->id, // ambil id log pertama sebagai identifier
                ];
            });
    }

    /**
     * Hapus satu log berdasarkan ID log-nya.
     * Hanya karyawan pemilik task yang boleh menghapus log-nya sendiri.
     */
    public function deleteLog(int $id): bool
    {
        // Cari log berdasarkan ID, kalau tidak ketemu bakal error 404 otomatis
        $log = \App\Models\TaskLog::findOrFail($id);

        // Ambil task yang berhubungan dengan log ini
        $task = $log->task;

        // Pastikan karyawan yang login adalah pemilik task ini
        // Kalau bukan, langsung abort dengan error 403 (tidak diizinkan)
        if (Auth::user()->karyawan->id !== $task->karyawan_id) {
            abort(403, 'Anda tidak memiliki izin menghapus log ini.');
        }

        // Kalau lolos validasi, hapus log lewat repository
        return $this->repository->delete($id);
    }
}
