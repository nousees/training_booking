<?php

namespace App\Livewire\Manager\Bookings;

use App\Models\Booking;
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

    public function deleteBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->delete();
        
        session()->flash('message', 'Бронирование успешно удалено.');
    }
}
