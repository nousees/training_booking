<?php

namespace App\Http\Livewire\Admin;

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsModeration extends Component
{
    use WithPagination;

    public function delete($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->delete();
        
        session()->flash('message', 'Review deleted!');
    }

    public function render()
    {
        $reviews = Review::with(['user', 'trainer', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.admin.reviews-moderation', [
            'reviews' => $reviews,
        ]);
    }
}

