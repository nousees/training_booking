<?php

namespace App\Livewire\Owner\Trainings;

use App\Models\Training;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TrainingShow extends Component
{
    public Training $training;

    public function mount(Training $training)
    {

        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        
        $this->training = $training;
    }

    public function render()
    {
        return view('livewire.owner.trainings.training-show', [
            'training' => $this->training,
        ]);
    }
}


