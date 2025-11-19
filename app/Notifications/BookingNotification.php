<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $recipientType
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $session = $this->booking->session;
        $message = (new MailMessage)
            ->subject('Новое бронирование')
            ->greeting('Здравствуйте, ' . $notifiable->name)
            ->line('Создано новое бронирование.');

        if ($this->recipientType === 'trainer') {
            $message->line('Клиент: ' . $this->booking->user->name)
                    ->line('Дата: ' . $session->date->format('d.m.Y'))
                    ->line('Время: ' . $session->start_time->format('H:i') . ' - ' . $session->end_time->format('H:i'))
                    ->action('Открыть бронирования', url('/trainer-panel/bookings'));
        } else {
            $message->line('Тренер: ' . $session->trainer->name)
                    ->line('Дата: ' . $session->date->format('d.m.Y'))
                    ->line('Время: ' . $session->start_time->format('H:i') . ' - ' . $session->end_time->format('H:i'))
                    ->action('Открыть бронирования', url('/profile'));
        }

        return $message;
    }

    public function toArray($notifiable): array
    {
        $session = $this->booking->session;
        $title = 'Новое бронирование';
        $message = $this->recipientType === 'trainer'
            ? ('Клиент: ' . $this->booking->user->name . ' • ' . $session->date->format('d.m.Y') . ' • ' . $session->start_time->format('H:i') . '–' . $session->end_time->format('H:i'))
            : ('Тренер: ' . $session->trainer->name . ' • ' . $session->date->format('d.m.Y') . ' • ' . $session->start_time->format('H:i') . '–' . $session->end_time->format('H:i'));

        return [
            'booking_id' => $this->booking->id,
            'type' => 'booking_created',
            'recipient_type' => $this->recipientType,
            'title' => $title,
            'message' => $message,
        ];
    }
}

