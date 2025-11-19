<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\Notification;
use App\Notifications\BookingNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class SendBookingNotification
{
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;
        $trainer = $booking->session->trainer;
        $client = $booking->user;

        Notification::create([
            'user_id' => $trainer->id,
            'title' => 'New Booking',
            'message' => "{$client->name} booked your session on {$booking->session->date->format('M j, Y')}",
            'type' => 'booking_created',
        ]);

        Notification::create([
            'user_id' => $client->id,
            'title' => 'Booking Confirmed',
            'message' => "Your booking with {$trainer->name} is pending confirmation",
            'type' => 'booking_created',
        ]);

        $trainer->notify(new BookingNotification($booking, 'trainer'));
        $client->notify(new BookingNotification($booking, 'client'));
    }
}

