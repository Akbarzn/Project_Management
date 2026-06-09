<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Seed 30 karyawan ke database.
     *
     * Distribusi:
     * - 5 role × 6 karyawan = 30 karyawan
     * - Setiap role memiliki variasi level: Junior, Junior, Intermediate, Intermediate, Senior, Lead
     * - Setiap karyawan memiliki skill set yang relevan dengan role-nya
     *
     * Roles (sesuai LeastLoadAssignmentService::ROLES):
     * 1. Business Analyst
     * 2. Programmer
     * 3. Database Functional
     * 4. Quality Test
     * 5. SysAdmin
     */
    public function run(): void
    {
        $employees = [
            // ═══════════════════════════════════════════
            // ROLE 1: Business Analyst (6 orang)
            // ═══════════════════════════════════════════
            [
                'name'      => 'Budi Analis',
                'jabatan'   => 'Staff',
                'phone'     => '081234567801',
                'nik'       => '3273010101900001',
                'job_title' => 'Business Analyst',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['Business Analysis', 'Requirement Gathering', 'UML'],
            ],
            [
                'name'      => 'Sari Permata',
                'jabatan'   => 'Staff',
                'phone'     => '081234567802',
                'nik'       => '3273010101900002',
                'job_title' => 'Business Analyst',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['Business Analysis', 'Flowchart', 'Documentation'],
            ],
            [
                'name'      => 'Rina Wijaya',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567803',
                'nik'       => '3273010101900003',
                'job_title' => 'Business Analyst',
                'level'     => 'Intermediate',
                'cost'      => 200000,
                'skills'    => ['Business Analysis', 'BPMN', 'Stakeholder Management'],
            ],
            [
                'name'      => 'Andi Prasetyo',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567804',
                'nik'       => '3273010101900004',
                'job_title' => 'Business Analyst',
                'level'     => 'Intermediate',
                'cost'      => 220000,
                'skills'    => ['Business Analysis', 'Use Case', 'ERD', 'BPMN'],
            ],
            [
                'name'      => 'Dewi Lestari',
                'jabatan'   => 'Specialist',
                'phone'     => '081234567805',
                'nik'       => '3273010101900005',
                'job_title' => 'Business Analyst',
                'level'     => 'Senior',
                'cost'      => 300000,
                'skills'    => ['Business Analysis', 'BPMN', 'UML', 'Agile', 'Scrum'],
            ],
            [
                'name'      => 'Hendra Kusuma',
                'jabatan'   => 'Manager',
                'phone'     => '081234567806',
                'nik'       => '3273010101900006',
                'job_title' => 'Business Analyst',
                'level'     => 'Lead',
                'cost'      => 400000,
                'skills'    => ['Business Analysis', 'Project Management', 'Strategic Planning', 'BPMN'],
            ],

            // ═══════════════════════════════════════════
            // ROLE 2: Programmer (6 orang)
            // ═══════════════════════════════════════════
            [
                'name'      => 'Charlie Dev',
                'jabatan'   => 'Staff',
                'phone'     => '081234567807',
                'nik'       => '3273010101900007',
                'job_title' => 'Programmer',
                'level'     => 'Junior',
                'cost'      => 180000,
                'skills'    => ['PHP', 'HTML', 'CSS', 'JavaScript'],
            ],
            [
                'name'      => 'Fajar Nugroho',
                'jabatan'   => 'Staff',
                'phone'     => '081234567808',
                'nik'       => '3273010101900008',
                'job_title' => 'Programmer',
                'level'     => 'Junior',
                'cost'      => 180000,
                'skills'    => ['Python', 'Django', 'HTML', 'CSS'],
            ],
            [
                'name'      => 'Dedi Programmer',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567809',
                'nik'       => '3273010101900009',
                'job_title' => 'Programmer',
                'level'     => 'Intermediate',
                'cost'      => 250000,
                'skills'    => ['Laravel', 'PHP', 'Vue.js', 'MySQL'],
            ],
            [
                'name'      => 'Gilang Ramadhan',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567810',
                'nik'       => '3273010101900010',
                'job_title' => 'Programmer',
                'level'     => 'Intermediate',
                'cost'      => 250000,
                'skills'    => ['Laravel', 'React', 'Node.js', 'REST API'],
            ],
            [
                'name'      => 'Irfan Hakim',
                'jabatan'   => 'Specialist',
                'phone'     => '081234567811',
                'nik'       => '3273010101900011',
                'job_title' => 'Programmer',
                'level'     => 'Senior',
                'cost'      => 350000,
                'skills'    => ['Laravel', 'PHP', 'Vue.js', 'Docker', 'CI/CD'],
            ],
            [
                'name'      => 'Kevin Saputra',
                'jabatan'   => 'Manager',
                'phone'     => '081234567812',
                'nik'       => '3273010101900012',
                'job_title' => 'Programmer',
                'level'     => 'Lead',
                'cost'      => 450000,
                'skills'    => ['System Architecture', 'Laravel', 'Microservices', 'DevOps', 'AWS'],
            ],

            // ═══════════════════════════════════════════
            // ROLE 3: Database Functional (6 orang)
            // ═══════════════════════════════════════════
            [
                'name'      => 'Lina Database',
                'jabatan'   => 'Staff',
                'phone'     => '081234567813',
                'nik'       => '3273010101900013',
                'job_title' => 'Database Functional',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['MySQL', 'SQL Basics', 'Data Entry'],
            ],
            [
                'name'      => 'Maulana Rizki',
                'jabatan'   => 'Staff',
                'phone'     => '081234567814',
                'nik'       => '3273010101900014',
                'job_title' => 'Database Functional',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['PostgreSQL', 'SQL', 'Data Migration'],
            ],
            [
                'name'      => 'Citra Database',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567815',
                'nik'       => '3273010101900015',
                'job_title' => 'Database Functional',
                'level'     => 'Intermediate',
                'cost'      => 200000,
                'skills'    => ['MySQL', 'PostgreSQL', 'Database Design'],
            ],
            [
                'name'      => 'Nanda Putri',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567816',
                'nik'       => '3273010101900016',
                'job_title' => 'Database Functional',
                'level'     => 'Intermediate',
                'cost'      => 220000,
                'skills'    => ['MySQL', 'MongoDB', 'Redis', 'ERD'],
            ],
            [
                'name'      => 'Bob DB Senior',
                'jabatan'   => 'Specialist',
                'phone'     => '081234567817',
                'nik'       => '3273010101900017',
                'job_title' => 'Database Functional',
                'level'     => 'Senior',
                'cost'      => 350000,
                'skills'    => ['Oracle', 'PostgreSQL', 'Query Optimization', 'Database Administration'],
            ],
            [
                'name'      => 'Oscar Firmansyah',
                'jabatan'   => 'Manager',
                'phone'     => '081234567818',
                'nik'       => '3273010101900018',
                'job_title' => 'Database Functional',
                'level'     => 'Lead',
                'cost'      => 400000,
                'skills'    => ['Database Architecture', 'Oracle', 'PostgreSQL', 'Performance Tuning', 'Sharding'],
            ],

            // ═══════════════════════════════════════════
            // ROLE 4: Quality Test (6 orang)
            // ═══════════════════════════════════════════
            [
                'name'      => 'Diana QA',
                'jabatan'   => 'Staff',
                'phone'     => '081234567819',
                'nik'       => '3273010101900019',
                'job_title' => 'Quality Test',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['Manual Testing', 'Test Case Writing', 'Bug Reporting'],
            ],
            [
                'name'      => 'Putri Handayani',
                'jabatan'   => 'Staff',
                'phone'     => '081234567820',
                'nik'       => '3273010101900020',
                'job_title' => 'Quality Test',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['Manual Testing', 'Regression Testing', 'Postman'],
            ],
            [
                'name'      => 'Eka Quality Test',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567821',
                'nik'       => '3273010101900021',
                'job_title' => 'Quality Test',
                'level'     => 'Intermediate',
                'cost'      => 200000,
                'skills'    => ['Selenium', 'API Testing', 'Test Automation', 'Postman'],
            ],
            [
                'name'      => 'Qori Amalia',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567822',
                'nik'       => '3273010101900022',
                'job_title' => 'Quality Test',
                'level'     => 'Intermediate',
                'cost'      => 220000,
                'skills'    => ['Cypress', 'Performance Testing', 'JMeter', 'API Testing'],
            ],
            [
                'name'      => 'Reza Mahendra',
                'jabatan'   => 'Specialist',
                'phone'     => '081234567823',
                'nik'       => '3273010101900023',
                'job_title' => 'Quality Test',
                'level'     => 'Senior',
                'cost'      => 300000,
                'skills'    => ['Test Strategy', 'Selenium', 'CI/CD Testing', 'Load Testing'],
            ],
            [
                'name'      => 'Sinta Dewi',
                'jabatan'   => 'Manager',
                'phone'     => '081234567824',
                'nik'       => '3273010101900024',
                'job_title' => 'Quality Test',
                'level'     => 'Lead',
                'cost'      => 380000,
                'skills'    => ['QA Strategy', 'Test Architecture', 'Team Management', 'DevOps Testing'],
            ],

            // ═══════════════════════════════════════════
            // ROLE 5: SysAdmin (6 orang)
            // ═══════════════════════════════════════════
            [
                'name'      => 'Fajar Sysadmin',
                'jabatan'   => 'Staff',
                'phone'     => '081234567825',
                'nik'       => '3273010101900025',
                'job_title' => 'SysAdmin',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['Linux', 'Ubuntu', 'Basic Networking'],
            ],
            [
                'name'      => 'Taufik Hidayat',
                'jabatan'   => 'Staff',
                'phone'     => '081234567826',
                'nik'       => '3273010101900026',
                'job_title' => 'SysAdmin',
                'level'     => 'Junior',
                'cost'      => 150000,
                'skills'    => ['Linux', 'Windows Server', 'DNS'],
            ],
            [
                'name'      => 'Umar Fadillah',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567827',
                'nik'       => '3273010101900027',
                'job_title' => 'SysAdmin',
                'level'     => 'Intermediate',
                'cost'      => 200000,
                'skills'    => ['Linux', 'Docker', 'Nginx', 'Firewall'],
            ],
            [
                'name'      => 'Vina Oktaviani',
                'jabatan'   => 'Supervisor',
                'phone'     => '081234567828',
                'nik'       => '3273010101900028',
                'job_title' => 'SysAdmin',
                'level'     => 'Intermediate',
                'cost'      => 220000,
                'skills'    => ['AWS', 'Linux', 'Monitoring', 'Grafana'],
            ],
            [
                'name'      => 'Ethan Admin',
                'jabatan'   => 'Specialist',
                'phone'     => '081234567829',
                'nik'       => '3273010101900029',
                'job_title' => 'SysAdmin',
                'level'     => 'Senior',
                'cost'      => 300000,
                'skills'    => ['AWS', 'Docker', 'Kubernetes', 'CI/CD', 'Terraform'],
            ],
            [
                'name'      => 'Wawan Setiawan',
                'jabatan'   => 'Manager',
                'phone'     => '081234567830',
                'nik'       => '3273010101900030',
                'job_title' => 'SysAdmin',
                'level'     => 'Lead',
                'cost'      => 400000,
                'skills'    => ['Cloud Architecture', 'AWS', 'GCP', 'Infrastructure as Code', 'Security'],
            ],
        ];

        foreach ($employees as $employeeData) {
            // Buat email otomatis dari nama
            $email = strtolower(str_replace(' ', '.', $employeeData['name'])) . '@company.com';

            // Buat user
            $user = User::create([
                'name'          => $employeeData['name'],
                'email'         => $email,
                'password'      => Hash::make('password'),
                'potho_profile' => 'images/default.jpg',
            ]);
            $user->assignRole('karyawan');

            // Buat data karyawan
            Karyawan::create(array_merge($employeeData, [
                'user_id'      => $user->id,
                'max_workload' => 40, // Legacy field, tidak dipakai di logika baru
            ]));
        }

        echo "✅ Berhasil membuat 30 User dan 30 Karyawan.\n";
        echo "   📋 Distribusi: 5 Role × 6 Karyawan\n";
        echo "   📊 Level per role: 2 Junior, 2 Intermediate, 1 Senior, 1 Lead\n";
    }
}
