<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CLient;
use App\Models\User;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $user = User::where('email', 'client@example.com')->first();

         Client::create([
            'user_id' => $user->id,
            'name' => 'PT ABC Teknologi',
            'nik' => '221222222222',
            'phone' => '081234567890',
            'kode_organisasi' => 'ORG001',
        ]);

    }
}
