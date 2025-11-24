<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    ProjectController,
    UserController,
    KaryawanController,
    ClientController,
    ProjectRequestController,
    TaskController,
    TaskLogController,
};

Route::get('/', fn() => view('auth.login'));

/**
 * Route Dashboard 
 */
Route::get('/dashboard', function () {
    $user = auth()->user();
    $role = $user->getRoleNames()->first();

    return match ($role) {
        'manager' => redirect()->route('manager.dashboard'),
        'karyawan' => redirect()->route('karyawan.tasks.index'),
        'client' => redirect()->route('clients.project-requests.index'),
        default => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Route Profile
 */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * Route Manager
 */
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/karyawan/{id}/project', [DashboardController::class, 'showKaryawanProject'])->name('karyawans.project');
        Route::get('/project-detail/{id}', [DashboardController::class, 'getProjectDetail'])->name('project-detail');

        // Resource Management
        Route::resource('users', UserController::class);
        Route::resource('karyawans', KaryawanController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('projects', ProjectController::class);

        // Project Approve
        Route::get('projects/create/{requestId}', [ProjectController::class, 'create'])->name('projects.create.from.request');
        Route::resource('projects', ProjectController::class);

        //  Project Request 
        Route::resource('tasks', TaskController::class);
        Route::resource('project-request', ProjectRequestController::class);

        // API untuk ambil daftar karyawan berdasarkan task
        Route::get('/manager/karyawan-task-status/{type}', function ($type) {

            if ($type === 'with-task') {
                $karyawans = \App\Models\Karyawan::whereHas('tasks')->get();
            } else {
                $karyawans = \App\Models\Karyawan::whereDoesntHave('tasks')->get();
            }
            return response()->json($karyawans);
        });
    });

Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        // Task Management
        Route::resource('tasks', TaskController::class);
        Route::get('tasks/{task}/log', [TaskLogController::class, 'show'])->name('tasks.logs');
        Route::delete('tasks/logs/{log}', [TaskLogController::class, 'destroy'])->name('tasks.logs.destroy');
    });

Route::middleware(['auth', 'role:client'])
    ->prefix('clients')
    ->name('clients.')
    ->group(function () {
        Route::resource('project-requests', ProjectRequestController::class);
    });

// ========================
// 🔹 AUTH ROUTES (Laravel Breeze / Fortify)
// ========================
require __DIR__ . '/auth.php';
