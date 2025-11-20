<?php

namespace App\Http\Livewire\Client;

use App\Models\TrainingSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Schedule extends Component
{
    public $showDetailsModal = false;
    public $detailsSession = null;

    public function showDetails($sessionId)
    {
        $this->detailsSession = TrainingSession::with(['trainer', 'booking.user'])->findOrFail($sessionId);
        $this->showDetailsModal = true;
    }

    public function closeDetails()
    {
        $this->showDetailsModal = false;
        $this->detailsSession = null;
    }

    public function render()
    {
        $today = now()->toDateString();
        $until = now()->copy()->addDays(6)->toDateString();

        $sessions = TrainingSession::whereHas('booking', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('date', [$today, $until])
            ->orderBy('date')
            ->orderBy('start_time')
            ->with(['trainer', 'booking'])
            ->get();

        return view('livewire.client.schedule', [
            'sessions' => $sessions,
        ]);
    }
}
