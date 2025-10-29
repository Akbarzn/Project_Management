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
        $jobTitles = [
            'Analisis Proses Bisnis',
            'Database FUnctional',
            'Programmer',
            'Quality Test',
            'SysAdmin',
        ];
        
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|max:50',
            'nik' => 'nullable|unique:karyawans,nik', 
            'jabatan' => 'string|max:50',
            'phone' => 'required|nullable',
            'job_title' =>'required',
            'cost' => 'required|numeric'
        
        ];
    }
}
