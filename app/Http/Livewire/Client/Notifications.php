<?php

namespace App\Http\Livewire\Client;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Notifications extends Component
{
    use WithPagination;

    public $show = 'all'; // all|unread

    public function markRead($id): void
    {
        $n = Notification::where('user_id', Auth::id())->findOrFail($id);
        $n->markAsRead();
        session()->flash('message', __('Уведомление отмечено как прочитанное'));
    }

    public function render()
    {
        $query = Notification::where('user_id', Auth::id())->orderByDesc('created_at');
        if ($this->show === 'unread') {
            $query->unread();
        }
        $items = $query->paginate(15);
        return view('livewire.client.notifications', [ 'items' => $items ]);
    }
}
