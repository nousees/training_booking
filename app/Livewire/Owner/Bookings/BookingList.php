<?php

namespace App\Livewire\Owner\Bookings;

use App\Models\Booking;
use App\Models\Training;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BookingList extends Component
{
    use WithPagination;

    public function render()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainingIds = Training::whereIn('gym_id', $gymIds)->pluck('id');
        $bookings = Booking::whereIn('training_id', $trainingIds)->with(['user', 'training.trainer', 'training.gym'])->paginate(10);
        
        return view('livewire.owner.bookings.booking-list', [
            'bookings' => $bookings
        ])->layout('components.layouts.app', [
            'title' => 'Мои бронирования'
        ]);
    }

    public function deleteBooking($bookingId)
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainingIds = Training::whereIn('gym_id', $gymIds)->pluck('id');
        $booking = Booking::whereIn('training_id', $trainingIds)->findOrFail($bookingId);
        $booking->delete();
        
        session()->flash('message', 'Бронирование успешно удалено.');
    }
}
