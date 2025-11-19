<?php

namespace App\Http\Livewire\Client;

use App\Models\Booking;
use App\Models\TrainingSession;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookingForm extends Component
{
    public $sessionId;
    public $session;
    public $errorMessage = '';

    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->session = TrainingSession::findOrFail($sessionId);
        
        if (!$this->session->isAvailable()) {
            abort(404, 'Session is not available');
        }

    }

    public function book()
    {
        if (!Auth::check() || !Auth::user()->isClient()) {
            return;
        }
        $this->errorMessage = '';

        try {
            $bookingService = app(BookingService::class);
            $booking = $bookingService->createBooking(Auth::user(), $this->session);

            $this->dispatch('booking-created', bookingId: $booking->id);

            session()->flash('message', 'Бронирование успешно создано!');

            return redirect()->route('profile.bookings');
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
            return;
        }
    }

    public function render()
    {
        return view('livewire.client.booking-form');
    }
}

