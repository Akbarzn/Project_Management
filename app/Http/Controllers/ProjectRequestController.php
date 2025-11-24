<?php 

namespace App\Http\Controllers;

use App\Models\Client;
use App\MOdels\ProjectRequest;
use Illuminate\Http\Request;
use App\Services\ProjectRequestService;

use App\Http\Requests\ProjectRequest\UpdateProjectRequest;
use App\Http\Requests\ProjectRequest\StoreProjectRequest;

class ProjectRequestController extends COntroller{

    /**
     * Summary of __construct
     * simpan service ke property
     * @param ProjectRequestService $service
     */
    public function __construct(protected ProjectRequestService $service){
    }

    /**
     * Summary of index
     * nampilin datar project request 
     * pake search dan filter status
     * view manager dan client beda
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request){
        $search = $request->get("search");
        $status = $request->query('status', 'pending');
        $data = $this->service->listProjectRequests($search,$status);
        $user = auth()->user();

        //cek kalo user client berarti cmn bisa liat request milikinya sendiri
        if($user->hasRole('client')){
            foreach($data as $projectRequest){
                $this->authorize('view', $projectRequest);
            }
        }

        // tentukan view sesuai role
        $view = $user->hasRole('manager') 
        ? 'manager.project-request.index'
        :'clients.project-requests.index';

        return view($view, compact('status', 'data', 'search'));
    }
    
    /**
     * Summary of create
     * nampilin form create projec request
     * kalo yang request project itu manager maka bisa pilih client
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request){
        $user = auth()->user();

        // kalo manager maka tampilin semua data client
        $clients = $user->hasRole('manager') ? Client::all(['id', 'name']): collect();

        // kalo client maka ambil relasi cllientnya
        $client = $user->hasRole('client') ? $user->client : null;
        
        // kalo manager pilih client tertentu
        $selectedClient = $request->query('client_id')
       ? Client::find($request->query('client_id'))
       : null;

        // panggil tiket number  dari service    
       $ticketNumber = $this->service->generateTicket();

        // tntukan view role
        $view = $user->hasRole('manager')
         ? 'manager.project-request.create'
         :'clients.project-requests.create';

        return view($view, compact('clients', 'client', 'ticketNumber','selectedClient'));
    }

    /**
     * Summary of store
     * simpan project request baru
     * @param StoreProjectRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProjectRequest $request){
    //    dd($request->all());
        $data = $request->validated();
        $user = auth()->user();

        // simpan document,jika ada upload document 
        if($request->hasFIle('document')){
            $data['document'] = $request->file('document');
        }
        
        $this->service->create($data);

        $route = $user->hasRole('manager') 
        ? 'manager.project-request.index'
        :'clients.project-requests.index';

        return redirect()->route($route)->with('success','Project request berhasil dibuat');
    }

    public function show(ProjectRequest $projectRequest){
        $user = auth()->user();
        
        // cek apkah user berhak lihat data ini
        $this->authorize('view', $projectRequest);

        $view = $user->hasRole('manager') 
        ? 'manager.project-request.show'
        :'clients.project-requests.show';

        return view($view, compact('projectRequest'));
    }

    /**
     * Summary of edit
     * nampilin form edit projectrequest
     * manager dan client bisa edit sesuai otorisasi
     * @param ProjectRequest $projectRequest
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(ProjectRequest $projectRequest, Request $request){
        $user = auth()->user();

        $clients = Client::all(['id','name']);

        if($user->hasRole('client')){
            abort_unless($projectRequest->client_id === $user->client->id, 403);
        }

        $selectedClient = $request->query('client_id')
        ? Client::find($request->query('client_id'))
        : null;

        // cek izin edit
        $this->authorize('update', $projectRequest);

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

        $this->authorize('update', $projectRequest);

        $user = auth()->user();
        $route = $user->hasRole('manager') 
        ? 'manager.project-request.index'
        :'clients.project-requests.index';

        return redirect()->route($route)->with('success', 'Project request berhasil diperbarui');
    }

    public function destroy(ProjectRequest $projectRequest){
        $user = auth()->user();

        $this->authorize('delete', $projectRequest);

        $this->service->delete($projectRequest);
        
        $route = $user->hasRole('manager') ? 'manager.project-request.index':'clients.project-requests.index';
        return redirect()->route($route)->with('success','Project request berhasil dihapus');
    }
}
