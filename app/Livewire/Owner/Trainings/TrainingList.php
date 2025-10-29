<?php

namespace App\Livewire\Owner\Trainings;

use App\Models\Training;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TrainingList extends Component
{
    use WithPagination;

    public function render()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainings = Training::whereIn('gym_id', $gymIds)->with(['gym', 'trainer'])->paginate(10);
        
        return view('livewire.owner.trainings.training-list', [
            'trainings' => $trainings
        ])->layout('components.layouts.app', [
            'title' => 'Мои тренировки'
        ]);
    }

    public function deleteTraining($trainingId)
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $training = Training::whereIn('gym_id', $gymIds)->findOrFail($trainingId);
        $training->delete();
        
        session()->flash('message', 'Тренировка успешно удалена.');
    }
}
