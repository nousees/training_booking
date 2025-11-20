<?php

namespace App\Http\Livewire\Admin;

use App\Models\TrainingSession;
use App\Models\User;
use Livewire\Component;

class TrainerSchedule extends Component
{
    public $trainerSearch = '';
    public $selectedTrainerId = null;
    public $showDetailsModal = false;
    public $detailsSession = null;

    public function selectTrainer($trainerId)
    {
        $this->selectedTrainerId = $trainerId;
    }

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
        $trainersQuery = User::where('role', 'trainer');

        if ($this->trainerSearch) {
            $term = $this->trainerSearch;
            $trainersQuery->where('name', 'ilike', "%{$term}%");
        }

        $trainers = $trainersQuery->orderBy('name')->limit(20)->get();

        $sessions = collect();
        $selectedTrainer = null;

        if ($this->selectedTrainerId) {
            $selectedTrainer = User::find($this->selectedTrainerId);
            if ($selectedTrainer) {
                $today = now()->toDateString();

                $sessions = TrainingSession::where('trainer_id', $selectedTrainer->id)
                    ->where('date', '>=', $today)
                    ->orderBy('date')
                    ->orderBy('start_time')
                    ->with(['booking.user'])
                    ->get();
            }
        }

        return view('livewire.admin.trainer-schedule', [
            'trainers' => $trainers,
            'sessions' => $sessions,
            'selectedTrainer' => $selectedTrainer,
        ]);
    }
}
