<?php

namespace App\Http\Requests\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name_project'       => ['required', 'string', 'max:250'],
            'kategori'           => ['required', 'string', 'max:50'],
            'description'        => ['required', 'string'],
            'document'           => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:2048'],
            // ─── Kolom baru untuk Auto Assignment ───────────────────────────────
            'priority'           => ['required', 'integer', 'min:1', 'max:4'],
            'difficulty'         => ['required', 'integer', 'min:1', 'max:5'],
            'estimated_duration' => ['nullable', 'integer', 'min:0'],
        ];

        if (auth()->user()->hasRole('manager')) {
            $rules['client_id'] = ['required', 'exists:clients,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name_project.required'       => 'Nama project wajib diisi.',
            'kategori.required'           => 'Kategori wajib diisi.',
            'description.required'        => 'Deskripsi wajib diisi.',

            'priority.required'           => 'Priority wajib dipilih.',
            'priority.integer'            => 'Priority harus berupa angka.',
            'priority.min'                => 'Priority minimal 1 (Low).',
            'priority.max'                => 'Priority maksimal 4 (Critical).',

            'difficulty.required'         => 'Difficulty wajib dipilih.',
            'difficulty.integer'          => 'Difficulty harus berupa angka.',
            'difficulty.min'              => 'Difficulty minimal 1 (Sangat Mudah).',
            'difficulty.max'              => 'Difficulty maksimal 5 (Sangat Sulit).',

            'client_id.required'          => 'Client wajib dipilih oleh Manager.',
            'client_id.exists'            => 'Client tidak valid.',

            'document.mimes'              => 'Dokumen harus berupa PDF, DOC, DOCX, PNG, JPG, atau JPEG.',
            'document.max'                => 'Ukuran dokumen maksimal 2 MB.',
        ];
    }
}

