<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class TrainersTable extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $trainers = User::where('role', 'trainer')
            ->when($this->search, fn($q) => $q->where('name', 'ilike', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.trainers-table', [
            'trainers' => $trainers,
        ]);
    }
}
