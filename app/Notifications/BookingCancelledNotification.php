<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
        protected string $cancelledBy, // client|trainer
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $training = $this->booking->training;

        $message = (new MailMessage)
            ->subject('Бронирование отменено')
            ->greeting('Здравствуйте, ' . $notifiable->name)
            ->line('Одно из ваших бронирований было отменено.')
            ->line('Тренировка: ' . ($training?->name ?? '—'))
            ->line('Начало: ' . $this->booking->start_time?->format('d.m.Y H:i'));

        if ($this->booking->cancellation_reason) {
            $message->line('Причина: ' . $this->booking->cancellation_reason);
        }

        return $message
            ->action('Мои тренировки', route('profile.bookings'));
    }
}




