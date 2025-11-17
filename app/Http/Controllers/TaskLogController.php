<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskLog;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use App\Services\TaskLogService;
use Illuminate\Support\Facades\Auth;

class TaskLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected TaskLogService $taskLogService;
    // Constructor: supaya service bisa dipakai di seluruh fungsi
    public function __construct(TaskLogService $taskLogService)
    {
        $this->taskLogService = $taskLogService;
    }

    /**
     * Tampilkan halaman log untuk 1 task
     */
    public function show(Task $task)
    {
        // Validasi bahwa task ini milik karyawan yang login
        if (Auth::user()->hasRole('karyawan') && $task->karyawan_id !== Auth::user()->karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke task ini.');
        }

        // Ambil log lewat service (sudah dalam format siap tampil)
        $logs = $this->taskLogService->listLogs($task->id);

        // Tampilkan view log
        return view('karyawans.tasks.logs', compact('task', 'logs'));
    }

    /**
     * Hapus satu log tertentu (hanya oleh pemilik task)
     */
    public function destroyLog($id)
    {
        $this->taskLogService->deleteLog($id);

        return back()->with('success', 'Riwayat log berhasil dihapus.');
    }
}
