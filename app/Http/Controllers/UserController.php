<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * UserController = jembatan antara View (Blade) dan Service.
 *
 * Bahasa pelajar:
 * - Controller cuma nerima request dari user
 * - Controller tidak query database langsung
 * - Controller lempar pekerjaan ke Service
 */
class UserController extends Controller
{
    /**
     * simpan userservice ke property.
     */
    protected UserService $service;

    /**
     * Inject service ke controller.
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Halaman daftar user dengan fitur search.
     */
    public function index(Request $request)
    {
        // Ambil kata pencarian dari input form
        $search = $request->get('search');

        // Ambil user dari Service
        $users = $this->service->getAllUsers($search);

        // Kirim ke view
        return view('manager.users.index', compact('users', 'search'));
    }

    /**
     * Halaman form create user.
     */
    public function create()
    {
        return view('manager.users.create');
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        // Validasi form
        $data = $request->validate([
            'name'     => 'required|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:manager,karyawan,client',
        ]);

        // Kirim ke Service untuk dibuatkan
        $this->service->createUser($data);

        // Redirect kembali ke index
        return redirect()->route('manager.users.index')
            ->with('success', 'User Created');
    }

    /**
     * Halaman edit user.
     */
    public function edit(User $user)
    {
        return view('manager.users.edit', compact('user'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        // Validasi
        $data = $request->validate([
            'name'     => 'required|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6|confirmed',
            'role'     => 'required|in:manager,karyawan,client',
        ]);

        // Kirim ke service untuk diupdate
        $this->service->updateUser($data, $user);

        return redirect()->route('manager.users.index')
            ->with('success', 'Update Success');
    }

    /**
     * Hapus user dari sistem.
     */
    public function destroy(User $user)
    {
        // Jika user mencoba hapus dirinya sendiri
        if (!$this->service->deleteUser($user)) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        return redirect()->route('manager.users.index')
            ->with('success', 'Delete Success');
    }
}
