<?php

namespace App\Livewire\Owner\Gyms;

use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GymShow extends Component
{
    public Gym $gym;

    public function mount(Gym $gym)
    {

        if ($gym->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $this->gym = $gym;
    }

    public function render()
    {
        return view('livewire.owner.gyms.gym-show', [
            'gym' => $this->gym,
        ]);
    }
}