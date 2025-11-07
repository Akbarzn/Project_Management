<?php
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Client\ProjectRequest\StoreProjectRequest;
use App\Http\Requests\Client\ProjectRequest\UpdateProjectRequest;

class ProjectRequestController extends Controller
{

    public function index(Request $request){
        // ->where('status', 'pending')
        $status = $request->query('status', 'pending');
        $data = ProjectRequest::with('client')
        ->when($status, fn($q) => $q->where('status', $status))
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('manager.project-request.index',compact('data','status'));
    }
    public function create(Request $request, ProjectRequest $projectRequest){
        $clients = CLient::all(['id','name']);
        $ticketNumber = $this->generateTiket();

        $selectedClient = $request->query('client_id')
        ? Client::find(request('client_id'))
        : $projectRequest->client;

        return view('manager.project-request.create',compact('clients','ticketNumber', 'projectRequest', 'selectedClient'));
    }

    public function show($id)
{
    $request = ProjectRequest::with('client')->findOrFail($id);

    return view('manager.project-request.show', compact('request'));
}


    public function store(StoreProjectRequest $request){
        ProjectRequest::create([
            'client_id' => $request->client_id,
            'tiket' => $this->generateTiket(),
            'name_project' => $request->name_project,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'document' => $request->file('document')
                ? $request->file('document')->store('project_documents', 'public')
                : null,
            'status' => 'pending'
        ]);
        return redirect()->route('manager.project-request.index')->with('success', 'Project request berhasil dibuat');
    }

    public function edit(ProjectRequest $projectRequest, Request $request){
        $clients = Client::all(['id','name']);
        $ticketNumber = $this->generateTiket();


        $selectedClient = $request->query('client_id')
        ? Client::find(request('client_id'))
        : $projectRequest->client;
        return view('manager.project-request.edit', compact('projectRequest','clients','ticketNumber','selectedClient'));
    }

    public function update(UpdateProjectRequest $request, ProjectRequest $projectRequest){
        if($request->hasFile('document')){
            if($projectRequest->document){
                Storage::disk('public')->delete($projectRequest->document);
            }
            $projectRequest->document = $request->file('document')->store('project_documents', 'public');
        }
        
        $projectRequest->update([
            'client_id' => $request->client_id,
            'name_project' => $request->name_project,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'document' => $projectRequest->document,
        ]);
        return redirect()->route('manager.project-request.index')->with('success','Project request berhasil diperbarui');
    }

    public function destroy(ProjectRequest $projectRequest){
        if($projectRequest->document){
            Storage::disk('public')->delete($projectRequest->document);
        }

        $projectRequest->delete();
        return redirect()->route('manager.project-request.index')->with('success','Project request berhasil dihapus');
    } 

    private function generateTiket(){
        $currentYear = now()->format('Y');
        $currentMounth = now()->format('m');

        // cari tiket yg tahun ini
        $lastTiket = ProjectRequest::where('tiket', 'like', '%' .$currentYear)
            ->orderBy('tiket', 'desc')
            ->first();

        if($lastTiket){
            $lastYear = substr($lastTiket->tiket, -4);
            $lastNumber = (int) substr($lastTiket->tiket,0,3);

        if($lastYear == $currentYear){
            $newNumber = str_pad($lastNumber +1, 3, '0', STR_PAD_LEFT);
        }else{
            $newNumber = '001';
        }
        }else{
            //kalo blm ada tiket sama sekali
            $newNumber = '001';
        }

        $today = $currentMounth. $currentYear;

        return $newNumber . $today;
    }

}

