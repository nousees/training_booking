<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/owner/dashboard', function () {
        return view('owner-dashboard');
    })->middleware('role:owner')->name('owner.dashboard');

    Route::get('/manager/dashboard', function () {
        return view('manager-dashboard');
    })->middleware('role:manager')->name('manager.dashboard');

    Route::get('/user/dashboard', function () {
        return view('user-dashboard');
    })->middleware('role:user')->name('user.dashboard');
});

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner-dashboard');
    })->name('dashboard');

    // Gym routes - ДОБАВИТЬ ЭТИ МАРШРУТЫ
    Route::prefix('gyms')->name('gyms.')->group(function () {
        Route::get('/', \App\Livewire\Owner\Gyms\GymList::class)->name('index');
        Route::get('/create', \App\Livewire\Owner\Gyms\GymForm::class)->name('create');
        Route::get('/{gym}/edit', \App\Livewire\Owner\Gyms\GymForm::class)->name('edit');
        Route::get('/{gym}', \App\Livewire\Owner\Gyms\GymShow::class)->name('show');
    });
});

require __DIR__.'/auth.php';

