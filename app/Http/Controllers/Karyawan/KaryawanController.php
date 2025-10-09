<?php

namespace App\Http\Controllers\Karyawan;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $karyawans = Karyawan::with('user')->paginate(10);
        return view('karyawans.index', compact('karyawans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        // $karyawans = User::doesntHave('karyawan')->get();
        return view('karyawans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('karyawan');

        Karyawan::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'nik' => $request->nik,
            'phone' => $request->phone,
            'jabatan' => $request->jabatan,
            'job_title' => $request->job_title,
            'cost' => $request->cost,
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
        return view('karyawans.edit', compact('karyawan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        $karyawan->update($request->validated()); // Gunakan validated() untuk data yang sudah bersih

        $user = $karyawan->user;
        $user->syncRoles('karyawan');
        return redirect()->route('karyawans.index')->with('success', 'Update Berhasil');
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
