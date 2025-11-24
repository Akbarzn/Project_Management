<?php

namespace App\Http\Requests\Manager\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Summary of rules
     * 
     * @return array{finish_date_project: string, karyawan_ids: string, karyawan_ids.*: string, name_project: string, start_date_project: string}
     */
    public function rules(): array
    {
        return [
            'name_project' => 'string',
            'karyawan_ids' => 'sometimes|array|min:1',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'start_date_project' => 'required|date',
            'finish_date_project' => 'required|date|after_or_equal:start_date_project',
        ];
    }

   public function messages(): array
    {
        return [
            'name_project.string' => 'Nama project harus berupa teks.',
            'name_project.max'    => 'Nama project maksimal 255 karakter.',

            'karyawan_ids.required' => 'Pilih minimal satu karyawan.',
            'karyawan_ids.array'    => 'Format data karyawan tidak valid.',
            'karyawan_ids.*.exists' => 'Karyawan yang dipilih tidak ditemukan.',

            'start_date_project.required' => 'Tanggal mulai wajib diisi.',
            'start_date_project.date'     => 'Tanggal mulai tidak valid.',

            'finish_date_project.required' => 'Tanggal selesai wajib diisi.',
            'finish_date_project.date'     => 'Tanggal selesai tidak valid.',
            'finish_date_project.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ];
    }
}
