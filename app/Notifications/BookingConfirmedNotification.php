<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject('Ваше бронирование подтверждено')
            ->greeting('Здравствуйте, ' . $notifiable->name)
            ->line('Ваше бронирование тренировки подтверждено.')
            ->line('Тренировка: ' . ($training?->name ?? '—'))
            ->line('Зал: ' . optional($training?->gym)->name)
            ->line('Тренер: ' . optional($training?->trainer)->name)
            ->line('Начало: ' . $this->booking->start_time?->format('d.m.Y H:i'))
            ->line('Окончание: ' . $this->booking->end_time?->format('d.m.Y H:i'))
            ->action('Мои тренировки', route('profile.bookings'))
            ->line('Спасибо, что выбираете наши тренировки!');
    }
}




