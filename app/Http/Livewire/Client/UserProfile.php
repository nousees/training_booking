<?php

namespace App\Http\Livewire\Client;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class UserProfile extends Component
{
    use WithPagination, WithFileUploads;

    public $name = '';
    public $email = '';
    public $phone = '';
    public $timezone = 'Europe/Moscow';
    public $avatar;
    public $avatar_upload;
    public $notify_email = true;
    public $notify_in_app = true;
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    protected function rules()
    {
        return [
            'name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'timezone' => ['required','string'],
            'avatar_upload' => ['nullable','image','max:2048'],
            'notify_email' => ['boolean'],
            'notify_in_app' => ['boolean'],
        ];
    }

    protected function passwordRules()
    {
        return [
            'current_password' => ['required','string','min:6'],
            'password' => ['required','string','min:6','confirmed'],
        ];
    }

    public function mount()
    {
        $u = Auth::user();
        $this->name = $u->name;
        $this->email = $u->email;
        $this->phone = $u->phone;
        $this->timezone = $u->timezone ?: 'Europe/Moscow';
        $this->avatar = $u->avatar;
        $this->notify_email = (bool) ($u->notify_email ?? true);
        $this->notify_in_app = (bool) ($u->notify_in_app ?? true);
    }

    public function cancel($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$booking->canBeCanceled()) {
            session()->flash('error', 'Нельзя отменить это бронирование.');
            return;
        }

        $bookingService = app(\App\Services\BookingService::class);
        $bookingService->cancelBooking($booking);

        session()->flash('message', 'Бронирование успешно отменено!');
    }

    public function saveProfile()
    {
        $this->validate();
        $u = Auth::user();

        if ($this->avatar_upload) {
            $path = $this->avatar_upload->store('avatars', 'public');
            $this->avatar = $path;
        }

        $u->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'timezone' => $this->timezone,
            'avatar' => $this->avatar,
            'notify_email' => (bool) $this->notify_email,
            'notify_in_app' => (bool) $this->notify_in_app,
        ]);

        session()->flash('profile_saved', __('Профиль обновлён'));
    }

    public function changePassword()
    {
        $this->validate($this->passwordRules());
        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', __('Текущий пароль неверен'));
            return;
        }

        $user->update([
            'password' => $this->password,
        ]);

        $this->reset(['current_password','password','password_confirmation']);
        session()->flash('profile_saved', __('Пароль обновлён'));
    }

    public function render()
    {

        Booking::where('user_id', Auth::id())
            ->where('status', 'confirmed')
            ->whereHas('session', function ($q) {
                $q->whereDate('date', '<', now()->toDateString())
                  ->orWhere(function ($q2) {
                      $q2->whereDate('date', '=', now()->toDateString())
                         ->where('end_time', '<', now()->format('H:i:s'));
                  });
            })
            ->update(['status' => 'completed']);

        $base = Booking::where('user_id', Auth::id())
            ->with(['session.trainer','review']);

        $upcoming = (clone $base)
            ->join('training_sessions as ts', 'ts.id', '=', 'bookings.session_id')
            ->whereIn('bookings.status', ['pending','confirmed'])
            ->orderBy('ts.date', 'asc')
            ->orderBy('ts.start_time', 'asc')
            ->select('bookings.*')
            ->paginate(5, ['*'], 'upcoming');

        $completed = (clone $base)
            ->join('training_sessions as ts2', 'ts2.id', '=', 'bookings.session_id')
            ->where('bookings.status','completed')
            ->orderBy('ts2.date', 'desc')
            ->orderBy('ts2.start_time', 'desc')
            ->select('bookings.*')
            ->paginate(5, ['*'], 'completed');

        $canceled = (clone $base)
            ->join('training_sessions as ts3', 'ts3.id', '=', 'bookings.session_id')
            ->where('bookings.status','canceled')
            ->orderBy('ts3.date', 'desc')
            ->orderBy('ts3.start_time', 'desc')
            ->select('bookings.*')
            ->paginate(5, ['*'], 'canceled');

        $timezones = \DateTimeZone::listIdentifiers();
        $myReviews = \App\Models\Review::where('user_id', Auth::id())
            ->with('trainer')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'reviews');

        return view('livewire.client.user-profile', [
            'upcoming' => $upcoming,
            'completed' => $completed,
            'canceled' => $canceled,
            'timezones' => $timezones,
            'myReviews' => $myReviews,
        ]);
    }
}

