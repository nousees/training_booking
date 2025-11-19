<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Мой аккаунт администратора') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">{{ __('Профиль владельца платформы') }}</h1>

                    <div class="space-y-4">
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Имя') }}</div>
                            <div class="text-lg text-gray-900">{{ auth()->user()->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Email</div>
                            <div class="text-lg text-gray-900">{{ auth()->user()->email }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">{{ __('Роль') }}</div>
                            <div class="text-lg text-gray-900">{{ __('Владелец платформы') }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-500">{{ __('Дата регистрации') }}</div>
                                <div class="text-lg text-gray-900">{{ auth()->user()->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">{{ __('Последний вход') }}</div>
                                <div class="text-lg text-gray-900">{{ auth()->user()->last_login_at ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('admin.settings') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">{{ __('Перейти к настройкам') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
