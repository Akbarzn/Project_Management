<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * KaryawanFactory
 *
 * Factory untuk generate data karyawan saat testing.
 * Menyediakan berbagai state/skenario sesuai kebutuhan test:
 * - Default     : Karyawan normal, belum overload
 * - withSkill() : Karyawan dengan skill spesifik
 * - overloaded(): Karyawan dengan max_workload sangat kecil (mudah overload)
 * - fresh()     : Karyawan baru, workload = 0
 */
class KaryawanFactory extends Factory
{
    protected $model = Karyawan::class;

    /**
     * State default: karyawan normal tanpa skill khusus.
     * max_workload default 40 jam sesuai standar bisnis.
     */
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'name'         => $this->faker->name(),
            'nik'          => $this->faker->unique()->numerify('################'),
            'jabatan'      => $this->faker->randomElement(['Staff', 'Supervisor', 'Specialist']),
            'phone'        => $this->faker->phoneNumber(),
            'job_title'    => $this->faker->jobTitle(),
            'cost'         => $this->faker->randomElement([150000, 200000, 250000, 300000]),
            'skills'       => null,      // Tanpa skill (fallback scenario)
            'max_workload' => 40,        // Kapasitas default 40 jam
        ];
    }

    /**
     * State: Karyawan dengan skill Laravel.
     *
     * Digunakan untuk test skill matching.
     * Pastikan karyawan ini yang diprioritaskan saat project butuh Laravel.
     *
     * Contoh pemakaian:
     *   Karyawan::factory()->withSkill('Laravel')->create();
     */
    public function withSkill(string $skill): static
    {
        return $this->state(fn() => [
            'skills' => [$skill],
        ]);
    }

    /**
     * State: Karyawan dengan beberapa skill sekaligus.
     *
     * Contoh pemakaian:
     *   Karyawan::factory()->withSkills(['Laravel', 'PHP', 'MySQL'])->create();
     */
    public function withSkills(array $skills): static
    {
        return $this->state(fn() => [
            'skills' => $skills,
        ]);
    }

    /**
     * State: Karyawan dengan max_workload sangat kecil (1 jam).
     *
     * Digunakan untuk test overload validation.
     * Karyawan ini akan langsung overload begitu ada task/project aktif.
     *
     * Contoh pemakaian:
     *   Karyawan::factory()->overloadable()->create();
     */
    public function overloadable(): static
    {
        return $this->state(fn() => [
            'max_workload' => 1, // Mudah overload, threshold sangat kecil
        ]);
    }

    /**
     * State: Karyawan baru tanpa beban kerja apapun.
     * max_workload tinggi (100 jam) agar tidak pernah overload dalam testing.
     *
     * Digunakan untuk test assign ke karyawan yang benar-benar free.
     */
    public function fresh(): static
    {
        return $this->state(fn() => [
            'skills'       => null,
            'max_workload' => 100,
        ]);
    }

    /**
     * State: Karyawan Laravel developer dengan kapasitas normal.
     * Siap digunakan sebagai kandidat ideal dalam test auto-assignment.
     */
    public function laravelDeveloper(): static
    {
        return $this->state(fn() => [
            'job_title'    => 'Laravel Developer',
            'skills'       => ['Laravel', 'PHP', 'MySQL'],
            'max_workload' => 40,
        ]);
    }
}
