<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $user = Auth::user();
            $defaultRoute = $user?->role === 'owner'
                ? route('admin.dashboard', absolute: false)
                : ($user?->role === 'trainer'
                    ? route('trainer-panel.dashboard', absolute: false)
                    : route('trainers', absolute: false));

            $this->redirectIntended(default: $defaultRoute, navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        Спасибо за регистрацию! Прежде чем продолжить, пожалуйста, подтвердите ваш email по ссылке, которую мы отправили. Если письмо не пришло, мы можем отправить его повторно.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Новая ссылка для подтверждения отправлена на ваш email.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <x-primary-button wire:click="sendVerification">
            Отправить письмо ещё раз
        </x-primary-button>

        <button wire:click="logout" type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Выйти
        </button>
    </div>
</div>
