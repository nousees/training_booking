<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Админ-панель') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Управление платформой</h1>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('admin.users') }}" 
                           class="p-6 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                            <h3 class="text-lg font-semibold text-indigo-900 mb-2">Пользователи</h3>
                            <p class="text-indigo-700">Управление пользователями и ролями</p>
                        </a>
                        <a href="{{ route('admin.reviews') }}" 
                           class="p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <h3 class="text-lg font-semibold text-green-900 mb-2">Отзывы</h3>
                            <p class="text-green-700">Модерация отзывов</p>
                        </a>
                        <a href="{{ route('admin.settings') }}" 
                           class="p-6 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <h3 class="text-lg font-semibold text-purple-900 mb-2">Настройки</h3>
                            <p class="text-purple-700">Настройки платформы</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

