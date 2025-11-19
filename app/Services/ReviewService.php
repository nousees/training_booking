<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;

class ReviewService
{
    public function createReview(User $user, Booking $booking, int $rating, ?string $comment = null): Review
    {
        if ($booking->user_id !== $user->id) {
            throw new \Exception('You can only review your own bookings');
        }

        if (!$booking->isCompleted()) {
            throw new \Exception('You can only review completed bookings');
        }

        if ($booking->review) {
            throw new \Exception('Review already exists for this booking');
        }

        if ($rating < 1 || $rating > 5) {
            throw new \Exception('Rating must be between 1 and 5');
        }

        return Review::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'trainer_id' => $booking->session->trainer_id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }
}

