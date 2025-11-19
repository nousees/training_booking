<?php

namespace App\Http\Livewire\Client;

use App\Models\TrainingSession;
use App\Models\User;
use Livewire\Component;

class TrainerProfileView extends Component
{
    public User $trainer;
    public $selectedDate = null;
    public $selectedSession = null;

    public function mount(User $trainer)
    {
        if (!$trainer->isTrainer()) {
            abort(404);
        }
        
        $this->trainer = $trainer->load('trainerProfile');
    }

    public function selectSession($sessionId)
    {
        $this->selectedSession = $sessionId;
    }

    public function render()
    {
        $profile = $this->trainer->trainerProfile;
        
        $today = now()->toDateString();
        $nowTime = now()->format('H:i:s');
        $endDate = now()->addDays(7)->toDateString();

        $sessions = TrainingSession::where('trainer_id', $this->trainer->id)
            ->where('status', 'available')
            ->where(function ($q) use ($today, $nowTime, $endDate) {
                $q->whereBetween('date', [$today, $endDate])
                  ->where(function ($qq) use ($today, $nowTime) {
                      $qq->where('date', '>', $today)
                         ->orWhere(function ($qqq) use ($today, $nowTime) {
                             $qqq->where('date', '=', $today)
                                 ->where('start_time', '>', $nowTime);
                         });
                  });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('date');

        return view('livewire.client.trainer-profile-view', [
            'profile' => $profile,
            'sessions' => $sessions,
        ]);
    }
}

