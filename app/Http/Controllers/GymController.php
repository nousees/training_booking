<?php

namespace App\Http\Controllers;

use App\Models\Gym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GymController extends Controller
{
    public function index()
    {
        $gyms = Gym::where('owner_id', Auth::id())->paginate(10);
        return view('gyms.index', compact('gyms'));
    }

    public function create()
    {
        return view('gyms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        Gym::create([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'is_active' => $request->has('is_active'),
            'owner_id' => Auth::id(),
        ]);

        return redirect()->route('gyms.index')->with('success', 'Спортзал успешно создан');
    }

    public function show(Gym $gym)
    {
        if ($gym->owner_id !== Auth::id()) {
            abort(403, 'Доступ запрещен');
        }
        return view('gyms.show', compact('gym'));
    }

    public function edit(Gym $gym)
    {
        if ($gym->owner_id !== Auth::id()) {
            abort(403, 'Доступ запрещен');
        }
        return view('gyms.edit', compact('gym'));
    }

    public function update(Request $request, Gym $gym)
    {
        if ($gym->owner_id !== Auth::id()) {
            abort(403, 'Доступ запрещен');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $gym->update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('gyms.index')->with('success', 'Спортзал успешно обновлен');
    }

    public function destroy(Gym $gym)
    {
        if ($gym->owner_id !== Auth::id()) {
            abort(403, 'Доступ запрещен');
        }

        $gym->delete();
        return redirect()->route('gyms.index')->with('success', 'Спортзал успешно удален');
    }
}

