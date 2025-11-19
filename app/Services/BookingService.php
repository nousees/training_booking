<?php

namespace App\Services;

use App\Listeners\SendBookingNotification;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\TrainingSession;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(User $user, TrainingSession $session): Booking
    {
        $existing = Booking::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->first();
        if ($existing) {
            return $existing;
        }

        if ($user->isBlocked()) {
            throw new \Exception('Ваш аккаунт заблокирован. Бронирование недоступно.');
        }

        if (!$session->isAvailable()) {
            throw new \Exception('Слот недоступен.');
        }

        if ($session->booking) {
            throw new \Exception('Слот уже забронирован.');
        }


        $dateStr = $session->date instanceof \Carbon\Carbon
            ? $session->date->format('Y-m-d')
            : \Carbon\Carbon::parse($session->date)->format('Y-m-d');
        $startStr = $session->start_time instanceof \Carbon\Carbon
            ? $session->start_time->format('H:i:s')
            : \Carbon\Carbon::parse($session->start_time)->format('H:i:s');
        $endStr = $session->end_time instanceof \Carbon\Carbon
            ? $session->end_time->format('H:i:s')
            : \Carbon\Carbon::parse($session->end_time)->format('H:i:s');

        $startAt = \Carbon\Carbon::parse($dateStr.' '.$startStr);
        $endAt = \Carbon\Carbon::parse($dateStr.' '.$endStr);

        $settings = SystemSetting::get();

        if ($startAt->isPast()) {
            throw new \Exception('Нельзя забронировать тренировку в прошлом.');
        }

        $hoursBeforeStart = now()->diffInHours($startAt, false);
        if ($hoursBeforeStart < (int) $settings->min_booking_hours_before_start) {
            throw new \Exception('Нельзя забронировать менее чем за ' . $settings->min_booking_hours_before_start . ' час(ов) до начала.');
        }

        $daysAhead = now()->startOfDay()->diffInDays($startAt->copy()->startOfDay(), false);
        if ($daysAhead > (int) $settings->max_booking_days_ahead) {
            throw new \Exception('Нельзя забронировать более чем на ' . $settings->max_booking_days_ahead . ' дней вперёд.');
        }



        $hasOverlap = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('session', function ($q) use ($session, $startAt, $endAt) {
                $q->where('date', $session->date)
                  ->where(function ($qq) use ($startAt, $endAt) {
                      $qq->where('start_time', '<', $endAt->format('H:i:s'))
                         ->where('end_time', '>', $startAt->format('H:i:s'));
                  });
            })
            ->exists();

        if ($hasOverlap) {
            throw new \Exception('У вас уже есть бронирование на это время.');
        }

        return DB::transaction(function () use ($user, $session, $settings) {
            $booking = Booking::create([
                'session_id' => $session->id,
                'user_id' => $user->id,
                'status' => $settings->auto_confirm_bookings ? 'confirmed' : 'pending',
                'payment_status' => 'unpaid',
            ]);

            $session->update(['status' => 'booked']);

            app(SendBookingNotification::class)->handle(new \App\Events\BookingCreated($booking));

            if ($settings->auto_confirm_bookings) {
                $this->sendBookingConfirmedNotifications($booking);
            }

            return $booking;
        });
    }

    public function confirmBooking(Booking $booking): void
    {
        if ($booking->status !== 'pending') {
            throw new \Exception('Можно подтвердить только бронирование в статусе "Ожидает".');
        }

        $booking->update(['status' => 'confirmed']);

        $this->sendBookingConfirmedNotifications($booking);
    }

    protected function sendBookingConfirmedNotifications(Booking $booking): void
    {
        $booking->loadMissing(['user', 'session.trainer']);

        $client = $booking->user;
        $trainer = $booking->session->trainer;

        if ($client) {
            Notification::create([
                'user_id' => $client->id,
                'title' => 'Бронирование подтверждено',
                'message' => 'Ваше бронирование тренировки подтверждено.',
                'type' => 'booking_confirmed',
            ]);

            $client->notify(new BookingConfirmedNotification($booking));
        }

        if ($trainer) {
            Notification::create([
                'user_id' => $trainer->id,
                'title' => 'Бронирование подтверждено',
                'message' => 'Вы подтвердили бронирование клиента ' . $client->name . '.',
                'type' => 'booking_confirmed',
            ]);

            $trainer->notify(new BookingConfirmedNotification($booking));
        }
    }

    public function cancelBooking(Booking $booking): void
    {
        if ($booking->isCanceled() || $booking->isCompleted()) {
            throw new \Exception('Нельзя отменить это бронирование.');
        }

        DB::transaction(function () use ($booking) {
            $booking->loadMissing(['user', 'session.trainer']);

            $booking->update(['status' => 'canceled']);
            $booking->session->update(['status' => 'available']);

            $client = $booking->user;
            $trainer = $booking->session->trainer;

            if ($client) {
                Notification::create([
                    'user_id' => $client->id,
                    'title' => 'Бронирование отменено',
                    'message' => 'Ваше бронирование было отменено.',
                    'type' => 'booking_cancelled',
                ]);

                $client->notify(new BookingCancelledNotification($booking, 'client'));
            }

            if ($trainer) {
                Notification::create([
                    'user_id' => $trainer->id,
                    'title' => 'Бронирование отменено',
                    'message' => 'Бронирование клиента ' . ($client?->name ?? '') . ' было отменено.',
                    'type' => 'booking_cancelled',
                ]);

                $trainer->notify(new BookingCancelledNotification($booking, 'trainer'));
            }
        });
    }
}

