<?php

namespace App\Livewire\Owner\Trainings;

use App\Models\Training;
use App\Models\Gym;
use App\Models\Trainer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TrainingForm extends Component
{
    public $trainingId;
    public $name;
    public $description;
    public $duration_minutes;
    public $price;
    public $max_participants;
    public $is_active = true;
    public $gym_id;
    public $trainer_id;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'duration_minutes' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'max_participants' => 'required|integer|min:1',
        'is_active' => 'boolean',
        'gym_id' => 'required|exists:gyms,id',
        'trainer_id' => 'required|exists:trainers,id',
    ];

    public function mount($training = null)
    {
        if ($training) {
            // Если $training это строка (ID), находим модель
            if (is_string($training) || is_numeric($training)) {
                $gymIds = \App\Models\Gym::where('owner_id', Auth::id())->pluck('id');
                $training = \App\Models\Training::whereIn('gym_id', $gymIds)->findOrFail($training);
            }
            
            $this->trainingId = $training->id;
            $this->name = $training->name;
            $this->description = $training->description;
            $this->duration_minutes = $training->duration_minutes;
            $this->price = $training->price;
            $this->max_participants = $training->max_participants;
            $this->is_active = $training->is_active;
            $this->gym_id = $training->gym_id;
            $this->trainer_id = $training->trainer_id;
        }
    }

    public function updatedGymId($value)
    {
        $this->trainer_id = null;
    }

    public function save()
    {
        $this->validate();

        // Проверяем, что спортзал принадлежит текущему владельцу
        $gym = Gym::where('owner_id', Auth::id())->findOrFail($this->gym_id);
        
        // Проверяем, что тренер принадлежит этому спортзалу
        $trainer = Trainer::where('gym_id', $this->gym_id)->findOrFail($this->trainer_id);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
            'max_participants' => $this->max_participants,
            'is_active' => $this->is_active,
            'gym_id' => $this->gym_id,
            'trainer_id' => $this->trainer_id,
        ];

        if ($this->trainingId) {
            $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
            $training = Training::whereIn('gym_id', $gymIds)->findOrFail($this->trainingId);
            $training->update($data);
            session()->flash('message', 'Тренировка успешно обновлена.');
        } else {
            Training::create($data);
            session()->flash('message', 'Тренировка успешно создана.');
        }

        return redirect()->route('owner.trainings.index');
    }

    public function render()
    {
        $gyms = Gym::where('owner_id', Auth::id())->get();
        $trainers = collect();
        
        if ($this->gym_id) {
            $trainers = Trainer::where('gym_id', $this->gym_id)->get();
        }
        
        return view('livewire.owner.trainings.training-form', [
            'gyms' => $gyms,
            'trainers' => $trainers
        ]);
    }
}
