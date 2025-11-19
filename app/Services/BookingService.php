<?php

namespace App\Services;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(User $user, TrainingSession $session): Booking
    {
        if ($user->isBlocked()) {
            throw new \Exception('Ваш аккаунт заблокирован. Бронирование недоступно.');
        }

        if (!$session->isAvailable()) {
            throw new \Exception('Session is not available');
        }

        if ($session->booking) {
            throw new \Exception('Session is already booked');
        }

        // Disallow booking past sessions (date/time already passed)
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
        if ($startAt->isPast()) {
            throw new \Exception('Cannot book a past session');
        }

        // Prevent client from booking overlapping sessions (pending/confirmed)
        // Overlap exists iff (existing.start < newEnd) AND (existing.end > newStart)
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
            throw new \Exception('You already have a booking at this time');
        }

        return DB::transaction(function () use ($user, $session) {
            $booking = Booking::create([
                'session_id' => $session->id,
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            $session->update(['status' => 'booked']);

            event(new BookingCreated($booking));

            return $booking;
        });
    }

    public function confirmBooking(Booking $booking): void
    {
        if ($booking->status !== 'pending') {
            throw new \Exception('Only pending bookings can be confirmed');
        }

        $booking->update(['status' => 'confirmed']);
    }

    public function cancelBooking(Booking $booking): void
    {
        if ($booking->isCanceled() || $booking->isCompleted()) {
            throw new \Exception('Cannot cancel this booking');
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'canceled']);
            $booking->session->update(['status' => 'available']);
        });
    }
}

