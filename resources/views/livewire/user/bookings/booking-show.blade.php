<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Информация о бронировании
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Бронирование #{{ $booking->id }}</h1>
                        <div class="space-x-3">
                            @if($booking->status === 'pending')
                            <a href="{{ route('user.bookings.edit', $booking) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Редактировать
                            </a>
                            @endif
                            <a href="{{ route('user.bookings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Назад к списку
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Информация о тренировке</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Название</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->training->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Тренер</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->training->trainer->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Спортзал</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->training->gym->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Цена</dt>
                                    <dd class="text-sm text-gray-900 text-green-600 font-semibold">{{ number_format($booking->training->price, 2) }} ₽</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Длительность</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->training->duration_minutes }} минут</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Детали бронирования</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Время начала</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->start_time->format('d.m.Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Время окончания</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->end_time->format('d.m.Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Статус</dt>
                                    <dd class="text-sm">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'completed' => 'bg-blue-100 text-blue-800'
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Ожидает',
                                                'confirmed' => 'Подтверждено',
                                                'cancelled' => 'Отменено',
                                                'completed' => 'Завершено'
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$booking->status] }}">
                                            {{ $statusLabels[$booking->status] }}
                                        </span>
                                    </dd>
                                </div>
                                @if($booking->notes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Заметки</dt>
                                    <dd class="text-sm text-gray-900">{{ $booking->notes }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

