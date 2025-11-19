<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Gym;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function index()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainings = Training::whereIn('gym_id', $gymIds)->with(['gym', 'trainer'])->paginate(10);
        return view('trainings.index', compact('trainings'));
    }

    public function create()
    {
        $gyms = Gym::where('owner_id', Auth::id())->get();
        $trainers = Trainer::whereIn('gym_id', $gyms->pluck('id'))->get();
        return view('trainings.create', compact('gyms', 'trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'max_participants' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'gym_id' => 'required|exists:gyms,id',
            'trainer_id' => 'required|exists:trainers,id',
        ]);


        $gym = Gym::where('owner_id', Auth::id())->findOrFail($request->gym_id);
        

        $trainer = Trainer::where('gym_id', $request->gym_id)->findOrFail($request->trainer_id);

        Training::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'price' => $request->price,
            'max_participants' => $request->max_participants,
            'is_active' => $request->has('is_active'),
            'gym_id' => $request->gym_id,
            'trainer_id' => $request->trainer_id,
        ]);

        return redirect()->route('trainings.index')->with('success', 'Тренировка успешно создана');
    }

    public function show(Training $training)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        return view('trainings.show', compact('training'));
    }

    public function edit(Training $training)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        $gyms = Gym::where('owner_id', Auth::id())->get();
        $trainers = Trainer::whereIn('gym_id', $gyms->pluck('id'))->get();
        return view('trainings.edit', compact('training', 'gyms', 'trainers'));
    }

    public function update(Request $request, Training $training)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'max_participants' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'gym_id' => 'required|exists:gyms,id',
            'trainer_id' => 'required|exists:trainers,id',
        ]);


        $newGym = Gym::where('owner_id', Auth::id())->findOrFail($request->gym_id);
        

        $trainer = Trainer::where('gym_id', $request->gym_id)->findOrFail($request->trainer_id);

        $training->update([
            'name' => $request->name,
            'description' => $request->description,
            'duration_minutes' => $request->duration_minutes,
            'price' => $request->price,
            'max_participants' => $request->max_participants,
            'is_active' => $request->has('is_active'),
            'gym_id' => $request->gym_id,
            'trainer_id' => $request->trainer_id,
        ]);

        return redirect()->route('trainings.index')->with('success', 'Тренировка успешно обновлена');
    }

    public function destroy(Training $training)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($training->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }

        $training->delete();
        return redirect()->route('trainings.index')->with('success', 'Тренировка успешно удалена');
    }
}


