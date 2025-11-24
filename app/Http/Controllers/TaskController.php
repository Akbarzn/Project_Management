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
use App\Http\Controllers\Controller;
use App\Policies\TaskPolicy;


/**
 * @method void middleware($middleware)
 */
class TaskController extends Controller{

 /**
  * Summary of __construct
 simpan tassk service ke property 
  * @param TaskService $service
  */
 public function __construct(protected TaskService $service){
 }   

 /**
  * Summary of index
  * nampilin daftar task
  * manager bisa lihat semua task
  * karyawan cuman bisa lihat task miliknya sendiri
  * @param Request $request
  * @return View
  */
 public function index(Request $request): View{
    // ambil task berdasarkan filter/search jika ada
    $tasks = $this->service->listTask($request->all());
    $user = Auth::user();

    // manager punya data semua project ,karyawan cuman project milikinya
    $projects = $user->hasRole('manager')
    ?Project::with('projectRequest')->get()
    :$user->karyawan->projects()->with('projectRequest')->get();

    // manager bisa lihat semua karyawan
    $karyawans = $user->hasRole('manager') 
    ? Karyawan::with('user')->get()
    : collect();

    // karyawan cuman bisa lihat task yang dipunyanya
    if($user->hasRole('karyawan')){
        foreach($tasks as $task){
            $this->authorize('view', $task);
        }
    }

    // tentukan view berdasarkan role
    $view = $user->hasRole('manager')
    ?'manager.tasks.index'
    :'karyawans.tasks.index';

    return view($view, compact('tasks', 'projects', 'karyawans'));
 }

    public function show(Task $task): View
    {
        $this->authorize('view', $task);
        $user = Auth::user();
        $view = $user->hasRole('manager') ? 'manager.tasks.show' : 'karyawans.tasks.show';
        return view($view, compact('task'));
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);
        $user = Auth::user();
        $view = $user->hasRole('manager') ? 'manager.tasks.edit' : 'karyawans.tasks.edit';
        return view($view, compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);
        $this->service->update($task, $request->validated());
        return redirect()->route('karyawan.tasks.index')->with('success', 'Task berhasil diperbarui.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);
        $this->service->delete($task);
        return redirect()->back()->with('success', 'Task berhasil dihapus.');
    }
}