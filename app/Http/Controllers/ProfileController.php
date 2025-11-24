<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        
        // Update data user
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // update password jika user isi password baru
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        // update poto profile
        if ($request->hasFile('potho_profile')) {
            if ($user->potho_profile && $user->potho_profile !== 'images/default.jpg') {
                Storage::disk('public')->delete($user->potho_profile);
            }

            // upload poto baru ke storage/public/profile_pothos
            $path = $request->file('potho_profile')->store('profile_photos', 'public');
            $user->potho_profile = $path;
        }
        
        
        // update data karyawan
        if ($user->hasRole('karyawan') && $user->karyawan) {
            $user->karyawan->update([
                'nik' => $validated['nik'] ?? $user->karyawan->nik,
                'phone' => $validated['phone'] ?? $user->karyawan->phone,
                'jabatan' => $validated['jabatan'] ?? $user->karyawan->jabatan,
                'job_title' => $validated['job_title'] ?? $user->karyawan->job_title,
                'cost' => $validated['cost'] ?? $user->karyawan->cost,
            ]);
        }
        
        // update data client
        if ($user->hasRole('client') && $user->client) {
            $user->client->update([
                'nik' => $validated['nik'] ?? $user->client->nik,
                'phone' => $validated['phone'] ?? $user->client->phone,
                'kode_organisasi' => $validated['kode_organisasi'] ?? $user->client->kode_organisasi,
            ]);
        }
        $user->save();
        // dd('masuk ke update ');/

        // dd($validated);
        // dd($user->roles->pluck('name'));


        return back()->with('success', 'Profil berhasil diperbarui.');
    }
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
