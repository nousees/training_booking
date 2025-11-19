<?php

namespace App\Livewire\User\Bookings;

use App\Models\Booking;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    public string $tab = 'upcoming';

    public function render()
    {
        $query = Booking::where('user_id', Auth::id())
            ->with(['training.trainer', 'training.gym'])
            ->orderBy('start_time');

        if ($this->tab === 'upcoming') {
            $query->where('start_time', '>=', now());
        } else {
            $query->where('start_time', '<', now());
        }

        $bookings = $query->paginate(10);
        
        return view('livewire.user.bookings.booking-list', [
            'bookings' => $bookings
        ])->layout('components.layouts.app', [
            'title' => 'Мои бронирования'
        ]);
    }

    public function cancelBooking(int $bookingId, ?string $reason = null): void
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        if (! Gate::allows('cancel', $booking)) {
            abort(403);
        }

        $booking->status = 'cancelled_by_client';
        $booking->cancellation_reason = $reason;
        $booking->save();

        Auth::user()->notify(new BookingCancelledNotification($booking, 'client'));

        session()->flash('message', 'Бронирование успешно отменено.');
    }
}
