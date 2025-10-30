<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Доступные тренировки
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Доступные тренировки</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($trainings as $training)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $training->name }}</h3>
                                <p class="text-sm text-gray-600 mb-4">{{ $training->description ?: 'Описание не указано' }}</p>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Тренер:</span>
                                        <span class="text-sm font-medium">{{ $training->trainer->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Спортзал:</span>
                                        <span class="text-sm font-medium">{{ $training->gym->name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Длительность:</span>
                                        <span class="text-sm font-medium">{{ $training->duration_minutes }} мин</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Цена:</span>
                                        <span class="text-sm font-medium text-green-600">{{ number_format($training->price, 2) }} ₽</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Макс. участников:</span>
                                        <span class="text-sm font-medium">{{ $training->max_participants }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('user.trainings.show', $training) }}" class="flex-1 bg-blue-500 hover:bg-blue-700 text-white text-center py-2 px-4 rounded text-sm">
                                        Подробнее
                                    </a>
                                    <a href="{{ route('user.bookings.create') }}?training_id={{ $training->id }}" class="flex-1 bg-green-500 hover:bg-green-700 text-white text-center py-2 px-4 rounded text-sm">
                                        Забронировать
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $trainings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


