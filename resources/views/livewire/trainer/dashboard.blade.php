<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Панель тренера</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="text-sm text-gray-500">Всего слотов</div>
            <div class="text-2xl font-bold text-green-700">{{ $stats['sessions_total'] }}</div>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="text-sm text-gray-500">Ближайшие слоты</div>
            <div class="text-2xl font-bold text-green-700">{{ $stats['sessions_upcoming'] }}</div>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="text-sm text-gray-500">Подтверждённые бронирования</div>
            <div class="text-2xl font-bold text-green-700">{{ $stats['bookings_confirmed'] }}</div>
        </div>
        <div class="p-6 bg-white rounded-lg shadow">
            <div class="text-sm text-gray-500">Ожидающие бронирования</div>
            <div class="text-2xl font-bold text-green-700">{{ $stats['bookings_pending'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('trainer-panel.schedule') }}" class="p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors block">
            <h3 class="text-lg font-semibold text-green-900 mb-2">Управление расписанием</h3>
            <p class="text-green-700">Создание, редактирование и удаление слотов</p>
        </a>
        <a href="{{ route('trainer-panel.bookings') }}" class="p-6 bg-white border border-green-200 rounded-lg hover:bg-green-50 transition-colors block">
            <h3 class="text-lg font-semibold text-green-900 mb-2">Мои бронирования</h3>
            <p class="text-green-700">Подтверждение и отклонение заявок</p>
        </a>
        <a href="{{ route('trainer-panel.profile') }}" class="p-6 bg-white border border-green-200 rounded-lg hover:bg-green-50 transition-colors block">
            <h3 class="text-lg font-semibold text-green-900 mb-2">Настройки аккаунта</h3>
            <p class="text-green-700">Обновление информации аккаунта</p>
        </a>
    </div>
</div>
