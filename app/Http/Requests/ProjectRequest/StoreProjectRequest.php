<?php

namespace App\Http\Requests\ProjectRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
        $rules = [
            'name_project' => ['required', 'string', 'max:250'],
            'kategori' => ['required', 'string' , 'max:50'],
            'description' => ['required', 'string'],
            'document' => ['nullable', 'file', 'mimes:pdf,doc,docx,png,jpg,jpeg', 'max:2048'],
        ];

        // kalo manager buat request maka wajib pilih client
        if(auth()->user()->hasRole('manager')){
            $rules['client_id'] = ['required', 'exists:clients,id'];
        }
        return $rules;
    }

     public function messages(): array
    {
        return [
            'name_project.required' => 'Nama project wajib diisi.',
            'kategori.required'     => 'Kategori wajib diisi.',
            'description.required'  => 'Deskripsi wajib diisi.',

            'client_id.required' => 'Client wajib dipilih oleh Manager.',
            'client_id.exists'   => 'Client tidak valid.',
            'client_id.prohibited' => 'Client tidak boleh memilih client_id.',

            'document.mimes' => 'Dokumen harus berupa PDF, DOC, DOCX, PNG, JPG, atau JPEG.',
            'document.max'   => 'Ukuran dokumen maksimal 2 MB.',
        ];
    }
}
