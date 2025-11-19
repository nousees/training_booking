<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Статистика тренера</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Всего слотов</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $totalSessions }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Подтверждённых тренировок</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $confirmedTrainings }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Завершённых тренировок</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $completedTrainings }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Доход (₽)</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($income, 2, '.', ' ') }}</div>
        </div>
    </div>
</div>
