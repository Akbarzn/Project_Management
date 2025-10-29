<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::paginate(10);
        return view('manager.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('manager.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:manager,karyawan,client',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' =>Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);

        
    if ($data['role'] === 'karyawan') {
        \App\Models\Karyawan::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'nik' => 'KR' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
            'jabatan' => 'Belum Ditentukan', // default
            'phone' => '0000000000',          // default
            'job_title' => 'Default Job',     // default
            'cost' => 0,                      // default
        ]);
    }

    if ($data['role'] === 'client') {
        \App\Models\Client::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'nik' => 'CL' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
            'kode_organisasi' => 'ORG-' . strtoupper(Str::random(4)),
            'phone' => null,
        ]);
    }

        return redirect()->route('manager.users.index')->with('success', 'User Created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
        return view('manager.users.edit',compact('user'));
    }

    public function update(Request $request, User $user)
{
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => [
            'required',
            'email',
            Rule::unique('users')->ignore($user->id),
        ],
        'password' => 'nullable|min:6|confirmed',
        'role' => 'required|in:manager,karyawan,client',
    ]);

    // Update basic user data
    $user->name = $data['name'];
    $user->email = $data['email'];
    if (!empty($data['password'])) {
        $user->password = Hash::make($data['password']);
    }
    $user->save();

    // Update roles (Spatie)
    $user->syncRoles([$data['role']]);

    // --- Mulai logika otomatis berdasarkan role ---
    if ($data['role'] === 'karyawan') {
        // Cek apakah sudah punya record di tabel karyawan
        $karyawan = \App\Models\Karyawan::where('user_id', $user->id)->first();
        if (!$karyawan) {
            // Jika belum ada, buat baru
            \App\Models\Karyawan::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'nik' => 'KR' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'jabatan' => 'Belum Ditentukan',
                'phone' => '0000000000',
                'job_title' => 'Default Job',
                'cost' => 0,
            ]);
        } else {
            // Jika sudah ada, update nama biar sinkron
            $karyawan->update(['name' => $user->name]);
        }

        // Hapus record client (kalau sebelumnya dia client)
        \App\Models\Client::where('user_id', $user->id)->delete();
    }

    if ($data['role'] === 'client') {
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        if (!$client) {
            \App\Models\Client::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'nik' => 'CL' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                'kode_organisasi' => 'ORG-' . strtoupper(Str::random(4)),
                'phone' => null,
            ]);
        } else {
            $client->update(['name' => $user->name]);
        }

        // Hapus record karyawan (kalau sebelumnya dia karyawan)
        \App\Models\Karyawan::where('user_id', $user->id)->delete();
    }

    if ($data['role'] === 'manager') {
        // Kalau manager, hapus data client & karyawan agar bersih
        \App\Models\Client::where('user_id', $user->id)->delete();
        \App\Models\Karyawan::where('user_id', $user->id)->delete();
    }
    return redirect()->route('manager.users.index')->with('success', 'Update Success');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user $user)
    {
        //
        if(auth()->id() === $user->id){
            return redirect()->back()->with('error','You cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('manager.users.index')->with('success', 'Delete Success');
    }
}
