<?php

namespace App\Http\Livewire\Admin;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class BookingsTable extends Component
{
    use WithPagination;

    public $status = '';
    public $date_from = null;
    public $date_to = null;
    public $trainer_id = '';
    public $client_id = '';

    public function render()
    {
        $bookings = Booking::with(['user', 'session.trainer'])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->client_id, fn($q) => $q->where('user_id', $this->client_id))
            ->when($this->trainer_id, function ($q) {
                $q->whereHas('session', fn($qq) => $qq->where('trainer_id', $this->trainer_id));
            })
            ->when($this->date_from, function ($q) {
                $q->whereHas('session', fn($qq) => $qq->whereDate('date', '>=', $this->date_from));
            })
            ->when($this->date_to, function ($q) {
                $q->whereHas('session', fn($qq) => $qq->whereDate('date', '<=', $this->date_to));
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $trainers = \App\Models\User::where('role','trainer')->orderBy('name')->get(['id','name']);
        $clients = \App\Models\User::where('role','client')->orderBy('name')->get(['id','name']);

        return view('livewire.admin.bookings-table', [
            'bookings' => $bookings,
            'trainers' => $trainers,
            'clients' => $clients,
        ]);
    }
}
