<?php

namespace App\Http\Livewire\Client;

use App\Models\TrainerProfile;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class TrainerList extends Component
{
    use WithPagination;

    public $search = '';
    public $specialization = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $minRating = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'specialization' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'minRating' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = TrainerProfile::with('user')
            ->whereHas('user', function ($q) {
                $q->where('role', 'trainer');
            });

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%');
            })->orWhere('bio', 'ilike', '%' . $this->search . '%');
        }

        if ($this->specialization) {
            $query->whereJsonContains('specializations', $this->specialization);
        }

        if ($this->minPrice) {
            $query->where('price_per_hour', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('price_per_hour', '<=', $this->maxPrice);
        }

        if ($this->minRating) {
            $query->where('rating', '>=', $this->minRating);
        }

        $trainers = $query->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $allSpecializations = TrainerProfile::whereNotNull('specializations')
            ->get()
            ->pluck('specializations')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return view('livewire.client.trainer-list', [
            'trainers' => $trainers,
            'allSpecializations' => $allSpecializations,
        ]);
    }
}

