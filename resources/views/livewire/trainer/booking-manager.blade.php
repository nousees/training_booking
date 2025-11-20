<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Бронирования</h1>
    <p class="text-sm text-gray-500 mb-4">Управляйте запросами клиентов на тренировки</p>

    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4 bg-white rounded-lg shadow p-4 space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Статус</label>
                <select wire:model.live="statusFilter" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                    <option value="">Все статусы</option>
                    <option value="pending">Ожидает</option>
                    <option value="confirmed">Подтверждено</option>
                    <option value="canceled">Отменено</option>
                    <option value="completed">Завершено</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Клиент</label>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Имя или email" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"/>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Дата от</label>
                <input type="date" wire:model.live="dateFrom" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"/>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Дата до</label>
                <input type="date" wire:model.live="dateTo" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"/>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs text-gray-600">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model.live="futureOnly" 
                       class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span>Показывать только будущие тренировки</span>
            </label>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата и время</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Локация</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $booking->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $booking->session->date->format('d.m.Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($booking->session->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($booking->session->end_time)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ Str::limit($booking->session->location, 30) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($booking->session->price, 2, '.', ' ') }} ₽
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'canceled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @switch($booking->status)
                                    @case('pending') Ожидает @break
                                    @case('confirmed') Подтверждено @break
                                    @case('canceled') Отменено @break
                                    @case('completed') Завершено @break
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            @if($booking->isPending())
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="confirm({{ $booking->id }})" 
                                            class="px-3 py-1.5 rounded-md border border-green-600 text-green-600 hover:bg-green-50">
                                        Подтвердить
                                    </button>
                                    <button wire:click="reject({{ $booking->id }})" 
                                            class="px-3 py-1.5 rounded-md border border-red-600 text-red-600 hover:bg-red-50">
                                        Отклонить
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Бронирований пока нет.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>

