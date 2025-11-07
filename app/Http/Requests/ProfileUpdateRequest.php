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

    // Awali dengan aturan umum
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

    
    if ($user && $user->hasRole('karyawan')) {
        $rules = array_merge($rules, [
            'nik' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:15'],
        ]);
    }

    if ($user && $user->hasRole('client')) {
        $rules = array_merge($rules, [
            'nik' => ['nullable', 'string', 'max:15'],
            'kode_organisasi' => ['nullable', 'string', 'max:15'],
            'phone' => ['nullable', 'string', 'max:15']
        ]);
    }

    return $rules;
}


    public function messages():array{
        return[
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ];
    }

}
