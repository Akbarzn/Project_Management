<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ProjectFactory
 *
 * Factory untuk generate data project saat testing.
 * Menyediakan berbagai state untuk skenario berbeda:
 * - Default         : Project ongoing, tanpa required_skill
 * - requiresSkill() : Project dengan skill spesifik
 * - pending()       : Project belum dimulai
 * - completed()     : Project sudah selesai
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * State default: Project dengan status 'ongoing'.
     * Status ongoing dibutuhkan agar karyawan yang terlibat
     * mendapat penalti workload (+5 jam per project).
     */
    public function definition(): array
    {
        return [
            'client_id'           => Client::factory(),
            'request_id'          => null,
            'start_date_project'  => now()->subDays(10)->toDateString(),
            'finish_date_project' => now()->addDays(20)->toDateString(),
            'status'              => 'ongoing',
            'total_cost'          => null,
            'approved_by'         => null,
            'is_approved'         => true,
            'difficulty'          => $this->faker->randomElement([1, 2, 3]),
            'estimated_hours'     => $this->faker->randomElement([40, 80, 120]),
            'required_skill'      => null,  // Tidak perlu skill khusus (fallback)
        ];
    }

    /**
     * State: Project membutuhkan skill tertentu.
     * Digunakan untuk test skill matching dalam auto-assignment.
     *
     * Contoh:
     *   Project::factory()->requiresSkill('Laravel')->create();
     */
    public function requiresSkill(string $skill): static
    {
        return $this->state(fn() => [
            'required_skill' => $skill,
        ]);
    }

    /**
     * State: Project dengan status 'pending' (belum aktif).
     * Karyawan yang assign ke project ini tidak mendapat penalti workload
     * karena project belum 'ongoing'.
     */
    public function pending(): static
    {
        return $this->state(fn() => [
            'status' => 'pending',
        ]);
    }

    /**
     * State: Project sudah selesai (complete).
     * Digunakan untuk memastikan project complete tidak mempengaruhi workload.
     */
    public function completed(): static
    {
        return $this->state(fn() => [
            'status' => 'complete',
        ]);
    }

    /**
     * State: Project dengan tingkat kesulitan tinggi.
     */
    public function difficult(): static
    {
        return $this->state(fn() => [
            'difficulty'     => 5,
            'estimated_hours' => 200,
        ]);
    }
}
