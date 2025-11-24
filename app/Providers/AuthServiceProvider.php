<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Daftarkan model dan policy kamu di sini
use App\Models\Task;
use App\Policies\TaskPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping antara model dan policy-nya.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Task::class => TaskPolicy::class,
        // Tambahkan policy lain di sini kalau ada
        // Project::class => ProjectPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Contoh Gate tambahan kalau mau custom akses
        Gate::define('is-manager', fn($user) => $user->hasRole('manager'));
        Gate::define('is-karyawan', fn($user) => $user->hasRole('karyawan'));
    }
}
