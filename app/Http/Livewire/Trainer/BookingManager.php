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
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $futureOnly = false;

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
            })
            ->with(['user', 'session']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', $search)
                       ->orWhere('email', 'like', $search);
                });
            });
        }

        if ($this->dateFrom) {
            $query->whereHas('session', function ($q) {
                $q->whereDate('date', '>=', $this->dateFrom);
            });
        }

        if ($this->dateTo) {
            $query->whereHas('session', function ($q) {
                $q->whereDate('date', '<=', $this->dateTo);
            });
        }

        if ($this->futureOnly) {
            $today = now()->toDateString();
            $nowTime = now()->format('H:i:s');
            $query->whereHas('session', function ($q) use ($today, $nowTime) {
                $q->where(function ($qq) use ($today, $nowTime) {
                    $qq->where('date', '>', $today)
                       ->orWhere(function ($qqq) use ($today, $nowTime) {
                           $qqq->where('date', '=', $today)
                               ->where('start_time', '>=', $nowTime);
                       });
                });
            });
        }

        // Сначала все, кроме completed, затем completed в самом конце
        $bookings = $query
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.trainer.booking-manager', [
            'bookings' => $bookings,
        ]);
    }
}

