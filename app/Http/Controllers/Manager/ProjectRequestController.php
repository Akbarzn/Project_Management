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
        $request = ProjectRequest::with('client')
        ->when($status, fn($q) => $q->where('status', $status))
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('manager.project-request.index',compact('request'));
    }
    public function create(){
        $clients = CLient::all(['id','name']);
        $ticketNumber = $this->generateTiket();
        return view('manager.project-request.create',compact('clients','ticketNumber'));
    }

    public function show($id)
{
    // ambil data project request berdasarkan ID
    $request = \App\Models\ProjectRequest::with('client')->findOrFail($id);

    // tampilkan view untuk detailnya
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

    public function edit(ProjectRequest $projectRequest){
        $clients = Client::all(['id','name']);
        return view('manager.project-request.edit', compact('projectRequest','clients'));
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
        $lastTiket = ProjectRequest::orderBy('tiket', 'desc')->first();

        // ambil 3 digit terkahir
        $newNumber = $lastTiket 
            ? str_pad(((int) substr($lastTiket->tiket, -3)) + 1, 3, '0', STR_PAD_LEFT)
            :'001';

         $today = now()->format('dmY');
         
         return $today . $newNumber;

    }

}

