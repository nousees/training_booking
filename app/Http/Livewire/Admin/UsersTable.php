<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateRole($userId, $newRole)
    {

        if (!in_array($newRole, ['client', 'trainer'], true)) {
            session()->flash('error', 'Можно назначать только роли Клиент и Тренер.');
            return;
        }

        $user = User::findOrFail($userId);


        if ($user->role === 'owner' || auth()->id() === $user->id) {
            session()->flash('error', 'Этому пользователю нельзя изменить роль.');
            return;
        }

        $user->update(['role' => $newRole]);

        session()->flash('message', 'Роль пользователя обновлена');
    }

    public function toggleBlock($userId)
    {
        $user = User::findOrFail($userId);


        if ($user->role === 'owner' || auth()->id() === $user->id) {
            session()->flash('error', 'Нельзя блокировать этого пользователя');
            return;
        }

        $user->blocked_at = $user->blocked_at ? null : now();
        $user->save();

        session()->flash('message', $user->blocked_at ? 'Пользователь заблокирован' : 'Пользователь разблокирован');
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('email', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('livewire.admin.users-table', [
            'users' => $users,
        ]);
    }
}

