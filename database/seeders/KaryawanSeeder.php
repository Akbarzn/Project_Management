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
                'nik' => '3273010101900001',
                'job_title' => 'Analisis Proses Bisnis',
                'cost' => 200000,
            ],
            [
                'name' => 'Citra Database',
                'jabatan' => 'Staff',
                'phone' => '081234567802',
                'nik' => '3273010101900002',
                'job_title' => 'Database Functional',
                'cost' => 200000,
            ],
            [
                'name' => 'Dedi Programmer',
                'jabatan' => 'Specialist',
                'phone' => '081234567803',
                'nik' => '3273010101900003',
                'job_title' => 'Programmer',
                'cost' => 300000,
            ],
            [
                'name' => 'Eka Quality Test',
                'jabatan' => 'Manager',
                'phone' => '081234567804',
                'nik' => '3273010101900004',
                'job_title' => 'Quality Test',
                'cost' => 200000,
            ],
            [
                'name' => 'Fajar Sysadmin',
                'jabatan' => 'Intern',
                'phone' => '081234567805',
                'nik' => '3273010101900005',
                'job_title' => 'SysAdmin',
                'cost' => 200000,
            ],
        ];

        foreach ($employees as $employeeData) {
            $email = strtolower(str_replace(' ', '.', $employeeData['name'])) . '@company.com';

            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $email,
                'password' => Hash::make('password'),
                'potho_profile' => 'images/default.jpg'
            ]);

            Karyawan::create(array_merge($employeeData, [
                'user_id' => $user->id,
            ]));

            echo "Created User and Karyawan for: " . $employeeData['name'] . " with email: " . $email . "\n";
        }

        echo "Successfully created 5 User and 5 Karyawan records.\n";
    }
    
}
