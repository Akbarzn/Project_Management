<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskLog;
use App\Models\Task;

class TaskLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
          $logs = TaskLog::with(['task', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.task_logs.index', compact('logs'));
    
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
          $logs = TaskLog::with('user')
            ->where('task_id', $task->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('karyawans.tasks.logs', compact('task', 'logs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
