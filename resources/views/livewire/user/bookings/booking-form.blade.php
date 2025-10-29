<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $bookingId ? 'Редактировать бронирование' : 'Новое бронирование' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="gym_id" class="block text-sm font-medium text-gray-700">Спортзал *</label>
                                <select wire:model="gym_id" id="gym_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Выберите спортзал</option>
                                    @foreach($gyms as $gym)
                                        <option value="{{ $gym->id }}">{{ $gym->name }} - {{ $gym->address }}</option>
                                    @endforeach
                                </select>
                                @error('gym_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="trainer_id" class="block text-sm font-medium text-gray-700">Тренер *</label>
                                <select wire:model="trainer_id" id="trainer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" {{ !$gym_id ? 'disabled' : '' }}>
                                    <option value="">Выберите тренера</option>
                                    @foreach($trainers as $trainer)
                                        <option value="{{ $trainer->id }}">{{ $trainer->name }} - {{ $trainer->specialization }}</option>
                                    @endforeach
                                </select>
                                @error('trainer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="training_id" class="block text-sm font-medium text-gray-700">Тренировка *</label>
                                <select wire:model="training_id" id="training_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" {{ !$gym_id || !$trainer_id ? 'disabled' : '' }}>
                                    <option value="">Выберите тренировку</option>
                                    @foreach($trainings as $training)
                                        <option value="{{ $training->id }}">{{ $training->name }} - {{ number_format($training->price, 2) }} ₽ ({{ $training->duration_minutes }} мин)</option>
                                    @endforeach
                                </select>
                                @error('training_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Время начала *</label>
                                <input type="datetime-local" wire:model="start_time" id="start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700">Время окончания *</label>
                                <input type="datetime-local" wire:model="end_time" id="end_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                        </div>

                        @if($training_id && $trainings->where('id', $training_id)->first())
                            @php
                                $selectedTraining = $trainings->where('id', $training_id)->first();
                            @endphp
                            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                                <h3 class="text-lg font-semibold text-blue-900 mb-2">Информация о тренировке</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium">Название:</span> {{ $selectedTraining->name }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Цена:</span> {{ number_format($selectedTraining->price, 2) }} ₽
                                    </div>
                                    <div>
                                        <span class="font-medium">Длительность:</span> {{ $selectedTraining->duration_minutes }} минут
                                    </div>
                                    <div>
                                        <span class="font-medium">Максимум участников:</span> {{ $selectedTraining->max_participants }}
                                    </div>
                                    @if($selectedTraining->description)
                                    <div class="md:col-span-2">
                                        <span class="font-medium">Описание:</span> {{ $selectedTraining->description }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Заметки</label>
                            <textarea wire:model="notes" id="notes" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Дополнительная информация о бронировании..."></textarea>
                            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('user.bookings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Отмена
                            </a>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                {{ $bookingId ? 'Обновить' : 'Забронировать' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
