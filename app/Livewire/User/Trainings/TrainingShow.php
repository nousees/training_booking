<?php

namespace App\Livewire\User\Trainings;

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
        return view('livewire.user.trainings.training-show', [
            'training' => $this->training,
        ]);
    }
}


