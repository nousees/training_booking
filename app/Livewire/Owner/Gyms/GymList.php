<?php

namespace App\Livewire\Owner\Gyms;

use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GymList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.owner.gyms.gym-list', [
            'gyms' => Gym::where('owner_id', auth()->id())->paginate(10)
        ])->layout('components.layouts.app', [
            'title' => 'Мои спортзалы'
        ]);
    }

    public function deleteGym($gymId)
    {
        $gym = Gym::where('owner_id', Auth::id())->findOrFail($gymId);
        $gym->delete();
        
        session()->flash('message', 'Gym deleted successfully.');
    }
}