<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Client;

class ProjectRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $request = ProjectRequest::with('client')->paginate(10);
    //     return view('clients.project-requests.index', compact('request'));
    // }

    public function index()
{
    $user = auth()->user();

    // cek apa user itu clinet
    if ($user->hasRole('client')) {
        $clientId = $user->client->id;

        // Ambil project request milik client yang login saja
        $requests = ProjectRequest::with('client')
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    } else {
        // Untuk manager atau lainnya, tampilkan semua
        $requests = ProjectRequest::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    return view('clients.project-requests.index', compact('requests'));
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ambil data client yang sedang login
        $user = Auth::user();
        $isManager = $user->hasRole('manager');
        $client = null;
        $clients = collect();
        if($user->hasRole('client')){
            $client = $user->client;
        }elseif($isManager){
            $clients = Client::all(['id','name']);
        }

        // generate nomor tiket terbaru
        $ticketNumber = $this->generateTiket();

        return view('clients.project-requests.create', compact('client', 'ticketNumber'));
    }

    /**
     * Generate Nomor Tiket Otomatis
     */
    private function generateTiket()
    {
        $today = now()->format('dmY');

        // cari nomor tiket terakhir yang punya prefix tanggal hari ini
        $lastTicket = ProjectRequest::where('tiket', 'like', $today . '%')
            ->orderBy('tiket', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->tiket, -3); // ambil 3 digit terakhir untuk no berjalannya
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return  $newNumber . $today ;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $client = Auth::user()->client;
        $filePath = null;

        if ($request->hasFile('document')) {
            $filePath = $request->file('document')->store('project_documents', 'public');
        }

        ProjectRequest::create([
            'client_id' => $client->id,
            'tiket' => $this->generateTiket(),
            'name_project' => $request->name_project,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'document' => $filePath,
            'status' => 'pending',
        ]);

        // dd(ProjectRequest::latest()->first());

        return redirect()->route('clients.project-requests.index')
            ->with('success', 'Project request berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectRequest $projectRequest)
    {
        // $request = ProjectRequest::with('client');
    return view('clients.project-requests.show', compact('ProjectRequest'));
    }

    /**
 * Show the form for editing the specified resource
     */
    public function edit(ProjectRequest $projectRequest)
    {
        return view('clients.project-requests.edit', compact('projectRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectRequest $projectRequest)
    {
        if ($request->hasFile('document')) {
            if ($projectRequest->document) {
                Storage::disk('public')->delete($projectRequest->document);
            }
            $projectRequest->document = $request->file('document')->store('project_documents', 'public');
        }

        $projectRequest->update([
            'kategori' => $request->kategori,
            'description' => $request->description,
            'document' => $projectRequest->document,
        ]);

        return redirect()->route('clients.project-requests.index')->with('success', 'Project request berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectRequest $projectRequest)
    {
        if ($projectRequest->upload_file) {
            Storage::disk('public')->delete($projectRequest->upload_file);
        }

        $projectRequest->delete();

        return redirect()->route('clients.project-requests.index')->with('success', 'Project request berhasil dihapus.');
    }
}
