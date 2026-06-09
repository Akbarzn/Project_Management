<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * WorkloadTestSeeder
 *
 * Seeder khusus untuk keperluan testing algoritma Workload Balancing.
 * JANGAN dijalankan di production, hanya untuk environment testing.
 *
 * Membuat data test yang representatif dengan skenario workload berbeda:
 * ┌─────────────────┬────────────┬────────────┬────────────────┬────────────┐
 * │ Karyawan        │ Jam Aktif  │ Project    │ Workload Score │ Max        │
 * │                 │ (task log) │ Aktif      │ (jam + proj×5) │ Workload   │
 * ├─────────────────┼────────────┼────────────┼────────────────┼────────────┤
 * │ Alice Laravel   │ 10 jam     │ 1 ongoing  │ 10+5 = 15      │ 40         │
 * │ Budi PHP        │ 25 jam     │ 2 ongoing  │ 25+10 = 35     │ 40         │
 * │ Citra MySQL     │ 0 jam      │ 0 project  │ 0+0 = 0        │ 40         │ ← TERINGAN
 * │ Dodi Overloaded │ 30 jam     │ 3 ongoing  │ 30+15 = 45     │ 40         │ ← OVERLOADED
 * │ Eka Multi-Skill │ 5 jam      │ 1 ongoing  │ 5+5 = 10       │ 40         │
 * └─────────────────┴────────────┴────────────┴────────────────┴────────────┘
 *
 * Penggunaan di test:
 *   $this->seed(WorkloadTestSeeder::class);
 */
class WorkloadTestSeeder extends Seeder
{
    /**
     * Simpan referensi model agar test dapat mengakses ID-nya.
     * Akses via: WorkloadTestSeeder::$karyawan['alice']->id
     */
    public static array $karyawan  = [];
    public static array $projects  = [];

    public function run(): void
    {
        // ─── Setup Roles ──────────────────────────────────────────────────────
        // Pastikan role sudah ada (biasanya di-seed oleh RoleSeeder)
        $this->call(RoleSeeder::class);

        // ─── Buat Client (diperlukan oleh Project) ────────────────────────────
        $clientUser = User::create([
            'name'     => 'Client Test',
            'email'    => 'client.test@test.com',
            'password' => Hash::make('password'),
        ]);

        $client = Client::create([
            'user_id'         => $clientUser->id,
            'name'            => 'PT Test Client',
            'nik'             => '1234567890',
            'kode_organisasi' => 'ORG-TEST',
            'phone'           => '08123456789',
        ]);

        // ─── Buat Projects ────────────────────────────────────────────────────

        // Project A: ongoing, butuh skill Laravel
        $projectA = Project::create([
            'client_id'           => $client->id,
            'start_date_project'  => now()->subDays(30)->toDateString(),
            'finish_date_project' => now()->addDays(30)->toDateString(),
            'status'              => 'ongoing',
            'required_skill'      => 'Laravel',
            'difficulty'          => 3,
            'estimated_hours'     => 120,
            'is_approved'         => true,
        ]);

        // Project B: ongoing, tanpa skill khusus
        $projectB = Project::create([
            'client_id'           => $client->id,
            'start_date_project'  => now()->subDays(15)->toDateString(),
            'finish_date_project' => now()->addDays(45)->toDateString(),
            'status'              => 'ongoing',
            'required_skill'      => null,
            'difficulty'          => 2,
            'estimated_hours'     => 80,
            'is_approved'         => true,
        ]);

        // Project C: Target project untuk testing assignment
        $projectTarget = Project::create([
            'client_id'           => $client->id,
            'start_date_project'  => now()->toDateString(),
            'finish_date_project' => now()->addDays(60)->toDateString(),
            'status'              => 'ongoing',
            'required_skill'      => 'Laravel',
            'difficulty'          => 2,
            'estimated_hours'     => 100,
            'is_approved'         => true,
        ]);

        static::$projects = [
            'a'      => $projectA,
            'b'      => $projectB,
            'target' => $projectTarget,
        ];

        // ─── Buat Karyawan ────────────────────────────────────────────────────

        // 1. Alice: Laravel dev, workload = 10 + (1×5) = 15
        $aliceUser = User::create([
            'name'     => 'Alice Laravel',
            'email'    => 'alice@test.com',
            'password' => Hash::make('password'),
        ]);
        $alice = Karyawan::create([
            'user_id'      => $aliceUser->id,
            'name'         => 'Alice Laravel',
            'nik'          => '1111111111111111',
            'jabatan'      => 'Specialist',
            'phone'        => '081111111111',
            'job_title'    => 'Laravel Developer',
            'cost'         => 300000,
            'skills'       => ['Laravel', 'PHP'],
            'max_workload' => 40,
        ]);

        // Attach Alice ke project A (1 ongoing project → penalti 5 jam)
        DB::table('karyawan_projects')->insert([
            'project_id'         => $projectA->id,
            'karyawan_id'        => $alice->id,
            'cost_snapshot'      => 300000,
            'job_title_snapshot' => 'Laravel Developer',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Alice punya task aktif dengan 10 jam work log
        $aliceTask = DB::table('tasks')->insertGetId([
            'project_id'  => $projectA->id,
            'karyawan_id' => $alice->id,
            'progress'    => 25,
            'status'      => 'inwork',
            'catatan'     => 'Pengembangan fitur login',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('task_work_logs')->insert([
            'task_id'     => $aliceTask,
            'karyawan_id' => $alice->id,
            'work_date'   => now()->toDateString(),
            'hours'       => 10.0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        // Alice workload = 10 + (1×5) = 15 jam

        // 2. Budi: PHP dev, workload = 25 + (2×5) = 35
        $budiUser = User::create([
            'name'     => 'Budi PHP',
            'email'    => 'budi@test.com',
            'password' => Hash::make('password'),
        ]);
        $budi = Karyawan::create([
            'user_id'      => $budiUser->id,
            'name'         => 'Budi PHP',
            'nik'          => '2222222222222222',
            'jabatan'      => 'Staff',
            'phone'        => '082222222222',
            'job_title'    => 'PHP Developer',
            'cost'         => 250000,
            'skills'       => ['PHP', 'MySQL'],
            'max_workload' => 40,
        ]);

        foreach ([$projectA, $projectB] as $p) {
            DB::table('karyawan_projects')->insert([
                'project_id'         => $p->id,
                'karyawan_id'        => $budi->id,
                'cost_snapshot'      => 250000,
                'job_title_snapshot' => 'PHP Developer',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
        $budiTask = DB::table('tasks')->insertGetId([
            'project_id'  => $projectA->id,
            'karyawan_id' => $budi->id,
            'progress'    => 50,
            'status'      => 'inwork',
            'catatan'     => 'Integrasi database',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('task_work_logs')->insert([
            'task_id'     => $budiTask,
            'karyawan_id' => $budi->id,
            'work_date'   => now()->toDateString(),
            'hours'       => 25.0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        // Budi workload = 25 + (2×5) = 35 jam

        // 3. Citra: fresh, workload = 0 (kandidat terbaik)
        $citraUser = User::create([
            'name'     => 'Citra Fresh',
            'email'    => 'citra@test.com',
            'password' => Hash::make('password'),
        ]);
        $citra = Karyawan::create([
            'user_id'      => $citraUser->id,
            'name'         => 'Citra Fresh',
            'nik'          => '3333333333333333',
            'jabatan'      => 'Staff',
            'phone'        => '083333333333',
            'job_title'    => 'Full Stack Developer',
            'cost'         => 200000,
            'skills'       => ['Laravel', 'Vue.js'],
            'max_workload' => 40,
        ]);
        // Citra: tidak ada task, tidak ada project → workload = 0

        // 4. Dodi: overloaded (workload=45 >= max=40)
        $dodiUser = User::create([
            'name'     => 'Dodi Overloaded',
            'email'    => 'dodi@test.com',
            'password' => Hash::make('password'),
        ]);
        $dodi = Karyawan::create([
            'user_id'      => $dodiUser->id,
            'name'         => 'Dodi Overloaded',
            'nik'          => '4444444444444444',
            'jabatan'      => 'Supervisor',
            'phone'        => '084444444444',
            'job_title'    => 'Tech Lead',
            'cost'         => 400000,
            'skills'       => ['Laravel', 'PHP', 'MySQL', 'Docker'],
            'max_workload' => 40,
        ]);

        // Dodi di 3 project ongoing
        foreach ([$projectA, $projectB] as $p) {
            DB::table('karyawan_projects')->insert([
                'project_id'         => $p->id,
                'karyawan_id'        => $dodi->id,
                'cost_snapshot'      => 400000,
                'job_title_snapshot' => 'Tech Lead',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        // Buat extra project untuk dodi
        $extraProject = Project::create([
            'client_id'           => $client->id,
            'start_date_project'  => now()->subDays(5)->toDateString(),
            'finish_date_project' => now()->addDays(25)->toDateString(),
            'status'              => 'ongoing',
            'required_skill'      => null,
            'difficulty'          => 4,
            'estimated_hours'     => 160,
            'is_approved'         => true,
        ]);
        DB::table('karyawan_projects')->insert([
            'project_id'         => $extraProject->id,
            'karyawan_id'        => $dodi->id,
            'cost_snapshot'      => 400000,
            'job_title_snapshot' => 'Tech Lead',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $dodiTask = DB::table('tasks')->insertGetId([
            'project_id'  => $projectA->id,
            'karyawan_id' => $dodi->id,
            'progress'    => 70,
            'status'      => 'inwork',
            'catatan'     => 'Review arsitektur sistem',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('task_work_logs')->insert([
            'task_id'     => $dodiTask,
            'karyawan_id' => $dodi->id,
            'work_date'   => now()->toDateString(),
            'hours'       => 30.0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        // Dodi workload = 30 + (3×5) = 45 >= max=40 → OVERLOADED

        // 5. Eka: multi-skill, workload = 5 + (1×5) = 10
        $ekaUser = User::create([
            'name'     => 'Eka Multi',
            'email'    => 'eka@test.com',
            'password' => Hash::make('password'),
        ]);
        $eka = Karyawan::create([
            'user_id'      => $ekaUser->id,
            'name'         => 'Eka Multi',
            'nik'          => '5555555555555555',
            'jabatan'      => 'Staff',
            'phone'        => '085555555555',
            'job_title'    => 'Backend Developer',
            'cost'         => 275000,
            'skills'       => ['Laravel', 'PHP', 'Docker', 'Redis'],
            'max_workload' => 40,
        ]);

        DB::table('karyawan_projects')->insert([
            'project_id'         => $projectB->id,
            'karyawan_id'        => $eka->id,
            'cost_snapshot'      => 275000,
            'job_title_snapshot' => 'Backend Developer',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
        $ekaTask = DB::table('tasks')->insertGetId([
            'project_id'  => $projectB->id,
            'karyawan_id' => $eka->id,
            'progress'    => 10,
            'status'      => 'pending',
            'catatan'     => 'Setup environment',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        DB::table('task_work_logs')->insert([
            'task_id'     => $ekaTask,
            'karyawan_id' => $eka->id,
            'work_date'   => now()->toDateString(),
            'hours'       => 5.0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        // Eka workload = 5 + (1×5) = 10 jam

        // Simpan referensi untuk diakses dari test
        static::$karyawan = [
            'alice' => $alice,  // workload=15, skill=Laravel
            'budi'  => $budi,   // workload=35, skill=PHP
            'citra' => $citra,  // workload=0, skill=Laravel ← TERINGAN
            'dodi'  => $dodi,   // workload=45 → OVERLOADED
            'eka'   => $eka,    // workload=10, skill=Laravel
        ];

        $this->command?->info('WorkloadTestSeeder: Data berhasil dibuat.');
        $this->command?->table(
            ['Karyawan', 'Jam Aktif', 'Project Aktif', 'Workload', 'Status'],
            [
                ['Alice', '10', '1', '15', 'Available'],
                ['Budi',  '25', '2', '35', 'Available'],
                ['Citra', '0',  '0', '0',  'Available (TERINGAN)'],
                ['Dodi',  '30', '3', '45', 'OVERLOADED'],
                ['Eka',   '5',  '1', '10', 'Available'],
            ]
        );
    }
}
