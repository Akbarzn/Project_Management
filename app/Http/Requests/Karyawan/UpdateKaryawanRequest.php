<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKaryawanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // ambil id karyawan dari route untuk pengecualian unique
        $karyawanId = $this->route('karyawan')->id ?? null;

        return [
            // field karyawan
            'name' => 'sometimes|max:50',
            'nik' => 'sometimes|max:30|unique:karyawans,nik,' . $karyawanId,
            'phone' => 'sometimes',
            'job_title' => 'sometimes',
            'cost' => 'sometimes|numeric',
            'jabatan' => 'sometimes',

            // field user
            'email' => 'nullable|email|unique:users,email,' . $this->karyawan->user_id,
            'password' => 'nullable|min:6|confirmed'
        ];
    }

     /**
      * Summary of messages
      * custom pesan erro
      * @return array{cost.numeric: string, email.email: string, email.unique: string, jabatan.max: string, job_title.max: string, name.max: string, nik.max: string, nik.unique: string, password.confirmed: string, password.min: string, phone.max: string}
      */
     public function messages(): array
    {
        return [
            'name.max' => 'Nama maksimal 50 karakter.',

            'email.email'  => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain.',

            'password.min'      => 'Password minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi password tidak sesuai.',

            'nik.unique' => 'NIK sudah digunakan.',
            'nik.max'    => 'NIK maksimal 30 karakter.',

            'phone.max' => 'Nomor telepon terlalu panjang.',

            'jabatan.max' => 'Jabatan maksimal 50 karakter.',

            'job_title.max' => 'Job title maksimal 50 karakter.',

            'cost.numeric' => 'Cost harus berupa angka.',
        ];
    }

}
