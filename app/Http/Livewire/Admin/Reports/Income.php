<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Booking;
use Livewire\Component;

class Income extends Component
{
    public function render()
    {
        $incomeByTrainer = Booking::whereIn('status', ['confirmed','completed'])
            ->with('session.trainer')
            ->get()
            ->groupBy(fn($b) => $b->session->trainer->name)
            ->map(fn($g) => $g->sum(fn($b) => (float) $b->session->price));

        return view('livewire.admin.reports.income', [
            'incomeByTrainer' => $incomeByTrainer,
        ]);
    }
}
