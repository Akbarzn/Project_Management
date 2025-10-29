<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client')->id ?? null; 

        return [
            'name' => 'required|max:50',
            
            'nik' => 'nullable|unique:clients,nik,' . $clientId, 
            
            'phone' => 'nullable|max:15',
            'kode_organisasi' => 'nullable|max:10',
        ];
    }
}
