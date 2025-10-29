<?php

namespace App\Http\Controllers\Karyawan;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Karyawan\UpdateKaryawanRequest;
use App\Http\Requests\Karyawan\StoreKaryawanRequest;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $karyawans = Karyawan::with('user')->paginate(10);
        return view('manager.karyawans.index', compact('karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('manager.karyawans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKaryawanRequest $request)
    {
        //
        $validatedData = $request->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($request->password),
        ]);

          if (method_exists($user, 'assignRole')) {
             $user->assignRole('karyawan');
        }

        Karyawan::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'nik' => $validatedData['nik'],
            'jabatan' => $validatedData['jabatan'],
            'phone' => $validatedData['phone'],
            'job_title' => $validatedData['job_title'],
            'cost' => $validatedData['cost'],
        ]);

        return redirect()->route('manager.karyawans.index')->with('success', 'Karyawan sukses ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan)
    {
        //
   
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Karyawan $karyawan)
    {
        //
        return view('manager.karyawans.edit', compact('karyawan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKaryawanRequest $request, Karyawan $karyawan)
{
    $user = $karyawan->user;

    // 🔹 Update tabel users
    $userData = $request->only(['name', 'email', 'password']);
    $userData = array_filter($userData, fn($value) => !is_null($value) && $value !== '');

    if (isset($userData['password'])) {
        $userData['password'] = bcrypt($userData['password']);
    }

    if (!empty($userData)) {
        $user->update($userData);
    }

    // 🔹 Update tabel karyawans
    $karyawanData = $request->only(['name', 'nik', 'phone', 'job_title', 'cost', 'jabatan']);
    $karyawanData = array_filter($karyawanData, fn($value) => !is_null($value) && $value !== '');

    if (!empty($karyawanData)) {
        $karyawan->update($karyawanData);
    }

    // 🔹 Pastikan role tetap 'karyawan'
    $user->syncRoles('karyawan');

    return redirect()
        ->route('manager.karyawans.index')
        ->with('success', 'Data karyawan berhasil diperbarui!');
}

    
    /**
     * Remove the specified resource from storage.
    */
    public function destroy(Karyawan $karyawan)
    {
        //
        $karyawan->delete();
        return redirect()->route('manager.karyawans.index')->with('success', 'Delete Berhasil');
    }
}
