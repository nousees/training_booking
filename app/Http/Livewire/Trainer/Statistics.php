<?php

namespace App\Http\Livewire\Trainer;

use App\Models\Booking;
use App\Models\TrainingSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Statistics extends Component
{
    public function render()
    {
        $trainerId = Auth::id();

        $totalSessions = TrainingSession::where('trainer_id', $trainerId)->count();
        $completedTrainings = Booking::whereHas('session', fn($q) => $q->where('trainer_id', $trainerId))
            ->where('status', 'completed')->count();
        $confirmedTrainings = Booking::whereHas('session', fn($q) => $q->where('trainer_id', $trainerId))
            ->where('status', 'confirmed')->count();

        $income = Booking::whereHas('session', fn($q) => $q->where('trainer_id', $trainerId))
            ->whereIn('status', ['confirmed','completed'])
            ->with('session')
            ->get()
            ->sum(fn($b) => (float) $b->session->price);

        return view('livewire.trainer.statistics', [
            'totalSessions' => $totalSessions,
            'completedTrainings' => $completedTrainings,
            'confirmedTrainings' => $confirmedTrainings,
            'income' => $income,
        ]);
    }
}
