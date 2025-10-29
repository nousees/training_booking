<?php

namespace App\Livewire\Manager\Trainings;

use App\Models\Training;
use Livewire\Component;
use Livewire\WithPagination;

class TrainingList extends Component
{
    use WithPagination;

    public function render()
    {
        $trainings = Training::with(['gym', 'trainer'])->paginate(10);
        
        return view('livewire.manager.trainings.training-list', [
            'trainings' => $trainings
        ])->layout('components.layouts.app', [
            'title' => 'Тренировки'
        ]);
    }

    public function deleteTraining($trainingId)
    {
        $training = Training::findOrFail($trainingId);
        $training->delete();
        
        session()->flash('message', 'Тренировка успешно удалена.');
    }
}
