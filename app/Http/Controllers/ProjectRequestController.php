<?php 

namespace App\Http\Controllers;

use App\Models\Client;
use App\MOdels\ProjectRequest;
use Illuminate\Http\Request;
use App\Services\ProjectRequestService;

use App\Http\Requests\ProjectRequest\UpdateProjectRequest;
use App\Http\Requests\ProjectRequest\StoreProjectRequest;

class ProjectRequestController extends COntroller{
    public function __construct(protected ProjectRequestService $service){

    }

    public function index(Request $request){
        $search = $request->get("search");
        $status = $request->query('status', 'pending');
        $data = $this->service->listProjectRequests($search,$status);
        $user = auth()->user();
        $view = $user->hasRole('manager') ? 'manager.project-request.index':'clients.project-requests.index';
        return view($view, compact('status', 'data', 'search'));
    }
    
    public function create(Request $request){
        $user = auth()->user();
        $clients = $user->hasRole('manager') ? Client::all(['id', 'name']): collect();
        $client = $user->hasRole('client') ? $user->client : null;
        
        $view = $user->hasRole('manager') ? 'manager.project-request.create':'clients.project-requests.create';
         $selectedClient = $request->query('client_id')
        ? Client::find($request->query('client_id'))
        : null;

       $ticketNumber = $this->service->generateTicket();
        return view($view, compact('clients', 'client', 'ticketNumber', 'selectedClient'));
    }

    public function store(StoreProjectRequest $request){
    //    dd($request->all());
        $data = $request->validated();
        $user = auth()->user();

        if($request->hasFIle('document')){
            $data['document'] = $request->file('document');
        }
        
        $this->service->create($data);
        $route = $user->hasRole('manager') ? 'manager.project-request.index':'clients.project-requests.index';
        return redirect()->route($route)->with('success','Project request berhasil dibuat');
    }

    public function show(ProjectRequest $projectRequest){
        $user = auth()->user();
        
        if($user->hasRole('client')){
            abort_unless($projectRequest->client_id === $user->client->id, 403);
        }
        
        $view = $user->hasRole('manager') ? 'manager.project-request.show':'clients.project-requests.show';
        return view($view, compact('projectRequest'));
    }

    public function edit(ProjectRequest $projectRequest, Request $request){
        $user = auth()->user();
        $clients = Client::all(['id','name']);
        if($user->hasRole('client')){
            abort_unless($projectRequest->client_id === $user->client->id, 403);
        }
        $selectedClient = $request->query('client_id')
        ? Client::find($request->query('client_id'))
        : null;

       $view = $user->hasRole('manager') ? 'manager.project-request.edit':'clients.project-requests.edit';
        return view($view, compact('projectRequest','clients', 'selectedClient'));
    }

    public function update(UpdateProjectRequest $request, ProjectRequest $projectRequest){
    //    dd($request->all());

        $data = $request->validated();
        if($request->hasFile('document')){
            $data['document'] = $request->file('document');
        }

        $this->service->update($projectRequest, $data);

        $user = auth()->user();
        $route = $user->hasRole('manager') ? 'manager.project-request.index':'clients.project-requests.index';
        return redirect()->route($route)->with('success', 'Project request berhasil diperbarui');
    }

    public function destroy(ProjectRequest $projectRequest){
        $user = auth()->user();

        // hanya manager atau client yang bisa hapus
        if($user->hasRole('client') && $projectRequest->client_id !== $user->client->id){
            abort(403, 'Anda tidak berhak menghapus project ini');
        }

        $this->service->delete($projectRequest);
        
        $route = $user->hasRole('manager') ? 'manager.project-request.index':'clients.project-requests.index';
        return redirect()->route($route)->with('success','Project request berhasil dihapus');
    }
}
