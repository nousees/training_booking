<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    public function index()
    {
        $gymIds = Gym::where('owner_id', Auth::id())->pluck('id');
        $trainers = Trainer::whereIn('gym_id', $gymIds)->with('gym')->paginate(10);
        return view('trainers.index', compact('trainers'));
    }

    public function create()
    {
        $gyms = Gym::where('owner_id', Auth::id())->get();
        return view('trainers.create', compact('gyms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'specialization' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'photo_path' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'gym_id' => 'required|exists:gyms,id',
        ]);


        $gym = Gym::where('owner_id', Auth::id())->findOrFail($request->gym_id);

        Trainer::create([
            'name' => $request->name,
            'bio' => $request->bio,
            'specialization' => $request->specialization,
            'experience_years' => $request->experience_years,
            'photo_path' => $request->photo_path,
            'is_active' => $request->has('is_active'),
            'gym_id' => $request->gym_id,
        ]);

        return redirect()->route('trainers.index')->with('success', 'Тренер успешно создан');
    }

    public function show(Trainer $trainer)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($trainer->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        return view('trainers.show', compact('trainer'));
    }

    public function edit(Trainer $trainer)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($trainer->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }
        $gyms = Gym::where('owner_id', Auth::id())->get();
        return view('trainers.edit', compact('trainer', 'gyms'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($trainer->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'specialization' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'photo_path' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'gym_id' => 'required|exists:gyms,id',
        ]);


        $newGym = Gym::where('owner_id', Auth::id())->findOrFail($request->gym_id);

        $trainer->update([
            'name' => $request->name,
            'bio' => $request->bio,
            'specialization' => $request->specialization,
            'experience_years' => $request->experience_years,
            'photo_path' => $request->photo_path,
            'is_active' => $request->has('is_active'),
            'gym_id' => $request->gym_id,
        ]);

        return redirect()->route('trainers.index')->with('success', 'Тренер успешно обновлен');
    }

    public function destroy(Trainer $trainer)
    {
        $gym = Gym::where('owner_id', Auth::id())->find($trainer->gym_id);
        if (!$gym) {
            abort(403, 'Доступ запрещен');
        }

        $trainer->delete();
        return redirect()->route('trainers.index')->with('success', 'Тренер успешно удален');
    }
}


