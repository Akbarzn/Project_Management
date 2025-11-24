<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    $user = $this->user() ?? Auth::user();

    // untuk semua role
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class)->ignore($user->id),
        ],
        'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        'password' => ['nullable', 'confirmed', 'min:8'],
    ];

    // untuk karywan
    if ($user && $user->hasRole('karyawan')) {
        $rules = array_merge($rules, [
            'nik' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:15'],
            'jabatan' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:50'],
            'cost' => ['nullable', 'numeric'],
        ]);
    }

    // untuk client
    if ($user && $user->hasRole('client')) {
        $rules = array_merge($rules, [
            'nik' => ['nullable', 'string', 'max:15'],
            'kode_organisasi' => ['nullable', 'string', 'max:15'],
            'phone' => ['nullable', 'string', 'max:15']
        ]);
    }

    return $rules;
}


    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max'      => 'Nama maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan.',

            'password.min'        => 'Password minimal 8 karakter.',
            'password.confirmed'  => 'Konfirmasi password tidak sesuai.',

            'potho_profile.image' => 'File harus berupa gambar.',
            'potho_profile.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
            'potho_profile.max'   => 'Ukuran foto maksimal 2MB.',

            'nik.max' => 'NIK terlalu panjang.',
            'phone.max' => 'Nomor telepon terlalu panjang.',
            'jabatan.max' => 'Jabatan maksimal 50 karakter.',
            'job_title.max' => 'Job title maksimal 50 karakter.',
            'cost.numeric' => 'Cost harus berupa angka.',

            'kode_organisasi.max' => 'Kode organisasi maksimal 15 karakter.',
        ];
    }

}
