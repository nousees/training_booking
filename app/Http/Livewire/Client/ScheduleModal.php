<?php

namespace App\Http\Livewire\Client;

use App\Models\TrainingSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ScheduleModal extends Component
{
    public $visible = false;
    public $detailsSession = null;

    protected $listeners = [
        'openClientSchedule' => 'open',
    ];

    public function open()
    {
        $this->visible = true;
    }

    public function close()
    {
        $this->visible = false;
        $this->detailsSession = null;
    }

    public function showDetails($sessionId)
    {
        $this->detailsSession = TrainingSession::with(['trainer', 'booking.user'])->findOrFail($sessionId);
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

        return view('livewire.client.schedule-modal', [
            'sessions' => $sessions,
        ]);
    }
}
