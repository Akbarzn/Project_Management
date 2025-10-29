<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Manager\UserController;
use App\Http\Controllers\Manager\ProjectController;
use App\Http\Controllers\Karyawan\KaryawanController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\ProjectRequestController;
use App\Http\Controllers\Manager\ProjectRequestController as ManagerProjectRequestController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Semua route utama aplikasi dikelompokkan berdasarkan role:
| - Manager
| - Karyawan
| - Client
| dan otomatis diarahkan ke dashboard sesuai role saat login.
|
*/

// ========================
// 🔹 ROOT & DASHBOARD
// ========================
Route::get('/', fn() => view('auth.login'));

Route::get('/dashboard', function () {
    $user = auth()->user();
    $role = $user->getRoleNames()->first();

    return match ($role) {
        'manager'  => redirect()->route('manager.dashboard'),
        'karyawan' => redirect()->route('karyawan.tasks.index'),
        'client'   => redirect()->route('clients.project-requests.index'),
        default    => view('dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// ========================
// 🔹 PROFILE (semua role)
// ========================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================
// 🔹 MANAGER ROUTES
// ========================
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        // Manajemen User, Karyawan, Client
        Route::resource('users', UserController::class);
        Route::resource('karyawans', KaryawanController::class);
        Route::resource('clients', ClientController::class);

        // Project Management
        Route::get('projects/requests', [ProjectController::class, 'showRequest'])
            ->name('projects.requests');
        Route::get('projects/create/{requestId}', [ProjectController::class, 'create'])
            ->name('projects.create.from.request');
        Route::resource('projects', ProjectController::class);
            // Rute untuk Project Request Manager

    Route::resource('project-request', ManagerProjectRequestController::class);
    });

// ========================
// 🔹 KARYAWAN ROUTES
// ========================
Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
    ->group(function () {
        // Task Management
        Route::resource('tasks', TaskController::class);

        // Log kerja (task work log)
        Route::get('tasks/{task}/logs', [TaskController::class, 'logs'])->name('tasks.logs');
        Route::delete('tasks/logs/{log}', [TaskController::class, 'destroyLog'])->name('tasks.logs.destroy');
    });

// ========================
// 🔹 CLIENT ROUTESb
// ========================
Route::middleware(['auth', 'role:client'])
    ->prefix('clients')
    ->name('clients.')
    ->group(function () {
        Route::resource('project-requests', ProjectRequestController::class);
    });

    Route::get('/manager/dashboard', [DashboardController::class, 'index'])
        ->name('manager.dashboard')
        ->middleware('role:manager');
// ========================
// 🔹 AUTH ROUTES (Laravel Breeze / Fortify)
// ========================
require __DIR__ . '/auth.php';
