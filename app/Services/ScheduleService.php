<?php

namespace App\Services;

use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Support\Carbon;

class ScheduleService
{
    public function createSession(User $trainer, array $data): TrainingSession
    {
        if (!$trainer->isTrainer()) {
            throw new \Exception('User is not a trainer');
        }

        $this->validateNoOverlap($trainer, $data['date'], $data['start_time'], $data['end_time']);

        return TrainingSession::create([
            'trainer_id' => $trainer->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'location' => $data['location'],
            'price' => $data['price'],
            'status' => 'available',
        ]);
    }

    public function updateSession(TrainingSession $session, array $data): void
    {
        if ($session->status === 'booked') {
            throw new \Exception('Cannot update a booked session');
        }

        $this->validateNoOverlap(
            $session->trainer,
            $data['date'],
            $data['start_time'],
            $data['end_time'],
            $session->id
        );

        $session->update($data);
    }

    protected function validateNoOverlap(User $trainer, string $date, string $startTime, string $endTime, ?int $excludeId = null): void
    {
        $dateObj = Carbon::parse($date);
        $start = Carbon::parse($date . ' ' . $startTime);
        $end = Carbon::parse($date . ' ' . $endTime);

        if ($start >= $end) {
            throw new \Exception('Start time must be before end time');
        }

        // Check overlap against all sessions for that day (both available and booked)
        // Overlap exists iff (existing.start < newEnd) AND (existing.end > newStart)
        $query = TrainingSession::where('trainer_id', $trainer->id)
            ->where('date', $date)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end->format('H:i:s'))
                  ->where('end_time', '>', $start->format('H:i:s'));
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \Exception('Session overlaps with existing session');
        }
    }

    /**
     * Compute free time intervals for a trainer on a given date within a working window.
     * Returns array of [ ['start' => 'H:i', 'end' => 'H:i'] , ... ]
     */
    public function getFreeIntervals(User $trainer, string $date, string $workStart = '07:00', string $workEnd = '22:00'): array
    {
        $dayStart = Carbon::parse($date . ' ' . $workStart);
        $dayEnd = Carbon::parse($date . ' ' . $workEnd);

        if ($dayEnd->lte($dayStart)) {
            return [];
        }

        // Fetch all sessions that affect the day (both available and booked)
        $sessions = TrainingSession::where('trainer_id', $trainer->id)
            ->where('date', $date)
            ->orderBy('start_time')
            ->get();

        $blocked = [];
        foreach ($sessions as $s) {
            $sStart = Carbon::parse($date . ' ' . Carbon::parse($s->start_time)->format('H:i:s'));
            $sEnd = Carbon::parse($date . ' ' . Carbon::parse($s->end_time)->format('H:i:s'));
            // Clip to working window
            if ($sEnd->lte($dayStart) || $sStart->gte($dayEnd)) {
                continue;
            }
            $blocked[] = [
                'start' => $sStart->max($dayStart)->clone(),
                'end' => $sEnd->min($dayEnd)->clone(),
            ];
        }

        // Merge overlapping blocked intervals
        usort($blocked, function ($a, $b) { return $a['start'] <=> $b['start']; });
        $merged = [];
        foreach ($blocked as $b) {
            if (empty($merged)) {
                $merged[] = $b;
                continue;
            }
            $last = &$merged[count($merged)-1];
            if ($b['start']->lte($last['end'])) {
                if ($b['end']->gt($last['end'])) {
                    $last['end'] = $b['end']->clone();
                }
            } else {
                $merged[] = $b;
            }
        }

        // Build free intervals as gaps between merged blocks within [dayStart, dayEnd]
        $free = [];
        $cursor = $dayStart->clone();
        foreach ($merged as $m) {
            if ($cursor->lt($m['start'])) {
                $free[] = [
                    'start' => $cursor->format('H:i'),
                    'end' => $m['start']->format('H:i'),
                ];
            }
            $cursor = $m['end']->clone();
        }
        if ($cursor->lt($dayEnd)) {
            $free[] = [
                'start' => $cursor->format('H:i'),
                'end' => $dayEnd->format('H:i'),
            ];
        }

        return $free;
    }
}

