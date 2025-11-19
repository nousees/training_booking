<?php

namespace App\Livewire\Owner\Trainers;

use App\Models\Trainer;
use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TrainerForm extends Component
{
    public $trainerId;
    public $name;
    public $bio;
    public $specialization;
    public $experience_years;
    public $photo_path;
    public $is_active = true;
    public $gym_id;

    protected $rules = [
        'name' => 'required|string|max:255',
        'bio' => 'nullable|string',
        'specialization' => 'required|string|max:255',
        'experience_years' => 'required|integer|min:0',
        'photo_path' => 'nullable|string|max:500',
        'is_active' => 'boolean',
        'gym_id' => 'required|exists:gyms,id',
    ];

    public function mount($trainer = null)
    {
        if ($trainer) {

            if (is_string($trainer) || is_numeric($trainer)) {
                $gymIds = \App\Models\Gym::where('owner_id', Auth::id())->pluck('id');
                $trainer = \App\Models\Trainer::whereIn('gym_id', $gymIds)->findOrFail($trainer);
            }
            
            $this->trainerId = $trainer->id;
            $this->name = $trainer->name;
            $this->bio = $trainer->bio;
            $this->specialization = $trainer->specialization;
            $this->experience_years = $trainer->experience_years;
            $this->photo_path = $trainer->photo_path;
            $this->is_active = $trainer->is_active;
            $this->gym_id = $trainer->gym_id;
        }
    }

    public function save()
    {
        $this->validate();


        $gym = Gym::where('owner_id', Auth::id())->findOrFail($this->gym_id);

        $data = [
            'name' => $this->name,
            'bio' => $this->bio,
            'specialization' => $this->specialization,
            'experience_years' => $this->experience_years,
            'photo_path' => $this->photo_path,
            'is_active' => $this->is_active,
            'gym_id' => $this->gym_id,
        ];

        if ($this->trainerId) {
            $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
            $trainer = Trainer::whereIn('gym_id', $gymIds)->findOrFail($this->trainerId);
            $trainer->update($data);
            session()->flash('message', 'Тренер успешно обновлен.');
        } else {
            Trainer::create($data);
            session()->flash('message', 'Тренер успешно создан.');
        }

        return redirect()->route('owner.trainers.index');
    }

    public function render()
    {
        $gyms = Gym::where('owner_id', Auth::id())->get();
        return view('livewire.owner.trainers.trainer-form', [
            'gyms' => $gyms
        ]);
    }
}
