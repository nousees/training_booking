<?php

namespace App\Livewire\Manager\Trainings;

use App\Models\Training;
use Livewire\Component;

class TrainingShow extends Component
{
    public Training $training;

    public function mount(Training $training)
    {
        $this->training = $training;
    }

    public function render()
    {
        return view('livewire.manager.trainings.training-show', [
            'training' => $this->training,
        ]);
    }
}

