<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Services\ProjectService;
use App\Models\Project;
use Illuminate\Http\Request;


class ProjectController extends Controller
{
    /**
     * Summary of service
     * simpan project service ke property
     * @var ProjectService
     */
    protected ProjectService $service;

    /**
     * Summary of __construct
     * inject projectService ke controller
     * @param ProjectService $service
     */
    public function __construct(ProjectService $service){
        $this->service = $service;
    }

    public function index(Request $request){
        $search = $request->get("search");
        $projects = $this->service->listProjects($search);
        return view('manager.projects.index', compact('projects'));
    }

    public function create($requestId){
        $projects = $this->service->getCreateData($requestId);
        return view('manager.projects.create', $projects);
    }

    public function show($id){
        return view('manager.projects.show', $this->service->showProject($id));
    }

    public function store(StoreProjectRequest $request){
        $this->service->create($request->validated());
        return redirect()->route('manager.projects.index')->with('success', 'Project created successfully');
    }

    public function edit(Project $project)
{
    $data = $this->service->getEditData($project);
    return view('manager.projects.edit', $data);
}


    public function update(UpdateProjectRequest $request, Project $project){
        $this->service->update($project, $request->validated());
        return redirect()->route('manager.projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project){
        $this->service->delete($project);
        return redirect()->route('manager.projects.index')->with('success', 'Project Deleted successfully');
    }

}