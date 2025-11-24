<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'project_id'      => 'nullable|exists:projects,id',
            'progress'        => 'nullable|integer|min:0|max:100',
            'start_date_task' => 'nullable|date',
            'finish_date_task' => 'nullable|date',
            'hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'catatan'     => 'nullable|string|max:500', 
            
        ];
    }

     public function messages(): array
    {
        return [
            'project_id.exists' => 'Project tidak valid.',

            'progress.integer' => 'Progress harus berupa angka.',
            'progress.min'     => 'Progress minimal 0%.',
            'progress.max'     => 'Progress maksimal 100%.',

            'hours.numeric' => 'Jam kerja harus berupa angka.',
            'hours.max'     => 'Jam kerja maksimal 24 jam.',

            'catatan.max' => 'Catatan maksimal 500 karakter.',

            'start_date_task.date' => 'Tanggal mulai task tidak valid.',
            'finish_date_task.date' => 'Tanggal selesai task tidak valid.',
            'finish_date_task.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ];
    }
}