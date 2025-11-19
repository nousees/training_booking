<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Training;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainingIds = Training::whereIn('gym_id', $gymIds)->pluck('id');
        $bookings = Booking::whereIn('training_id', $trainingIds)->with(['user', 'training.trainer', 'training.gym'])->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainings = Training::whereIn('gym_id', $gymIds)->with(['gym', 'trainer'])->get();
        return view('bookings.create', compact('trainings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'training_id' => 'required|exists:trainings,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000',
        ]);


        $training = Training::findOrFail($request->training_id);
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }

        Booking::create([
            'user_id' => $request->user_id,
            'training_id' => $request->training_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Бронирование успешно создано');
    }

    public function show(Booking $booking)
    {
        $training = Training::find($booking->training_id);
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $training = Training::find($booking->training_id);
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainings = Training::whereIn('gym_id', $gymIds)->with(['gym', 'trainer'])->get();
        return view('bookings.edit', compact('booking', 'trainings'));
    }

    public function update(Request $request, Booking $booking)
    {
        $training = Training::find($booking->training_id);
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'training_id' => 'required|exists:trainings,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000',
        ]);


        $newTraining = Training::findOrFail($request->training_id);
        $newGym = Gym::where('owner_id', Auth::id())->find($newTraining->gym_id);
        if (!$newGym) {
            abort(403, 'Доступ запрещен');
        }

        $booking->update([
            'user_id' => $request->user_id,
            'training_id' => $request->training_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Бронирование успешно обновлено');
    }

    public function destroy(Booking $booking)
    {
        $training = Training::find($booking->training_id);
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }

        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Бронирование успешно удалено');
    }
}


