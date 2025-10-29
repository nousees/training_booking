<?php

namespace App\Livewire\Owner\Trainers;

use App\Models\Trainer;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TrainerList extends Component
{
    use WithPagination;

    public function render()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainers = Trainer::whereIn('gym_id', $gymIds)->with('gym')->paginate(10);
        
        return view('livewire.owner.trainers.trainer-list', [
            'trainers' => $trainers
        ])->layout('components.layouts.app', [
            'title' => 'Мои тренеры'
        ]);
    }

    public function deleteTrainer($trainerId)
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainer = Trainer::whereIn('gym_id', $gymIds)->findOrFail($trainerId);
        $trainer->delete();
        
        session()->flash('message', 'Тренер успешно удален.');
    }
}
