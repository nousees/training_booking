<?php

namespace App\Livewire\User\Bookings;

use App\Models\Booking;
use App\Models\Training;
use App\Models\Gym;
use App\Models\Trainer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookingForm extends Component
{
    public $bookingId;
    public $gym_id;
    public $trainer_id;
    public $training_id;
    public $start_time;
    public $end_time;
    public $status = 'pending';
    public $notes;

    protected $rules = [
        'gym_id' => 'required|exists:gyms,id',
        'trainer_id' => 'required|exists:trainers,id',
        'training_id' => 'required|exists:trainings,id',
        'start_time' => 'required|date|after:now',
        'end_time' => 'required|date|after:start_time',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount($booking = null)
    {
        if ($booking) {
            // Если $booking это строка (ID), находим модель
            if (is_string($booking) || is_numeric($booking)) {
                $booking = \App\Models\Booking::where('user_id', Auth::id())->findOrFail($booking);
            }
            
            $this->bookingId = $booking->id;
            $this->gym_id = $booking->training->gym_id;
            $this->trainer_id = $booking->training->trainer_id;
            $this->training_id = $booking->training_id;
            $this->start_time = $booking->start_time->format('Y-m-d\TH:i');
            $this->end_time = $booking->end_time->format('Y-m-d\TH:i');
            $this->status = $booking->status;
            $this->notes = $booking->notes;
        }
    }

    public function updatedGymId($value)
    {
        $this->trainer_id = null;
        $this->training_id = null;
    }

    public function updatedTrainerId($value)
    {
        $this->training_id = null;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'training_id' => $this->training_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->bookingId) {
            $booking = Booking::where('user_id', Auth::id())->findOrFail($this->bookingId);
            $booking->update($data);
            session()->flash('message', 'Бронирование успешно обновлено.');
        } else {
            Booking::create($data);
            session()->flash('message', 'Бронирование успешно создано.');
        }

        return redirect()->route('user.bookings.index');
    }

    public function render()
    {
        $gyms = Gym::where('is_active', true)->get();
        
        $trainers = collect();
        if ($this->gym_id) {
            $trainers = Trainer::where('gym_id', $this->gym_id)
                ->where('is_active', true)
                ->get();
        }
        
        $trainings = collect();
        if ($this->gym_id && $this->trainer_id) {
            $trainings = Training::where('gym_id', $this->gym_id)
                ->where('trainer_id', $this->trainer_id)
                ->where('is_active', true)
                ->get();
        }
        
        return view('livewire.user.bookings.booking-form', [
            'gyms' => $gyms,
            'trainers' => $trainers,
            'trainings' => $trainings
        ]);
    }
}
