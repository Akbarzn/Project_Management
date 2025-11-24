<?php

namespace App\Http\Requests\Project;

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
            'name_project' => 'sometimes|string',
            'description' => 'nullable|string',
            'client_id' => 'required|exists:clients,id',
            'karyawan_ids' => 'sometimes|array|min:1',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'start_date_project' => 'required|date',
            'finish_date_project' => 'required|date|after_or_equal:start_date_project',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Client tidak boleh kosong.',
            'client_id.exists'   => 'Client tidak valid.',

            'karyawan_ids.array' => 'Format data karyawan tidak valid.',
            'karyawan_ids.*.exists' => 'Karyawan yang dipilih tidak ditemukan.',

            'start_date_project.date' => 'Tanggal mulai tidak valid.',
            'finish_date_project.date' => 'Tanggal selesai tidak valid.',
            'finish_date_project.after_or_equal' => 'Tanggal selesai harus sama atau lebih besar dari tanggal mulai.',
        ];
    }
}
