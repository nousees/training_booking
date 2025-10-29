<?php

namespace App\Livewire\Owner\Bookings;

use App\Models\Booking;
use App\Models\Training;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BookingForm extends Component
{
    public $bookingId;
    public $user_id;
    public $training_id;
    public $start_time;
    public $end_time;
    public $status = 'pending';
    public $notes;

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'training_id' => 'required|exists:trainings,id',
        'start_time' => 'required|date|after:now',
        'end_time' => 'required|date|after:start_time',
        'status' => 'required|in:pending,confirmed,cancelled,completed',
        'notes' => 'nullable|string|max:1000',
    ];

    public function mount($booking = null)
    {
        if ($booking) {
            // Если $booking это строка (ID), находим модель
            if (is_string($booking) || is_numeric($booking)) {
                $gymIds = \App\Models\Gym::where('owner_id', Auth::id())->pluck('id');
                $trainingIds = \App\Models\Training::whereIn('gym_id', $gymIds)->pluck('id');
                $booking = \App\Models\Booking::whereIn('training_id', $trainingIds)->findOrFail($booking);
            }
            
            $this->bookingId = $booking->id;
            $this->user_id = $booking->user_id;
            $this->training_id = $booking->training_id;
            $this->start_time = $booking->start_time->format('Y-m-d\TH:i');
            $this->end_time = $booking->end_time->format('Y-m-d\TH:i');
            $this->status = $booking->status;
            $this->notes = $booking->notes;
        }
    }

    public function save()
    {
        $this->validate();

        // Проверяем, что тренировка принадлежит спортзалу владельца
        $training = Training::findOrFail($this->training_id);
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            $this->addError('training_id', 'Тренировка не найдена или доступ запрещен');
            return;
        }

        $data = [
            'user_id' => $this->user_id,
            'training_id' => $this->training_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->bookingId) {
            $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
            $trainingIds = Training::whereIn('gym_id', $gymIds)->pluck('id');
            $booking = Booking::whereIn('training_id', $trainingIds)->findOrFail($this->bookingId);
            $booking->update($data);
            session()->flash('message', 'Бронирование успешно обновлено.');
        } else {
            Booking::create($data);
            session()->flash('message', 'Бронирование успешно создано.');
        }

        return redirect()->route('owner.bookings.index');
    }

    public function render()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainings = Training::whereIn('gym_id', $gymIds)->with(['gym', 'trainer'])->get();
        $users = User::all();
        
        return view('livewire.owner.bookings.booking-form', [
            'trainings' => $trainings,
            'users' => $users
        ]);
    }
}
