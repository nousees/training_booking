<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', function () {
    $user = auth()->user();
    
    switch ($user->role) {
        case 'owner':
            return redirect()->route('owner.dashboard');
        case 'manager':
            return redirect()->route('manager.dashboard');
        case 'user':
            return redirect()->route('user.dashboard');
        default:
            return view('dashboard');
    }
})->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Удалены промежуточные дашборды - теперь пользователи перенаправляются сразу на основные страницы

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner-dashboard');
    })->name('dashboard');

    // Gym routes
    Route::prefix('gyms')->name('gyms.')->group(function () {
        Route::get('/', \App\Livewire\Owner\Gyms\GymList::class)->name('index');
        Route::get('/create', \App\Livewire\Owner\Gyms\GymForm::class)->name('create');
        Route::get('/{gym}/edit', \App\Livewire\Owner\Gyms\GymForm::class)->name('edit');
        Route::get('/{gym}', \App\Livewire\Owner\Gyms\GymShow::class)->name('show');
    });

    // Trainer routes
    Route::prefix('trainers')->name('trainers.')->group(function () {
        Route::get('/', \App\Livewire\Owner\Trainers\TrainerList::class)->name('index');
        Route::get('/create', \App\Livewire\Owner\Trainers\TrainerForm::class)->name('create');
        Route::get('/{trainer}/edit', \App\Livewire\Owner\Trainers\TrainerForm::class)->name('edit');
        Route::get('/{trainer}', \App\Livewire\Owner\Trainers\TrainerShow::class)->name('show');
    });

    // Training routes
    Route::prefix('trainings')->name('trainings.')->group(function () {
        Route::get('/', \App\Livewire\Owner\Trainings\TrainingList::class)->name('index');
        Route::get('/create', \App\Livewire\Owner\Trainings\TrainingForm::class)->name('create');
        Route::get('/{training}/edit', \App\Livewire\Owner\Trainings\TrainingForm::class)->name('edit');
        Route::get('/{training}', \App\Livewire\Owner\Trainings\TrainingShow::class)->name('show');
    });

    // Booking routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', \App\Livewire\Owner\Bookings\BookingList::class)->name('index');
        Route::get('/create', \App\Livewire\Owner\Bookings\BookingForm::class)->name('create');
        Route::get('/{booking}/edit', \App\Livewire\Owner\Bookings\BookingForm::class)->name('edit');
        Route::get('/{booking}', \App\Livewire\Owner\Bookings\BookingShow::class)->name('show');
    });
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', function () {
        return view('manager-dashboard');
    })->name('dashboard');

    // Trainer routes
    Route::prefix('trainers')->name('trainers.')->group(function () {
        Route::get('/', \App\Livewire\Manager\Trainers\TrainerList::class)->name('index');
        Route::get('/create', \App\Livewire\Manager\Trainers\TrainerForm::class)->name('create');
        Route::get('/{trainer}/edit', \App\Livewire\Manager\Trainers\TrainerForm::class)->name('edit');
        Route::get('/{trainer}', \App\Livewire\Manager\Trainers\TrainerShow::class)->name('show');
    });

    // Training routes
    Route::prefix('trainings')->name('trainings.')->group(function () {
        Route::get('/', \App\Livewire\Manager\Trainings\TrainingList::class)->name('index');
        Route::get('/create', \App\Livewire\Manager\Trainings\TrainingForm::class)->name('create');
        Route::get('/{training}/edit', \App\Livewire\Manager\Trainings\TrainingForm::class)->name('edit');
        Route::get('/{training}', \App\Livewire\Manager\Trainings\TrainingShow::class)->name('show');
    });

    // Booking routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', \App\Livewire\Manager\Bookings\BookingList::class)->name('index');
        Route::get('/create', \App\Livewire\Manager\Bookings\BookingForm::class)->name('create');
        Route::get('/{booking}/edit', \App\Livewire\Manager\Bookings\BookingForm::class)->name('edit');
        Route::get('/{booking}', \App\Livewire\Manager\Bookings\BookingShow::class)->name('show');
    });
});

// User routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', function () {
        return view('user-dashboard');
    })->name('dashboard');

    // Training routes
    Route::prefix('trainings')->name('trainings.')->group(function () {
        Route::get('/', \App\Livewire\User\Trainings\TrainingList::class)->name('index');
        Route::get('/{training}', \App\Livewire\User\Trainings\TrainingShow::class)->name('show');
    });

    // Booking routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', \App\Livewire\User\Bookings\BookingList::class)->name('index');
        Route::get('/create', \App\Livewire\User\Bookings\BookingForm::class)->name('create');
        Route::get('/{booking}/edit', \App\Livewire\User\Bookings\BookingForm::class)->name('edit');
        Route::get('/{booking}', \App\Livewire\User\Bookings\BookingShow::class)->name('show');
    });
});

require __DIR__.'/auth.php';

