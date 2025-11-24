<?php

namespace App\Http\Requests\Manager\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'start_date_project' => 'required|string|max:100',
            'finish_date_project' => 'required|date|after_or_equal:start_date_project',
            'karyawan_ids' => 'required|array|size:5',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'request_id' => 'required|exists:project_requests,id',
            'total_cost' => 'nullable'
        ];
    }

    public function messages(): array
    {
        return [
            'start_date_project.required' => 'Tanggal mulai wajib diisi.',
            'start_date_project.date'     => 'Tanggal mulai harus berupa tanggal yang valid.',

            'finish_date_project.required' => 'Tanggal selesai wajib diisi.',
            'finish_date_project.date'     => 'Tanggal selesai harus berupa tanggal yang valid.',
            'finish_date_project.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',

            'karyawan_ids.required' => 'Pilih karyawan minimal dan maksimal 5 orang.',
            'karyawan_ids.array'    => 'Format karyawan tidak valid.',
            'karyawan_ids.size'     => 'Jumlah karyawan harus tepat 5 orang.',
            'karyawan_ids.*.exists' => 'Karyawan yang dipilih tidak ditemukan.',

            'request_id.required' => 'Request ID wajib diisi.',
            'request_id.exists'   => 'Request ID tidak valid.',

            'total_cost.numeric' => 'Total biaya harus berupa angka.',
        ];
    }
}
