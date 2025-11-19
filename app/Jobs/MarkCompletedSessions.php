<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class MarkCompletedSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = Carbon::today();

        Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereHas('session', function ($query) use ($today) {
                $query->where('date', '<', $today);
            })
            ->chunkById(100, function ($bookings) {
                foreach ($bookings as $booking) {
                    $booking->update(['status' => 'completed']);
                }
            });
    }
}

