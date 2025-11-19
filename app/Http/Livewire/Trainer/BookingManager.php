<?php

namespace App\Http\Livewire\Trainer;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BookingManager extends Component
{
    use WithPagination;

    public $statusFilter = '';

    public function confirm($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        if ($booking->session->trainer_id !== Auth::id()) {
            abort(403);
        }

        $bookingService = app(BookingService::class);
        $bookingService->confirmBooking($booking);
        
        session()->flash('message', 'Бронирование подтверждено');
    }

    public function cancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        if ($booking->session->trainer_id !== Auth::id()) {
            abort(403);
        }

        $bookingService = app(BookingService::class);
        $bookingService->cancelBooking($booking);
        
        session()->flash('message', 'Бронирование отменено');
    }

    public function reject($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        if ($booking->session->trainer_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            session()->flash('message', 'Можно отклонить только ожидающие бронирования');
            return;
        }

        $bookingService = app(BookingService::class);
        $bookingService->cancelBooking($booking);
        session()->flash('message', 'Бронирование отклонено');
    }

    public function render()
    {
        $query = Booking::whereHas('session', function ($q) {
            $q->where('trainer_id', Auth::id());
        })->with(['user', 'session']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.trainer.booking-manager', [
            'bookings' => $bookings,
        ]);
    }
}

