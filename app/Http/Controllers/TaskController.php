<?php 

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\{Task, Project, Karyawan};
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;

/**
 * @method void middleware($middleware)
 */
class TaskController extends Controller{

    // use AuthorizesRequests;
 public function __construct(protected TaskService $service){
    // $this->authorizeResource(Task::class, 'task');
    // $this->middleware('auth');
    // $this->middleware('role:karyawan');
 }   

 public function index(Request $request): View{
    $tasks = $this->service->listTask($request->all());
    $user = Auth::user();

    $projects = $user->hasRole('manager')
    ?Project::with('projectRequest')->get()
    :$user->karyawan->projects()->with('projectRequest')->get();

    $karyawans = $user->hasRole('manager') 
    ? Karyawan::with('user')->get()
    : collect();

    $view = $user->hasRole('manager')
    ?'manager.tasks.index'
    :'karyawans.tasks.index';

    return view($view, compact('tasks', 'projects', 'karyawans'));
 }

  public function create(): View
    {
        $projects = Project::whereHas('karyawans', fn($q) =>
            $q->where('id', Auth::user()->karyawan->id)
        )->get();

        return view('karyawans.tasks.create', compact('projects'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        // $this->service->create($request->validated());
        $data = $request->validated();
        $this->service->create($data);
        return redirect()->route('karyawan.tasks.index')->with('success', 'Task baru berhasil dibuat!');
    }

 public function show(Task $task): View
    {
        $user = Auth::user();
        $view = $user->hasRole('manager') ? 'manager.tasks.show' : 'karyawans.tasks.show';
        return view($view, compact('task'));
    }

    public function edit(Task $task): View
    {
        $user = Auth::user();
        $view = $user->hasRole('manager') ? 'manager.tasks.edit' : 'karyawans.tasks.edit';
        return view($view, compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->service->update($task, $request->validated());
        return redirect()->route('karyawan.tasks.index')->with('success', 'Task berhasil diperbarui.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->service->delete($task);
        return redirect()->back()->with('success', 'Task berhasil dihapus.');
    }
}