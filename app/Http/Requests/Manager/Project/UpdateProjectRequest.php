<?php

namespace App\Http\Requests\Manager\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan hanya manager yang bisa approve
        // return auth()->check() && auth()->user()->role === 'manager';
        return true;
    }

    public function rules(): array
    {
        return [
            'name_project' => 'string',
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'start_date_project' => 'required|date',
            'finish_date_project' => 'required|date|after_or_equal:start_date_project',
        ];
    }

    public function messages(): array
    {
        return [
            'karyawan_ids.required' => 'Pilih minimal satu karyawan untuk mengerjakan project.',
            'start_date_project.required' => 'Tanggal mulai harus diisi.',
            'finish_date_project.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ];
    }
}
