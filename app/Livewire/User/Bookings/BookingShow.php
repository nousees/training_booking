<?php

namespace App\Livewire\User\Bookings;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookingShow extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        // Проверяем, что бронирование принадлежит текущему пользователю
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Доступ запрещен');
        }
        
        $this->booking = $booking;
    }

    public function render()
    {
        return view('livewire.user.bookings.booking-show', [
            'booking' => $this->booking,
        ]);
    }
}

