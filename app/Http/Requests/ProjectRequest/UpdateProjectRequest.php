<?php

namespace App\Http\Requests\ProjectRequest;

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
        $user = auth()->user();
        return [
            'name_project' => ['sometimes', 'string'],
            'kategori' => ['sometimes', 'string', 'in:New Aplikasi,Update Aplikasi'],
            'description' => ['sometimes', 'string'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:2048'],
            'status' => ['sometimes', 'in:pending,approve,rejected'],

            //  client tidak boleh mengirim client_id
            // manager bisa ubah client_id
            'client_id' => $user->hasRole('manager')
                ? 'required|exists:clients,id'
                : 'prohibited' 

        ];
    }

    public function messages(): array
    {
        return [
            'name_project.max' => 'Nama project maksimal 250 karakter.',

            'kategori.in' => 'Kategori harus salah satu: New Aplikasi atau Update Aplikasi.',

            'document.mimes' => 'Dokumen harus berupa PDF, DOC, DOCX, PNG, JPG, atau JPEG.',
            'document.max'   => 'Dokumen maksimal 2 MB.',

            'status.in' => 'Status tidak valid.',

            'client_id.exists'   => 'Client tidak ditemukan.',
            'client_id.prohibited' => 'Anda tidak boleh mengubah client_id.',
        ];
    }
}
