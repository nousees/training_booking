<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Панель владельца
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Панель владельца спортзала</h1>
                    <p>Добро пожаловать, {{ Auth::user()->name }}!</p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Мои спортзалы</h3>
                            <p class="text-2xl">{{ Auth::user()->ownedGyms->count() }}</p>
                            <a href="{{ route('owner.gyms.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">Управление спортзалами</a>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Тренеры</h3>
                            <p class="text-2xl">{{ \App\Models\Trainer::whereIn('gym_id', Auth::user()->ownedGyms->pluck('id'))->count() }}</p>
                            <a href="{{ route('owner.trainers.index') }}" class="text-green-600 hover:text-green-800 text-sm">Управление тренерами</a>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Тренировки</h3>
                            <p class="text-2xl">{{ \App\Models\Training::whereIn('gym_id', Auth::user()->ownedGyms->pluck('id'))->count() }}</p>
                            <a href="{{ route('owner.trainings.index') }}" class="text-purple-600 hover:text-purple-800 text-sm">Управление тренировками</a>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Бронирования</h3>
                            <p class="text-2xl">{{ \App\Models\Booking::whereIn('training_id', \App\Models\Training::whereIn('gym_id', Auth::user()->ownedGyms->pluck('id'))->pluck('id'))->count() }}</p>
                            <a href="{{ route('owner.bookings.index') }}" class="text-yellow-600 hover:text-yellow-800 text-sm">Управление бронированиями</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>