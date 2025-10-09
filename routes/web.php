<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\UserController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Karyawan\KaryawanController;
use App\Http\Controllers\Manager\ProjectController;
use App\Http\Controllers\Client\ProjectRequestController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','role:manager'])->prefix('manager')->name('manager.')->group(function(){
    Route::resource('users', UserController::class);
});

Route::middleware(['auth','role:manager'])->prefix('manager')->name('manager.')->group(function(){
    Route::resource('clients', ClientController::class);
});

Route::middleware(['auth','role:manager'])->prefix('manager')->name('manager.')->group(function(){
    Route::resource('karyawans', KaryawanController::class);
});

Route::middleware(['auth','role:client'])->prefix('clients')->name('clients.')->group(function(){
    Route::resource('project-requests', ProjectRequestController::class);
});


// Pastikan ini ada di dalam file routes/web.php Anda

Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        // Rute KUSTOM untuk menampilkan semua request yang pending
        Route::get('projects/requests', [ProjectController::class, 'showRequest'])->name('projects.requests');

        // Rute KUSTOM untuk form create yang menerima parameter requestId
        Route::get('projects/create/{requestId}', [ProjectController::class, 'create'])->name('projects.create.from.request');

        // Rute RESOURCE standar untuk CRUD Project
        Route::resource('projects', ProjectController::class);
    });
require __DIR__.'/auth.php';
