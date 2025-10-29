<?php

namespace App\Livewire\User\Trainings;

use App\Models\Training;
use Livewire\Component;
use Livewire\WithPagination;

class TrainingList extends Component
{
    use WithPagination;

    public function render()
    {
        $trainings = Training::with(['gym', 'trainer'])->where('is_active', true)->paginate(10);
        
        return view('livewire.user.trainings.training-list', [
            'trainings' => $trainings
        ])->layout('components.layouts.app', [
            'title' => 'Доступные тренировки'
        ]);
    }
}
