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
        return [
            'email' => 'required|unique:karyawans,email',
            'password' => 'required|min:8',
            'name' => 'required|max:50',
            'nik' => 'nullable|unique:karyawans,nik', 
            'jabatan' => 'string|max:25',
            'phone' => 'required|nullable',
            'job_title' =>'required',
            'cost' => 'required|numeric'
        
        ];
    }
}
