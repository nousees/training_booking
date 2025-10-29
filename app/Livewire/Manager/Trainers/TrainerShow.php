<?php

namespace App\Livewire\Manager\Trainers;

use App\Models\Trainer;
use Livewire\Component;

class TrainerShow extends Component
{
    public Trainer $trainer;

    public function mount(Trainer $trainer)
    {
        $this->trainer = $trainer;
    }

    public function render()
    {
        return view('livewire.manager.trainers.trainer-show', [
            'trainer' => $this->trainer,
        ]);
    }
}

