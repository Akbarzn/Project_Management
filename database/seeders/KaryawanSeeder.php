<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash; // Wajib: Import Hash untuk enkripsi password

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar 5 data karyawan yang akan dibuat
        $employees = [
            [
                'name' => 'Budi Analis',
                'jabatan' => 'Supervisor',
                'phone' => '081234567801',
                'nik' => '1111000001',
                'job_title' => 'Analisis Proses Bisnis',
                'cost' => 8000000,
            ],
            [
                'name' => 'Citra Database',
                'jabatan' => 'Staff',
                'phone' => '081234567802',
                'nik' => '1111000002',
                'job_title' => 'Database Functional',
                'cost' => 5500000,
            ],
            [
                'name' => 'Dedi Programmer',
                'jabatan' => 'Specialist',
                'phone' => '081234567803',
                'nik' => '1111000003',
                'job_title' => 'Programmer',
                'cost' => 9000000,
            ],
            [
                'name' => 'Eka Quality Test',
                'jabatan' => 'Manager',
                'phone' => '081234567804',
                'nik' => '1111000004',
                'job_title' => 'Quality Test',
                'cost' => 12000000,
            ],
            [
                'name' => 'Fajar Sysadmin',
                'jabatan' => 'Intern',
                'phone' => '081234567805',
                'nik' => '1111000005',
                'job_title' => 'SysAdmin',
                'cost' => 3000000,
            ],
        ];

        // Buat setiap data user dan karyawan yang berkorespondensi
        foreach ($employees as $employeeData) {
            // 1. Buat email unik berdasarkan nama (misal: budi.supervisor@company.com)
            $email = strtolower(str_replace(' ', '.', $employeeData['name'])) . '@company.com';

            // 2. Buat record User
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $email,
                'password' => Hash::make('password'), // Password default untuk semua user: 'password'
            ]);

            // 3. Buat record Karyawan dan tautkan ke user yang baru dibuat
            Karyawan::create(array_merge($employeeData, [
                'user_id' => $user->id,
            ]));

            echo "Created User and Karyawan for: " . $employeeData['name'] . " with email: " . $email . "\n";
        }

        echo "Successfully created 5 User and 5 Karyawan records.\n";
    }
}
