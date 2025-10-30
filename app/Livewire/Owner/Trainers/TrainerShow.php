<?php

namespace App\Livewire\Owner\Trainers;

use App\Models\Trainer;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TrainerShow extends Component
{
    public Trainer $trainer;

    public function mount(Trainer $trainer)
    {
        // Проверяем, что тренер принадлежит спортзалу владельца
        $gym = Gym::where('owner_id', Auth::id())->find($trainer->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        
        $this->trainer = $trainer;
    }

    public function render()
    {
        return view('livewire.owner.trainers.trainer-show', [
            'trainer' => $this->trainer,
        ]);
    }
}


