<?php

namespace App\Http\Requests\Karyawan;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
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
        // $jobTitles = [
        //     'Analisis Proses Bisnis',
        //     'Database FUnctional',
        //     'Programmer',
        //     'Quality Test',
        //     'SysAdmin',
        // ];

        /**
         * atur validasi untuk create karyawan
         */
        
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|max:50',
            'nik' => 'nullable|unique:karyawans,nik|max:30', 
            'jabatan' => 'string|max:50',
            'phone' => 'required|nullable',
            'job_title' =>'required',
            'cost' => 'required|numeric'
        
        ];
    }

     /**
      * Summary of messages
      * pesan error custom 
      * @return array{cost.numeric: string, cost.required: string, email.email: string, email.required: string, email.unique: string, jabatan.max: string, job_title.required: string, name.max: string, name.required: string, nik.max: string, nik.unique: string, password.min: string, password.required: string, phone.max: string}
      */
     public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan, silakan pakai yang lain.',

            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 8 karakter.',

            'name.required' => 'Nama wajib diisi.',
            'name.max'      => 'Nama maksimal 50 karakter.',

            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.max'    => 'NIK maksimal 30 karakter.',

            'jabatan.max' => 'Jabatan maksimal 50 karakter.',

            'phone.max' => 'Nomor telepon terlalu panjang.',

            'job_title.required' => 'Job title wajib dipilih.',

            'cost.required' => 'Cost wajib diisi.',
            'cost.numeric'  => 'Cost harus berupa angka.',
        ];
    }
}
