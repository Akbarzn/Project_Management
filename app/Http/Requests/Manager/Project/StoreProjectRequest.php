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
            'karyawan_ids' => 'required|array|min:5|max:5',
            'karyawan_ids.*' => 'exists:karyawans,id',
            'request_id' => 'required|exists:project_requests,id'
        ];
    }
}
