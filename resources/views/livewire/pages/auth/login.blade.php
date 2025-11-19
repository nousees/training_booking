<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        // Блокировка входа для заблокированных пользователей
        if (auth()->user()?->isBlocked()) {
            auth()->logout();
            Session::invalidate();
            Session::regenerateToken();
            $this->addError('form.email', 'Ваш аккаунт заблокирован. Обратитесь к поддержке.');
            return;
        }

        $user = auth()->user();
        $defaultRoute = $user?->isOwner()
            ? route('admin.dashboard', absolute: false)
            : ($user?->isTrainer()
                ? route('trainer-panel.dashboard', absolute: false)
                : route('trainers', absolute: false));

        $this->redirectIntended(default: $defaultRoute, navigate: true);
    }
}; ?>

<div>
    <!-- Статус сессии -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Пароль -->
        <div class="mt-4">
            <x-input-label for="password" value="Пароль" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Запомнить меня -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Запомнить меня</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" href="{{ route('password.request') }}" wire:navigate>
                    Забыли пароль?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Войти
            </x-primary-button>
        </div>
    </form>
</div>
