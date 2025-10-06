<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\UserController;
use App\Http\Controllers\Manager\ClientController;

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


require __DIR__.'/auth.php';
