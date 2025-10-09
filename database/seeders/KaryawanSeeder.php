<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;      // <-- WAJIB: Tambahkan import untuk Model User
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user = User::where('email', 'karyawan@example.com')->first();

         Karyawan::create([
            'user_id' => $user->id,
            'name' => 'tesKaryawan',
            'jabatan' => 'SuperVisor',
            'phone' => '123456789',
            'nik' => '999999999',
            'job_title' => 'Programer',
            'cost' => 8000000,
        ]);

    }
}
