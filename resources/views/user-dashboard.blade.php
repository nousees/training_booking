<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Панель клиента
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Панель клиента</h1>
                    <p>Добро пожаловать, {{ Auth::user()->name }}!</p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Доступные тренировки</h3>
                            <p class="text-2xl">{{ \App\Models\Training::where('is_active', true)->count() }}</p>
                            <a href="{{ route('user.trainings.index') }}" class="text-purple-600 hover:text-purple-800 text-sm">Просмотр тренировок</a>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Мои бронирования</h3>
                            <p class="text-2xl">{{ \App\Models\Booking::where('user_id', Auth::id())->count() }}</p>
                            <a href="{{ route('user.bookings.index') }}" class="text-yellow-600 hover:text-yellow-800 text-sm">Управление бронированиями</a>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Активные спортзалы</h3>
                            <p class="text-2xl">{{ \App\Models\Gym::where('is_active', true)->count() }}</p>
                            <a href="{{ route('user.trainings.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">Выбрать тренировку</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>