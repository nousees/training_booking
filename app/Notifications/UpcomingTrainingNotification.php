<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingTrainingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Booking $booking,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $training = $this->booking->training;

        return (new MailMessage)
            ->subject('Напоминание о предстоящей тренировке')
            ->greeting('Здравствуйте, ' . $notifiable->name)
            ->line('Напоминаем о вашей предстоящей тренировке.')
            ->line('Тренировка: ' . ($training?->name ?? '—'))
            ->line('Начало: ' . $this->booking->start_time?->format('d.m.Y H:i'))
            ->action('Мои тренировки', route('user.bookings.index'));
    }
}




