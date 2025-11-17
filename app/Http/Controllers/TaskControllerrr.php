<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Project;
use App\Models\TaskLog;
use App\Models\TaskWorkLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskControllerrr extends Controller
{

    public function index(Request $request)
    {
        $karyawan = Auth::user()->karyawan;

        $query = Task::with(['project.client', 'project.projectRequest'])
            ->where('karyawan_id', $karyawan->id);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->orderBy('updated_at', 'desc')->get();

        $projects = $karyawan->projects()
            ->with('projectRequest') 
            ->get()
            ->mapWithKeys(function ($project) {
                $name = $project->projectRequest->name_project ?? optional($project->projectRequest)->name_project ?? 'Tanpa Nama';
                return [$project->id => $name];
            });
        // dd($request->all());
        return view('karyawans.tasks.index', compact('tasks', 'projects'));
    }

    public function create()
    {
        $projects = Project::whereHas('karyawans', function ($q) {
            $q->where('id', Auth::user()->karyawan->id);
        })->get();

        return view('karyawans.tasks.create', compact('projects'));
    }

    public function store(StoreTaskRequest $request)
    {
        $karyawan = Auth::user()->karyawan;

        Task::create([
            'project_id' => $request->project_id,
            'karyawan_id' => $karyawan->id,
            'task_name' => $karyawan->job_title, 
            'progress' => $request->progress ?? 0,
            'description_task' => $request->description_task, 
            'start_date_task' => $request->start_date_task,
            'finish_date_task' => $request->progress == 100 ? now() : null,
        ]);

        return redirect()->route('karyawan.tasks.index')
            ->with('success', 'Task baru berhasil ditambahkan!');
    }

    public function show(Task $task)
    {
        $this->authorizeAccess($task);
        return view('karyawans.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorizeAccess($task);
        return view('karyawans.tasks.edit', compact('task'));
    }

 
    public function update(UpdateTaskRequest $request, Task $task){
        $karyawan = Auth::user()->karyawan;

        // cegah akses task milik org lain
        abort_if(!$karyawan || $task->karyawan_id !== $karyawan->id, 403, 'Anda tidak memilki akses ke task ini');

        $project = null;

        DB::transaction(function () use ($request, $task, $karyawan){

            // simpan tgl hari ini
            $today = now()->toDateString();
            // ambil project terkait task
            $project = $task->project;

            // cek task
            $isFirstUpdate = $task->status === 'pending';

            // update prgres,deskripsi, status
            $this->applyTaskUpdate($task, $request);

            // cek apakah ada input jam kerja
            if($request->filled('work_hours')){
                $this->logWorkHours($task, $karyawan,$today, (float) $request->work_hours, $isFirstUpdate);
            }

            // panggilfungsi syncproject untuk hitung ulang cost
            $this->updateTotalCost($project);
        });

        if($task->project){
            $task->project->updateStatus();
        }
        return redirect()->route('karyawan.tasks.index')->with('success','Update Task Berhasil');
    }

    public function applyTaskUpdate(Task $task, Request $request){
        // ambil data progres baru kalo kosong pakai yang lama
        $progress = $request->progress ?? $task->progress;

        // isi ulang field task
        $task->fill([
            'progress' => $progress,
            'description_task' => $request->desc ?? $task->description_task
        ]);

        // match(true){
        //     $progress >= 100 && $task->status !== 'complete' => $this->markTaskComplete($task),
        //     $progress <= 100 && $task->status === 'complete' => $this->markTaskInwork($task),
        //     default => $task->status = $request->status ?? $task->status,
        // };

        if($progress >= 100){
            $this->markTaskComplete($task);
        }elseif($progress > 0 && $task->status === 'pending'){
            $this->markTaskInwork($task);
        }
        $task->save();
    }

    public function markTaskComplete(Task $task){
        $task->status = 'complete';
        $task->finish_date_task = now();
    }

    public function markTaskInwork(Task $task){
        $task->status = 'inwork';
        $task->finish_date_task = null;
    }

    private function logWorkHours(Task $task, $karyawan, string $today, float $hours, bool $firstUpdate){
        $sisaJamKerja = 7 - TaskWorkLog::where('karyawan_id', $karyawan->id)
        ->whereDate('work_date', $today)
        ->sum('hours');

        // validasi batas maksimal jam kerja
        abort_if($sisaJamKerja <= 0, 400, 'Batas 7 jam kerja tercapai hari ini.');
        abort_if($hours > $sisaJamKerja, 400, "Jam kerja melebihi sisa {$sisaJamKerja} hari ini");
        
        // update log jam kerja
        TaskWorkLog::updateOrCreate([
            'task_id' => $task->id,
            'karyawan_id' => $karyawan->id,
            'work_date' => $today,
        ],
        ['hours' => $hours]
    );
    if($firstUpdate)
        $task->update([
    'status' => 'inwork',
    'start_date_task' => now()
        ]);
     // Jika project belum punya tanggal mulai, isi juga
        // $task->project?->update([
        //     'start_date_project' => $task->project->start_date_project ?? now(),
        // ]);
    }

    private function updateTotalCost(Project $project = null){
        if(!$project) return;

        // hitung ulang total cost dari seluruh task dan log kerja
        // $totalCost = $project->tasks()
        // ->with(['workLogs.karyawan'])
        // ->get()
        // ->flatMap->workLogs
        // ->sum(fn($log) => ($log->karyawan->cost ?? 0) * $log->hours);

          $totalCost = $project->tasks()
        ->with(['workLogs.karyawan'])
        ->get()
        ->sum(function ($task) use ($project) {
            $totalHours = $task->workLogs->sum('hours');
            // gunakan cost snapshot dari pivot
            $snapshot = $project->getKaryawanCost($task->karyawan_id);
            return $totalHours * $snapshot;
        });

        $project->update(['total_cost' => $totalCost]);

          if ($project->tasks()->where('status', '!=', 'complete')->doesntExist()) {
        $project->update([
            'status' => 'complete',
            'finish_date_project' => now(),
        ]);
    }
    }


    public function destroy(Task $task)
    {
        $this->authorizeAccess($task);
        $task->delete();

        return redirect()->route('karyawan.tasks.index')
            ->with('success', 'Task berhasil dihapus!');
    }

    /**
     * Menampilkan history logs untuk task tertentu
     */
    public function logs(Task $task)
    {
        $this->authorizeAccess($task); 
        $logs = $task->logs()->with('user')->orderBy('created_at', 'desc')->get();

        return view('karyawans.tasks.logs', compact('task', 'logs'));
    }

    public function destroyLog($id)
    {
        $taskLog = TaskLog::findOrFail($id);
        $this->authorizeAccess($taskLog->task); 
        
        $taskLog->delete();
        return back()->with('success', 'Riwayat berhasil dihapus.');
    }

    private function authorizeAccess(Task $task)
    {
        if ($task->karyawan_id !== Auth::user()->karyawan->id) {
            abort(403, 'Anda tidak memiliki akses ke task ini.');
        }
    }
}