<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Booking;
use Livewire\Component;

class Bookings extends Component
{
    public function downloadCsv()
    {
        $rows = Booking::with(['user','session.trainer'])->orderByDesc('created_at')->get()->map(function($b){
            return [
                'id' => $b->id,
                'client' => $b->user->name,
                'trainer' => $b->session->trainer->name,
                'date' => optional($b->session->date)->format('Y-m-d'),
                'status' => $b->status,
            ];
        });

        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Клиент','Тренер','Дата','Статус']);
            foreach ($rows as $r) {
                fputcsv($out, [$r['id'],$r['client'],$r['trainer'],$r['date'],$r['status']]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, 'bookings.csv');
    }

    public function render()
    {
        return view('livewire.admin.reports.bookings');
    }
}
