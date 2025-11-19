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
            'title' => 'Новое бронирование',
            'message' => "Клиент {$client->name} забронировал у вас тренировку на {$booking->session->date->format('d.m.Y')}.",
            'type' => 'booking_created',
        ]);

        Notification::create([
            'user_id' => $client->id,
            'title' => 'Бронирование создано',
            'message' => "Ваше бронирование у тренера {$trainer->name} ожидает подтверждения.",
            'type' => 'booking_created',
        ]);

        $client->notify(new BookingNotification($booking, 'client'));
    }
}

