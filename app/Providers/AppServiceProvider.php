<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('app.http.livewire.trainer.schedule-manager', \App\Http\Livewire\Trainer\ScheduleManager::class);
        Livewire::component('app.http.livewire.trainer.booking-manager', \App\Http\Livewire\Trainer\BookingManager::class);
        Livewire::component('app.http.livewire.trainer.dashboard', \App\Http\Livewire\Trainer\Dashboard::class);
        Livewire::component('app.http.livewire.trainer.profile-editor', \App\Http\Livewire\Trainer\ProfileEditor::class);
        Livewire::component('app.http.livewire.trainer.statistics', \App\Http\Livewire\Trainer\Statistics::class);
        Livewire::component('app.http.livewire.client.trainer-list', \App\Http\Livewire\Client\TrainerList::class);
        Livewire::component('app.http.livewire.client.trainer-profile-view', \App\Http\Livewire\Client\TrainerProfileView::class);
        Livewire::component('app.http.livewire.client.user-profile', \App\Http\Livewire\Client\UserProfile::class);
        Livewire::component('app.http.livewire.client.notifications', \App\Http\Livewire\Client\Notifications::class);
        Livewire::component('app.http.livewire.client.booking-form', \App\Http\Livewire\Client\BookingForm::class);
        Livewire::component('client.booking-form', \App\Http\Livewire\Client\BookingForm::class);
        Livewire::component('app.http.livewire.client.review-create', \App\Http\Livewire\Client\ReviewCreate::class);
        Livewire::component('app.http.livewire.admin.users-table', \App\Http\Livewire\Admin\UsersTable::class);
        Livewire::component('app.http.livewire.admin.trainers-table', \App\Http\Livewire\Admin\TrainersTable::class);
        Livewire::component('app.http.livewire.admin.bookings-table', \App\Http\Livewire\Admin\BookingsTable::class);
        Livewire::component('app.http.livewire.admin.reviews-moderation', \App\Http\Livewire\Admin\ReviewsModeration::class);
        Livewire::component('app.http.livewire.admin.platform-settings', \App\Http\Livewire\Admin\PlatformSettings::class);
        Livewire::component('app.http.livewire.admin.reports.bookings', \App\Http\Livewire\Admin\Reports\Bookings::class);
        Livewire::component('app.http.livewire.admin.reports.income', \App\Http\Livewire\Admin\Reports\Income::class);
    }
}
