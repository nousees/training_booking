<?php

namespace App\Livewire\User\Bookings;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    public function render()
    {
        $bookings = Booking::where('user_id', Auth::id())->with(['training.trainer', 'training.gym'])->paginate(10);
        
        return view('livewire.user.bookings.booking-list', [
            'bookings' => $bookings
        ])->layout('components.layouts.app', [
            'title' => 'Мои бронирования'
        ]);
    }

    public function deleteBooking($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);
        $booking->delete();
        
        session()->flash('message', 'Бронирование успешно удалено.');
    }
}
