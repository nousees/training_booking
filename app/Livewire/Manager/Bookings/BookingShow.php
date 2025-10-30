<?php

namespace App\Livewire\Manager\Bookings;

use App\Models\Booking;
use Livewire\Component;

class BookingShow extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function render()
    {
        return view('livewire.manager.bookings.booking-show', [
            'booking' => $this->booking,
        ]);
    }
}


