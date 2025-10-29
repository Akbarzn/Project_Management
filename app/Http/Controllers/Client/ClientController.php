<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $clients = Client::with('user')->paginate(10);
        return view('manager.clients.index', compact('clients'));
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
    // public function store(Request $request)
    // {
    //     //
    //     $request->validate([
    //         'name' => 'required|max:50',
    //         'nik' => 'required|max:15',
    //         'phone' => 'required|nullable|max:15',
    //         'kode_organisasi' => 'nullable|max:10',
    //     ]);

    //     Client::create($request->all());
    //     return redirect()->route('clients.index')->with('success', 'Client Success Added');
    // }

    public function store(StoreClientRequest $request)
{
   
      // ambil data yang sudah divalidasi
        $validatedData = $request->validated();
        
        // buat user baru
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']), 
        ]);

        // cek apakah metod ada di spatie 
        if (method_exists($user, 'assignRole')) {
             $user->assignRole('client');
        }

        // buat data client yang terhubung dengan user
        Client::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'nik' => $validatedData['nik'],
            'phone' => $validatedData['phone'],
            'kode_organisasi' => $validatedData['kode_organisasi'],
        ]);

        return redirect()->route('manager.clients.index')
            ->with('success', 'Client berhasil ditambahkan.');
    }



    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
        return view('manager.clients.edit',compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {

       $client->update($request->validated()); 
        $client->user->update([
            'name' => $request->name
        ]);

        $user = $client->user;
        $user->syncRoles('client');
        return redirect()->route('manager.clients.index')->with('success', 'Client berhasil di update');
    }

    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
        $client->delete();
        return redirect()->route('manager.clients.index')->with('success', 'CLient berhasil di hapus');
    }
}
