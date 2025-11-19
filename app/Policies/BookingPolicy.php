<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        if ($user->isClient() && $user->id === $booking->user_id) {
            return true;
        }

        if ($user->isTrainer() && $booking->session->trainer_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isClient();
    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        if ($user->isTrainer() && $booking->session->trainer_id === $user->id) {
            return true;
        }

        return false;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        if ($user->isClient() && $user->id === $booking->user_id) {
            return $booking->canBeCanceled();
        }

        if ($user->isTrainer() && $booking->session->trainer_id === $user->id) {
            return true;
        }

        return false;
    }
}



