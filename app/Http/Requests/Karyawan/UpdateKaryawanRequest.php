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
        // Kita dapat mengakses model Karyawan yang sedang diperbarui melalui route
        $karyawanId = $this->route('karyawan')->id ?? null;
        return [
            'name' => 'sometimes|max:50',
            'nik' => 'sometimes|unique:karyawans,nik,' .$karyawan->id,
            'phone' => 'sometimes',
            'job_title' =>'sometimes',
            'cost' => 'sometimes|numeric',
            'jabatan' => 'sometimes',
        ];
    }
    
}
