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
        // dd('masuk ke update ');
        $user = Auth::user();
        $validated = $request->validated();

        // Update data user
        $user->name = $validated['name'];
        $user->email = $validated['email'];

         if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // update poto
        if ($request->hasFile('profile_photo')) {
            if ($user->potho_profile && $user->potho_profile !== 'images/default.jpg') {
                Storage::disk('public')->delete($user->potho_profile);
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->potho_profile = $path;
        }

        $user->save();

        // update data karyawan
        if ($user->hasRole('karyawan') && $user->karyawan) {
            $user->karyawan->update([
                'nik' => $validated['nik'] ?? $user->karyawan->nik,
                'phone' => $validated['phone'] ?? $user->karyawan->phone,
            ]);
        }

        // update data client
        if ($user->hasRole('client') && $user->client) {
            $user->client->update([
                'jabatan' => $validated['jabatan'] ?? $user->client->jabatan,
            ]);
        }

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
