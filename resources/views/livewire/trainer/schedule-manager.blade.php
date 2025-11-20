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

    @php
        $startDate = now()->startOfDay();
        $days = 7;
        $hoursStart = 7;
        $hoursEnd = 22;
        $stepMinutes = 30;

        $grid = [];
        foreach (range(0, $days - 1) as $dayOffset) {
            $date = $startDate->copy()->addDays($dayOffset);
            $grid[$date->toDateString()] = [
                'date' => $date,
                'slots' => [],
            ];
        }

        $sourceSessions = isset($allSessions) ? $allSessions : $sessions;

        foreach ($sourceSessions as $session) {
            $dateKey = $session->date->toDateString();
            if (!isset($grid[$dateKey])) continue;

            $start = \Carbon\Carbon::parse($session->date->format('Y-m-d').' '.\Carbon\Carbon::parse($session->start_time)->format('H:i:s'));
            $end = \Carbon\Carbon::parse($session->date->format('Y-m-d').' '.\Carbon\Carbon::parse($session->end_time)->format('H:i:s'));

            $grid[$dateKey]['slots'][] = [
                'start' => $start,
                'end' => $end,
                'session' => $session,
            ];
        }
    @endphp

    <div class="bg-white rounded-lg shadow-md mb-6 p-4" style="padding-left: 0;">
        <div class="flex">
            <div class="flex-shrink-0" style="width: 120px;">
                <div class="border-b border-gray-200 bg-gray-50" style="height: 40px;"></div>
                @foreach($grid as $day)
                    <div class="border-t border-gray-200 text-gray-700 px-3 flex items-center" style="font-size: 14px; background-color: #f9fafb; height: 60px;">
                        <div>
                            <div class="font-semibold" style="font-size: 15px;">{{ $day['date']->format('d.m') }}</div>
                            <div class="text-gray-500" style="font-size: 12px;">{{ $day['date']->clone()->locale('ru')->isoFormat('dd') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex-1 overflow-x-auto">
                <div class="min-w-max">
                    @php
                        $totalSteps = ($hoursEnd - $hoursStart) * 60 / $stepMinutes + 1;
                    @endphp
                    <div class="grid" style="grid-template-columns: repeat({{ $totalSteps }}, minmax(70px, 1fr)); column-gap: 6px;">
                        @for($m = $hoursStart * 60; $m <= $hoursEnd * 60; $m += $stepMinutes)
                            @php
                                $hourLabel = intdiv($m, 60);
                                $minuteLabel = $m % 60;
                            @endphp
                            <div class="border-b border-gray-200 bg-gray-50 text-center text-[11px] font-semibold text-gray-700" style="height: 40px; display: flex; align-items: center; justify-content: center;">
                                @if($minuteLabel === 0)
                                    {{ sprintf('%02d:00', $hourLabel) }}
                                @else
                                    {{ sprintf('%02d:%02d', $hourLabel, $minuteLabel) }}
                                @endif
                            </div>
                        @endfor

                        @foreach($grid as $day)
                            @php
                                $dayStartMinutes = $hoursStart * 60;
                                $dayEndMinutes = $hoursEnd * 60;
                            @endphp

                            <div class="border-t border-gray-200 relative bg-white" style="grid-column: 1 / span {{ $totalSteps }}; height: 60px;">
                                @foreach($day['slots'] as $slot)
                                    @php
                                        $slotStartMinutesRaw = $slot['start']->hour * 60 + $slot['start']->minute;
                                        $slotEndMinutesRaw = $slot['end']->hour * 60 + $slot['end']->minute;

                                        $slotStartMinutes = max($dayStartMinutes, $slotStartMinutesRaw);
                                        $slotEndMinutes = min($dayEndMinutes, $slotEndMinutesRaw);
                                        if ($slotEndMinutes <= $slotStartMinutes) continue;

                                        $startStep = max(0, intdiv($slotStartMinutes - $dayStartMinutes, $stepMinutes));
                                        $lengthMinutes = $slotEndMinutes - $slotStartMinutes;
                                        $spanSteps = max(1, (int)ceil($lengthMinutes / $stepMinutes));

                                        $leftPercent = ($startStep / $totalSteps) * 100;
                                        $widthPercent = ($spanSteps / $totalSteps) * 100;

                                        $cellSession = $slot['session'];
                                        $isBooked = $cellSession->status === 'booked';

                                        $isPast = $slot['end']->lte(\Carbon\Carbon::now());

                                        if ($isPast) {
                                            $bg = 'bg-gray-400';
                                            $label = 'Завершено';
                                        } else {
                                            $bg = $isBooked ? 'bg-blue-500' : 'bg-green-500';
                                            $label = $isBooked ? 'Занято' : 'Свободно';
                                        }
                                    @endphp
                                    <button type="button"
                                            wire:click="showDetails({{ $cellSession->id }})"
                                            class="absolute {{ $bg }} bg-opacity-80 text-white leading-tight flex items-center justify-center text-center hover:bg-opacity-100 focus:outline-none overflow-hidden"
                                            style="left: calc({{ $leftPercent }}% + 3px); width: calc({{ $widthPercent }}% - 6px); top: 2px; bottom: 2px; font-size: 10px; padding: 0 4px; border: 1px solid rgba(255,255,255,0.7); box-sizing: border-box; border-radius: 8px;">
                                        <span class="whitespace-nowrap" style="transform: rotate(-12deg); display: inline-block; color: #ffffff;">{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showDetailsModal && $detailsSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Информация о слоте</h3>
                    <button type="button" wire:click="closeDetails" class="text-gray-400 hover:text-gray-600 text-xl leading-none">×</button>
                </div>

                <div class="space-y-2 text-sm text-gray-800">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Дата</span>
                        <span>{{ $detailsSession->date->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Время</span>
                        <span>{{ \Carbon\Carbon::parse($detailsSession->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($detailsSession->end_time)->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Статус</span>
                        <span>
                            @switch($detailsSession->status)
                                @case('available') Доступно @break
                                @case('booked') Забронировано @break
                                @default Отменено
                            @endswitch
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Цена</span>
                        <span>{{ number_format($detailsSession->price, 2, '.', ' ') }} ₽</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="font-medium text-gray-600 mr-4">Локация</span>
                        <span class="flex-1 text-right text-gray-800 break-words">{{ $detailsSession->location }}</span>
                    </div>
                    <div class="flex justify-between items-start">
                        <span class="font-medium text-gray-600 mr-4">Клиент</span>
                        <span class="flex-1 text-right">
                            @if($detailsSession->booking && $detailsSession->booking->user)
                                {{ $detailsSession->booking->user->name }}
                            @else
                                Нет бронирования
                            @endif
                        </span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    @if($detailsSession->status === 'available')
                        <button type="button"
                                wire:click="openForm({{ $detailsSession->id }})"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                            Редактировать слот
                        </button>
                        <button type="button"
                                wire:click="delete({{ $detailsSession->id }})"
                                wire:confirm="Удалить слот?"
                                class="px-4 py-2 bg-red-600 rounded-md hover:bg-red-700 focus:ring-2 focus:ring-red-500"
                                style="color:#ffffff;">
                            Удалить слот
                        </button>
                    @endif

                    <button type="button" wire:click="closeDetails" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    {{ $editingSession ? 'Редактирование слота' : 'Новый слот' }}
                </h2>
                
                <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Дата</label>
                        <input type="date" wire:model.live="date" wire:change="handleDateChange" min="{{ now()->format('Y-m-d') }}" max="{{ now()->addDays(6)->format('Y-m-d') }}"
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Цена</label>
                        <input type="number" wire:model="price" step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               required>
                        @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Локация</label>
                        <select wire:model="location"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                required>
                            <option value="">Выберите локацию</option>
                            @forelse($availableLocations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
                            @empty
                                <option value="" disabled>Сначала добавьте локации в профиле тренера</option>
                            @endforelse
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Список локаций настраивается в профиле тренера.</p>
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
        </div>
    @endif

    @php
        $grouped = $sessions->groupBy(function($s) { return $s->date->format('Y-m-d'); });
    @endphp

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($grouped as $dateKey => $items)
                @php
                    $dateObj = \Carbon\Carbon::parse($dateKey);
                    $isExpanded = in_array($dateKey, $expandedDates ?? [], true);
                @endphp
                <div>
                    <button type="button"
                            wire:click="toggleDateSection('{{ $dateKey }}')"
                            class="w-full flex items-center justify-between px-6 py-3 bg-gray-50 hover:bg-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-md bg-gray-200 flex flex-col items-center justify-center text-xs font-semibold text-gray-700">
                                <span>{{ $dateObj->format('d') }}</span>
                                <span>{{ $dateObj->format('m') }}</span>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-gray-900">{{ $dateObj->format('d.m.Y') }}</div>
                                <div class="text-xs text-gray-500">Всего слотов: {{ $items->count() }}</div>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $isExpanded ? 'Свернуть' : 'Развернуть' }}
                        </span>
                    </button>

                    @if($isExpanded)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Время</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Локация</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($items as $session)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ Str::limit($session->location, 40) }}
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
                                                                class="px-3 py-1.5 rounded-md bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-red-500"
                                                                style="color:#ffffff;">
                                                            Удалить
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-6 py-10 text-center text-gray-500">
                    Слотов пока нет. Создайте первый слот!
                </div>
            @endforelse
        </div>
    </div>
    
    <div class="mt-4"></div>
</div>
