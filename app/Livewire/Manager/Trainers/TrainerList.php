<?php

namespace App\Livewire\Manager\Trainers;

use App\Models\Trainer;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TrainerList extends Component
{
    use WithPagination;

    public function render()
    {
        // Менеджер может видеть всех тренеров из всех спортзалов
        $trainers = Trainer::with('gym')->paginate(10);
        
        return view('livewire.manager.trainers.trainer-list', [
            'trainers' => $trainers
        ])->layout('components.layouts.app', [
            'title' => 'Тренеры'
        ]);
    }

    public function deleteTrainer($trainerId)
    {
        $trainer = Trainer::findOrFail($trainerId);
        $trainer->delete();
        
        session()->flash('message', 'Тренер успешно удален.');
    }
}
