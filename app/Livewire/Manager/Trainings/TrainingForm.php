<?php

namespace App\Livewire\Manager\Trainings;

use App\Models\Training;
use App\Models\Gym;
use App\Models\Trainer;
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

            if (is_string($training) || is_numeric($training)) {
                $training = \App\Models\Training::findOrFail($training);
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
            $training = Training::findOrFail($this->trainingId);
            $training->update($data);
            session()->flash('message', 'Тренировка успешно обновлена.');
        } else {
            Training::create($data);
            session()->flash('message', 'Тренировка успешно создана.');
        }

        return redirect()->route('manager.trainings.index');
    }

    public function render()
    {
        $gyms = Gym::all();
        $trainers = collect();
        
        if ($this->gym_id) {
            $trainers = Trainer::where('gym_id', $this->gym_id)->get();
        }
        
        return view('livewire.manager.trainings.training-form', [
            'gyms' => $gyms,
            'trainers' => $trainers
        ]);
    }
}
