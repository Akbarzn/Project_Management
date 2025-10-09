<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $request = ProjectRequest::with('client')->paginate(10);
return view('clients.project-requests.index', compact('request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil data client yang sedang login
        $client = Auth::user()->client;

        // Generate nomor tiket terbaru
        $ticketNumber = $this->generateTiket();

        // Kirim ke view agar bisa ditampilkan sebelum submit
return view('clients.project-requests.create', compact('client', 'ticketNumber'));
    }

    /**
     * Generate Nomor Tiket Otomatis
     */
    private function generateTiket()
    {
        $today = now()->format('dmY'); // contoh: 08102025

        // Cari nomor tiket terakhir yang punya prefix tanggal hari ini
        $lastTicket = ProjectRequest::where('tiket', 'like', $today . '%')
            ->orderBy('tiket', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->tiket, -3); // ambil 3 digit terakhir
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $today . $newNumber;
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
return view('clients.project-requests.show', compact('projectRequest'));
    }

    /**
     * Show the form for editing the specified resource.
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
            if ($projectRequest->upload_file) {
                Storage::disk('public')->delete($projectRequest->upload_file);
            }
            $projectRequest->upload_file = $request->file('document')->store('project_documents', 'public');
        }

        $projectRequest->update([
            'kategori' => $request->kategori,
            'description' => $request->description,
            'status' => $request->status,
            'document' => $projectRequest->upload_file,
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
