<?php

namespace App\Http\Controllers\Manager;


use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\User;

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
        //
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
    //     return redirect()->route('manager.clients.index')->with('success', 'Client Success Added');
    // }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:50',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6', // tambahkan password juga
        'nik' => 'required|max:15',
        'phone' => 'nullable|max:15',
        'kode_organisasi' => 'nullable|max:10',
    ]);

    // 1️⃣ Buat user baru
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password), // enkripsi password
    ]);

    // assign role client
    $user->assignRole('client');

    // 2️⃣ Buat data client yang terhubung dengan user
    Client::create([
        'user_id' => $user->id,
        'name' => $user->name,
        'nik' => $request->nik,
        'phone' => $request->phone,
        'kode_organisasi' => $request->kode_organisasi,
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
    public function update(Request $request, Client $client)
    {
        //
        $request->validate([
            'name' => 'required|max:50',
            'nik' => 'required|unique:clients,nik, |max:15',
            'phone' => 'required|nullable|max:15',
            'kode_organisasi' => 'nullable|max:10',
        ]);

        $client->update($request->all());
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
