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
    /**
     * Summary of taskLogService
     * simpan taskLog service ke property
     * @var TaskLogService
     */
    protected TaskLogService $taskLogService;

    public function __construct(TaskLogService $taskLogService)
    {
        $this->taskLogService = $taskLogService;
    }

    /**
     * nampilin log/task history untuk satu task
     * karyawan cuman boleh liat log miliknya sendiri
     */
    public function show(Task $task)
    {
        // cek bahwa task ini milik karyawan yang login
        if (Auth::user()->hasRole('karyawan') && $task->karyawan_id !== Auth::user()->karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke task ini.');
        }

        // ambil log lewat service 
        $logs = $this->taskLogService->listLogs($task->id);

        return view('karyawans.tasks.logs', compact('task', 'logs'));
    }

    /**
     * Hapus satu log tertentu 
     * cuman bisa dilakukan sama pemilik task
     */
    // public function destroyLog($id)
    // {
    //     $this->taskLogService->deleteLog($id);

    //     return back()->with('success', 'Riwayat log berhasil dihapus.');
    // }
}
