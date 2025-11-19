<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('/trainers', \App\Http\Livewire\Client\TrainerList::class)->name('trainers');
Route::get('/trainer/{trainer}', \App\Http\Livewire\Client\TrainerProfileView::class)->name('trainer.show');

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/profile', \App\Http\Livewire\Client\UserProfile::class)->name('profile');
    Route::get('/profile/bookings', \App\Http\Livewire\Client\UserProfile::class)->name('profile.bookings');
    Route::get('/notifications', \App\Http\Livewire\Client\Notifications::class)->name('notifications');
    Route::get('/reviews/create/{booking}', \App\Http\Livewire\Client\ReviewCreate::class)->name('reviews.create');
});

Route::middleware(['auth', 'role:trainer'])->prefix('trainer-panel')->name('trainer-panel.')->group(function () {
    Route::get('/', \App\Http\Livewire\Trainer\Dashboard::class)->name('dashboard');
    Route::get('/profile', \App\Http\Livewire\Trainer\ProfileEditor::class)->name('profile');
    Route::get('/schedule', \App\Http\Livewire\Trainer\ScheduleManager::class)->name('schedule');
    Route::get('/bookings', \App\Http\Livewire\Trainer\BookingManager::class)->name('bookings');
    Route::get('/statistics', \App\Http\Livewire\Trainer\Statistics::class)->name('statistics');
});

Route::middleware(['auth', 'role:owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::view('/profile', 'admin.profile')->name('profile');
    Route::get('/users', \App\Http\Livewire\Admin\UsersTable::class)->name('users');
    Route::get('/trainers', \App\Http\Livewire\Admin\TrainersTable::class)->name('trainers');
    Route::get('/bookings', \App\Http\Livewire\Admin\BookingsTable::class)->name('bookings');
    Route::get('/reviews', \App\Http\Livewire\Admin\ReviewsModeration::class)->name('reviews');
    Route::get('/settings', \App\Http\Livewire\Admin\PlatformSettings::class)->name('settings');
    Route::get('/reports/bookings', \App\Http\Livewire\Admin\Reports\Bookings::class)->name('reports.bookings');
    Route::get('/reports/income', \App\Http\Livewire\Admin\Reports\Income::class)->name('reports.income');
});

require __DIR__.'/auth.php';

