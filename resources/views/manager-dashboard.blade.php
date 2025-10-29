<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Панель менеджера
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Панель менеджера спортзала</h1>
                    <p>Добро пожаловать, {{ Auth::user()->name }}!</p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Тренеры</h3>
                            <p class="text-2xl">{{ \App\Models\Trainer::count() }}</p>
                            <a href="{{ route('manager.trainers.index') }}" class="text-green-600 hover:text-green-800 text-sm">Управление тренерами</a>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Тренировки</h3>
                            <p class="text-2xl">{{ \App\Models\Training::count() }}</p>
                            <a href="{{ route('manager.trainings.index') }}" class="text-purple-600 hover:text-purple-800 text-sm">Управление тренировками</a>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Бронирования</h3>
                            <p class="text-2xl">{{ \App\Models\Booking::count() }}</p>
                            <a href="{{ route('manager.bookings.index') }}" class="text-yellow-600 hover:text-yellow-800 text-sm">Управление бронированиями</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>