<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use Illuminate\Support\Facades\Auth;

class ClientScheduleController extends Controller
{
    public function __invoke()
    {
        $today = now()->toDateString();
        $until = now()->copy()->addDays(6)->toDateString();

        $sessions = TrainingSession::whereHas('booking', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereBetween('date', [$today, $until])
            ->orderBy('date')
            ->orderBy('start_time')
            ->with(['trainer', 'booking'])
            ->get();

        return view('livewire.client.schedule', compact('sessions'));
    }
}
