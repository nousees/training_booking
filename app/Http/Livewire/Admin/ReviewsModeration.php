<?php

namespace App\Http\Livewire\Admin;

use App\Models\Review;
use App\Models\TrainerProfile;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsModeration extends Component
{
    use WithPagination;

    public $trainerId = '';

    public function delete($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->delete();
        
        session()->flash('message', 'Review deleted!');
    }

    public function render()
    {
        $query = Review::with(['user', 'trainer', 'booking'])
            ->orderBy('created_at', 'desc');

        if ($this->trainerId) {
            $query->where('trainer_id', $this->trainerId);
        }

        $reviews = $query->paginate(20);

        $trainers = TrainerProfile::with('user')
            ->orderBy('user_id')
            ->get();

        return view('livewire.admin.reviews-moderation', [
            'reviews' => $reviews,
            'trainers' => $trainers,
        ]);
    }
}

