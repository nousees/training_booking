<?php

namespace App\Http\Livewire\Trainer;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\TrainingSession;
use App\Models\Booking;

class Dashboard extends Component
{
    public function render()
    {
        $trainerId = Auth::id();

        $stats = [
            'sessions_total' => TrainingSession::where('trainer_id', $trainerId)->count(),
            'sessions_upcoming' => TrainingSession::where('trainer_id', $trainerId)
                ->whereDate('date', '>=', now()->toDateString())
                ->count(),
            'bookings_confirmed' => Booking::whereHas('session', fn($q) => $q->where('trainer_id', $trainerId))
                ->where('status', 'confirmed')->count(),
            'bookings_pending' => Booking::whereHas('session', fn($q) => $q->where('trainer_id', $trainerId))
                ->where('status', 'pending')->count(),
        ];

        return view('livewire.trainer.dashboard', compact('stats'));
    }
}
