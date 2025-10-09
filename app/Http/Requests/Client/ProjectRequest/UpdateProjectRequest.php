<?php

namespace App\Http\Requests\Client\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'name_project' => ['sometimes', 'string'],
            'kategori' => ['sometimes', 'string', 'in:New Aplikasi, Update Aplikasi'],
            'description' => ['sometimes', 'string'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048'],
            'status' => ['sometimes', 'in:pending,approve,rejected'],
        ];
    }
}
