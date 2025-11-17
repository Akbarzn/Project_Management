<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\Manager\UserController;
// use App\Http\Controllers\Manager\ProjectController;
// use App\Http\Controllers\Karyawan\KaryawanController;
// use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectRequestController;
// use App\Http\Controllers\Manager\ProjectRequestController as ManagerProjectRequestController;
// use App\Http\Controllers\Client\ProjectRequestController;
// use App\Http\Controllers\Manager\ProjectRequestController as ManagerProjectRequestController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskLogController;
use App\Http\Controllers\Manager\TaskController as ManagerTaskController;


Route::get('/', fn() => view('auth.login'));

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// manager
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        // Manajemen User, Karyawan, Client
        Route::resource('users', UserController::class);
        Route::resource('karyawans', KaryawanController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('projects', ProjectController::class);

        // Project Management
        Route::get('projects/requests', [ProjectController::class, 'showRequest'])
            ->name('projects.requests');
        Route::get('projects/create/{requestId}', [ProjectController::class, 'create'])
            ->name('projects.create.from.request');
        Route::resource('projects', ProjectController::class);
        
        // Rute untuk Project Request Manager
        Route::resource('tasks', TaskController::class);
        Route::resource('project-request', ProjectRequestController::class);
        Route::get('/karyawan/{id}/project', [DashboardController::class, 'showKaryawanProject'])->name('karyawans.project');
        
        // /untuk projectdetail
        Route::get('project-detail/{id}', [DashboardController::class,'getProjectDetail'])->name('project-detail');
    });

Route::middleware(['auth', 'role:karyawan'])
    ->prefix('karyawan')
    ->name('karyawan.')
->group(function () {
        // Task Management
        Route::resource('tasks', TaskController::class);
        Route::get('tasks/{task}/log', [TaskLogController::class, 'show'])->name('tasks.logs');
        Route::delete('tasks/logs/{log}', [TaskLogController::class, 'destroy'])->name('tasks.logs.destroy');


        // Log kerja (task work log)
        // Route::get('tasks/{task}/logs', [TaskController::class, 'logs'])->name('tasks.logs');
        // Route::resource('task-logs', TaskLogController::class);
    });

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
