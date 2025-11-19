<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Моё расписание</h1>
            <p class="text-sm text-gray-500 mt-1">Управляйте доступными слотами для клиентов</p>
        </div>
        <div class="flex-shrink-0">
            <button wire:click="openForm" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                Добавить слот
            </button>
        </div>
    </div>

    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    @if($showForm)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ $editingSession ? 'Редактирование слота' : 'Новый слот' }}
            </h2>
            
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Дата</label>
                        <input type="date" wire:model.live="date" wire:change="handleDateChange" min="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               required>
                        @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Шаг времени</label>
                        <select wire:model.live="timeStep"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                            <option value="30">30 минут</option>
                            <option value="60">60 минут</option>
                        </select>
                        @error('timeStep') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Начало</label>
                        <select wire:model.live="startTime"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                            <option value="">Выберите время</option>
                            @forelse($freeStartOptions as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @empty
                                <option value="" disabled>Нет свободного времени</option>
                            @endforelse
                        </select>
                        @error('startTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Поле конца убрано: конец рассчитывается автоматически на основе начала и шага -->
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Цена</label>
                        <input type="number" wire:model="price" step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               required>
                        @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Локация</label>
                        <textarea wire:model="location" rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Адрес или ссылка на Zoom" required></textarea>
                        @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-4">
                    <button type="button" wire:click="closeForm" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Отмена
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Время</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Локация</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($sessions as $session)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $session->date->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($session->location, 30) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($session->price, 2, '.', ' ') }} ₽
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($session->status === 'available') bg-green-100 text-green-800
                                @elseif($session->status === 'booked') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                @switch($session->status)
                                    @case('available') Доступно @break
                                    @case('booked') Забронировано @break
                                    @default Отменено
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            @if($session->status === 'available')
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="openForm({{ $session->id }})" 
                                            class="px-3 py-1.5 rounded-md border border-green-600 text-green-600 hover:bg-green-50">
                                        Редактировать
                                    </button>
                                    <button wire:click="delete({{ $session->id }})" 
                                            wire:confirm="Удалить слот?"
                                            class="px-3 py-1.5 rounded-md border border-red-600 text-red-600 hover:bg-red-50">
                                        Удалить
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            Слотов пока нет. Создайте первый слот!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
</div>

