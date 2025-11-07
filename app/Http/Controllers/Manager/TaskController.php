<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Task;
use App\Models\Project;


class TaskController extends Controller
{
    public function index(Request $request){
        $query = Task::with(['karyawan.user','project.projectRequest']);

        if($request->filled('project_id')){
            $query->where('project_id',$request->project_id);
        }

        // karyawan
       if($request->filled('karyawan_id')){
        $query->where('karyawan_id', $request->karyawan_id);
       }

        // status
       if($request->filled('status')){
        $query->where('status', $request->status);
       }
       
        // if($request->filled('search')){
        //     $query->where('name_project', 'like', "%{$request->search}%");
        // }

       if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->whereHas('project.projectRequest', function ($q2) use ($search) {
                $q2->where('name_project', 'like', "%{$search}%");
            })
            ->orWhereHas('karyawan.user', function ($q3) use ($search) {
                $q3->where('name', 'like', "%{$search}%");
            });
        });
    }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        $projects = Project::with('projectRequest')->get();
        $karyawans = Karyawan::with('user')->get();

        return view('manager.tasks.index', compact('tasks', 'projects', 'karyawans'));
    }

}
