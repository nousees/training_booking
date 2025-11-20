<div>
    @if($this->visible)
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

            foreach ($sessions as $session) {
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

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-2xl max-w-5xl w-full mx-4 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Моё расписание на ближайшую неделю</h3>
                    <button type="button" wire:click="close" class="text-gray-400 hover:text-gray-600 text-xl leading-none">×</button>
                </div>

                <div class="bg-white rounded-lg shadow-md mb-2 p-4" style="padding-left: 0;">
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
                                                {{ sprintf('%02d:%02d', $hourLabel) }}
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
                                                    $bg = 'bg-blue-500';
                                                    $label = $cellSession->trainer ? $cellSession->trainer->name : 'Тренировка';
                                                @endphp
                                                <button type="button"
                                                        wire:click="showDetails({{ $cellSession->id }})"
                                                        class="absolute {{ $bg }} bg-opacity-80 text-white leading-tight flex items-center justify-center text-center overflow-hidden hover:bg-opacity-100 focus:outline-none"
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

                @if($detailsSession)
                    <div class="mt-4 border-t pt-4">
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
                                <span class="font-medium text-gray-600">Тренер</span>
                                <span>{{ $detailsSession->trainer?->name }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="font-medium text-gray-600 mr-4">Локация</span>
                                <span class="flex-1 text-right text-gray-800 break-words">{{ $detailsSession->location }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-600">Цена</span>
                                <span>{{ number_format($detailsSession->price, 2, '.', ' ') }} ₽</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <button type="button" wire:click="close" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Закрыть</button>
                </div>
            </div>
        </div>
    @endif
</div>
