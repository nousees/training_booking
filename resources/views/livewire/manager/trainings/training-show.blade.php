<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Информация о тренировке
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">{{ $training->name }}</h1>
                        <div class="space-x-3">
                            <a href="{{ route('manager.trainings.edit', $training) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Редактировать
                            </a>
                            <a href="{{ route('manager.trainings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Назад к списку
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Основная информация</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Название</dt>
                                    <dd class="text-sm text-gray-900">{{ $training->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Тренер</dt>
                                    <dd class="text-sm text-gray-900">{{ $training->trainer->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Спортзал</dt>
                                    <dd class="text-sm text-gray-900">{{ $training->gym->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Длительность</dt>
                                    <dd class="text-sm text-gray-900">{{ $training->duration_minutes }} минут</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Цена</dt>
                                    <dd class="text-sm text-gray-900">{{ number_format($training->price, 2) }} ₽</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Максимум участников</dt>
                                    <dd class="text-sm text-gray-900">{{ $training->max_participants }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Статус</dt>
                                    <dd class="text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $training->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $training->is_active ? 'Активна' : 'Неактивна' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Описание</h3>
                            <div class="text-sm text-gray-900">
                                {{ $training->description ?: 'Описание не указано' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


