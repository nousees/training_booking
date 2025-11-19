<?php

namespace App\Http\Livewire\Client;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReviewCreate extends Component
{
    public $bookingId;
    public $rating = 5;
    public $comment = '';

    protected function rules()
    {
        return [
            'bookingId' => ['required','integer','exists:bookings,id'],
            'rating' => ['required','integer','min:1','max:5'],
            'comment' => ['nullable','string','max:2000'],
        ];
    }

    public function mount($booking = null)
    {
        $this->bookingId = $booking;
    }

    public function save()
    {
        $this->validate();
        $booking = Booking::with('session')->findOrFail($this->bookingId);

        if ($booking->user_id !== Auth::id() || !$booking->isCompleted()) {
            abort(403);
        }

        if ($booking->review) {
            session()->flash('message', __('Отзыв уже существует'));
            return;
        }

        Review::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'trainer_id' => $booking->session->trainer_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        session()->flash('message', __('Отзыв сохранён'));
        return redirect()->route('profile');
    }

    public function render()
    {
        return view('livewire.client.review-create');
    }
}
