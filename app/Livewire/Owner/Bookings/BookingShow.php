<?php

namespace App\Livewire\Owner\Bookings;

use App\Models\Booking;
use App\Models\Training;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookingShow extends Component
{
    public Booking $booking;

    public function mount(Booking $booking)
    {
        // Проверяем, что бронирование принадлежит тренировке владельца
        $training = Training::find($booking->training_id);
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        
        $this->booking = $booking;
    }

    public function render()
    {
        return view('livewire.owner.bookings.booking-show', [
            'booking' => $this->booking,
        ]);
    }
}

