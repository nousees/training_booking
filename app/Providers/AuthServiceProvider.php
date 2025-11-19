<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\TrainerProfile;
use App\Policies\BookingPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\TrainerProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Booking::class => BookingPolicy::class,
        TrainerProfile::class => TrainerProfilePolicy::class,
        Review::class => ReviewPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}



