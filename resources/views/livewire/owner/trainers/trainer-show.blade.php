<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Информация о тренере
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">{{ $trainer->name }}</h1>
                        <div class="space-x-3">
                            <a href="{{ route('owner.trainers.edit', $trainer) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Редактировать
                            </a>
                            <a href="{{ route('owner.trainers.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Назад к списку
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Основная информация</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Имя</dt>
                                    <dd class="text-sm text-gray-900">{{ $trainer->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Специализация</dt>
                                    <dd class="text-sm text-gray-900">{{ $trainer->specialization }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Опыт работы</dt>
                                    <dd class="text-sm text-gray-900">{{ $trainer->experience_years }} лет</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Спортзал</dt>
                                    <dd class="text-sm text-gray-900">{{ $trainer->gym->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Статус</dt>
                                    <dd class="text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $trainer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $trainer->is_active ? 'Активен' : 'Неактивен' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Дополнительная информация</h3>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Биография</dt>
                                <dd class="text-sm text-gray-900 mt-1">{{ $trainer->bio ?: 'Не указано' }}</dd>
                            </div>
                            
                            @if($trainer->photo_path)
                            <div class="mt-4">
                                <dt class="text-sm font-medium text-gray-500">Фото</dt>
                                <dd class="text-sm text-gray-900 mt-1">
                                    <img src="{{ $trainer->photo_path }}" alt="Фото тренера" class="w-32 h-32 object-cover rounded-lg">
                                </dd>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


