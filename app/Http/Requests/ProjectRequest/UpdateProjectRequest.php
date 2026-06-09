<?php

namespace App\Http\Requests\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();

        return [
            'name_project'       => ['sometimes', 'string', 'max:250'],
            'kategori'           => ['sometimes', 'string', 'in:New Aplikasi,Update Aplikasi'],
            'description'        => ['sometimes', 'string'],
            'document'           => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:2048'],
            'status'             => ['sometimes', 'in:pending,approve,rejected'],
            // ─── Kolom baru untuk Auto Assignment ───────────────────────────────
            'priority'           => ['sometimes', 'integer', 'min:1', 'max:4'],
            'difficulty'         => ['sometimes', 'integer', 'min:1', 'max:5'],
            'estimated_duration' => ['nullable', 'integer', 'min:0'],
            // manager bisa ubah client_id, client tidak boleh
            'client_id'          => $user->hasRole('manager')
                ? 'required|exists:clients,id'
                : 'prohibited',
        ];
    }

    public function messages(): array
    {
        return [
            'name_project.max'            => 'Nama project maksimal 250 karakter.',
            'kategori.in'                 => 'Kategori harus salah satu: New Aplikasi atau Update Aplikasi.',

            'priority.integer'            => 'Priority harus berupa angka.',
            'priority.min'                => 'Priority minimal 1 (Low).',
            'priority.max'                => 'Priority maksimal 4 (Critical).',

            'difficulty.integer'          => 'Difficulty harus berupa angka.',
            'difficulty.min'              => 'Difficulty minimal 1 (Sangat Mudah).',
            'difficulty.max'              => 'Difficulty maksimal 5 (Sangat Sulit).',

            'document.mimes'              => 'Dokumen harus berupa PDF, DOC, DOCX, PNG, JPG, atau JPEG.',
            'document.max'                => 'Dokumen maksimal 2 MB.',
            'status.in'                   => 'Status tidak valid.',
            'client_id.exists'            => 'Client tidak ditemukan.',
            'client_id.prohibited'        => 'Anda tidak boleh mengubah client_id.',
        ];
    }
}
