<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Project Repository
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Eloquent\ProjectRepository;

// Karyawan Repository
use App\Repositories\Contracts\KaryawanRepositoryInterface;
use App\Repositories\Eloquent\KaryawanRepository;

// Workload Repository (untuk kalkulasi workload karyawan)
use App\Repositories\Contracts\WorkloadRepositoryInterface;
use App\Repositories\Eloquent\WorkloadRepository;

// AutoAssignment Repository (untuk Full Automatic Assignment)
use App\Repositories\Contracts\AutoAssignmentRepositoryInterface;
use App\Repositories\Eloquent\AutoAssignmentRepository;

/**
 * RepositoryServiceProvider
 *
 * Mendaftarkan semua binding Interface → Implementasi ke IoC Container Laravel.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Binding ProjectRepository
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);

        // Binding KaryawanRepository
        $this->app->bind(KaryawanRepositoryInterface::class, KaryawanRepository::class);

        // Binding WorkloadRepository
        $this->app->bind(WorkloadRepositoryInterface::class, WorkloadRepository::class);

        // Binding AutoAssignmentRepository
        $this->app->bind(AutoAssignmentRepositoryInterface::class, AutoAssignmentRepository::class);
    }

    public function boot(): void {}
}