<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkRequest extends FormRequest
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
            'work_hours_today' => ['nullable', 'numeric', 'min:0', 'max:7'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'description_task' => ['nullable', 'string', 'max:2000'],
        ];
    }

     public function messages(): array
    {
        return [
            'work_hours_today.numeric' => 'Jam kerja harus berupa angka.',
            'work_hours_today.min'     => 'Jam kerja minimal 0 jam.',
            'work_hours_today.max'     => 'Jam kerja maksimal 7 jam.',

            'progress.integer' => 'Progress harus berupa angka.',
            'progress.min'     => 'Progress minimal 0%.',
            'progress.max'     => 'Progress maksimal 100%.',

            'description_task.max' => 'Deskripsi task maksimal 2000 karakter.',
        ];
    }
}
