<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        // ✅ Authorize harus mengembalikan boolean (true atau false).
        // Biasanya dicek apakah karyawan yang login adalah pemilik task ini.
        // Untuk contoh ini, kita kembalikan true agar validasi bisa berjalan.
        return true; 
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan (rules).
     */
    public function rules(): array
    {
        // ✅ Aturan validasi harus berada di sini dan mengembalikan array.
        return [
            'project_id'      => 'nullable|exists:projects,id',
            'progress'        => 'nullable|integer|min:0|max:100',
            
            'desc'     => 'nullable|string|max:500', 
            
            'start_date_task' => 'nullable|date',
        ];
    }

    /**
     * Dapatkan pesan kesalahan validasi khusus.
     */
    public function messages(): array
    {
        return [
            'project_id.exists'     => 'Project tidak valid.',
            'progress.integer'      => 'Progress harus berupa angka.',
            'progress.min'          => 'Progress minimal 0%.',
            'progress.max'          => 'Progress maksimal 100%.',
            
            'desc.max'       => 'Deskripsi/Catatan maksimal 500 karakter.', 
            
            'start_date_task.date'  => 'Tanggal mulai task tidak valid.',
        ];
    }
}