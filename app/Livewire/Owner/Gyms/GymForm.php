<?php

namespace App\Livewire\Owner\Gyms;

use App\Models\Gym;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GymForm extends Component
{
    public $gymId;
    public $name;
    public $description;
    public $address;
    public $phone;
    public $email;
    public $opening_time;
    public $closing_time;
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'address' => 'required|string|max:500',
        'phone' => 'required|string|max:20',
        'email' => 'required|email',
        'opening_time' => 'required|date_format:H:i',
        'closing_time' => 'required|date_format:H:i',
        'is_active' => 'boolean',
    ];

    public function mount($gym = null)
    {
        if ($gym) {
            // Если $gym это строка (ID), находим модель
            if (is_string($gym) || is_numeric($gym)) {
                $gym = Gym::where('owner_id', Auth::id())->findOrFail($gym);
            }
            
            $this->gymId = $gym->id;
            $this->name = $gym->name;
            $this->description = $gym->description;
            $this->address = $gym->address;
            $this->phone = $gym->phone;
            $this->email = $gym->email;
            $this->opening_time = $gym->opening_time->format('H:i');
            $this->closing_time = $gym->closing_time->format('H:i');
            $this->is_active = $gym->is_active;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'is_active' => $this->is_active,
            'owner_id' => Auth::id(),
        ];

        if ($this->gymId) {
            $gym = Gym::where('owner_id', Auth::id())->findOrFail($this->gymId);
            $gym->update($data);
            session()->flash('message', 'Gym updated successfully.');
        } else {
            Gym::create($data);
            session()->flash('message', 'Gym created successfully.');
        }

        return redirect()->route('owner.gyms.index');
    }

    public function render()
    {
        return view('livewire.owner.gyms.gym-form');
    }
}