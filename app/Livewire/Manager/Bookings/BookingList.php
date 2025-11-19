<?php

namespace App\Livewire\Manager\Bookings;

use App\Models\Booking;
use App\Notifications\BookingCancelledNotification;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    public function render()
    {
        $bookings = Booking::with(['user', 'training.trainer', 'training.gym'])->paginate(10);
        
        return view('livewire.manager.bookings.booking-list', [
            'bookings' => $bookings
        ])->layout('components.layouts.app', [
            'title' => 'Бронирования'
        ]);
    }

    public function cancelBooking(int $bookingId, ?string $reason = null): void
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'cancelled_by_trainer';
        $booking->cancellation_reason = $reason;
        $booking->save();

        if ($booking->user) {
            $booking->user->notify(new BookingCancelledNotification($booking, 'trainer'));
        }

        session()->flash('message', 'Бронирование отменено тренером.');
    }
}
