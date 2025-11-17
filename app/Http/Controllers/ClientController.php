<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Services\ClientService;
use App\Models\Client;

class ClientController extends Controller
{
    protected ClientService $clientService;

    public function __construct(ClientService $clientService){
        $this->clientService = $clientService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        // ambil data dari service
        $clients = $this->clientService->listClients($search);
        return view('manager.clients.index', compact('clients','search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manager.clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        //validasi data dgn formrequest
        $validated  = $request->validated();

        // buat client via service
        $this->clientService->createClient($validated);

        return redirect()->route('manager.clients.index')->with('Client berhasil ditambahkan','');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        // ambil data client dari service
        $client = $this->clientService->showClient($id);

        // cek apakah ada client
        if(!$client){
            abort(404, 'Client tidak ditemukan');
        }

        return view('manager.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        // laravel route-model otomatis kirim model client
        return view('manager.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        //validasi data
        $validated = $request->validated();

        // uppdate via service
        $this->clientService->updateClient($client, $validated);

        return redirect()->route('manager.clients.index')->with('success','Client berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // hapus via service
        $this->clientService->deleteClient($client);

        return redirect()->route('manager.clients.index')->with('success','Client berhasil dihapus');
    }
}
