<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\MOdels\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('manager')
        ]);
        $manager->assignRole('manager');

        $karyawan = User::create([
            'name' => 'Karyawan',
            'email' => 'karyawan@example.com',
            'password' => Hash::make('karyawan')
        ]);
        $karyawan->assignRole('karyawan');

        $client = User::create([
            'name' => 'client',
            'email' => 'client@example.com',
            'password' => Hash::make('client')
        ]);
        $client->assignRole('client');

    }
}
