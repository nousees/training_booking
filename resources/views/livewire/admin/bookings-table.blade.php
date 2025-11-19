<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Все бронирования</h1>

    <div class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
        <div>
            <label class="block text-sm text-gray-700 mb-1">Статус</label>
            <select wire:model.live="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Все</option>
                <option value="pending">Ожидает</option>
                <option value="confirmed">Подтверждено</option>
                <option value="canceled">Отменено</option>
                <option value="completed">Завершено</option>
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-700 mb-1">С даты</label>
            <input type="date" wire:model.live="date_from" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>
        <div>
            <label class="block text-sm text-gray-700 mb-1">По дату</label>
            <input type="date" wire:model.live="date_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>
        <div>
            <label class="block text-sm text-gray-700 mb-1">Тренер</label>
            <select wire:model.live="trainer_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Все</option>
                @foreach($trainers as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-700 mb-1">Клиент</label>
            <select wire:model.live="client_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Все</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тренер</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($bookings as $b)
                    <tr>
                        <td class="px-6 py-4">{{ $b->user->name }}</td>
                        <td class="px-6 py-4">{{ $b->session->trainer->name }}</td>
                        <td class="px-6 py-4">{{ $b->session->date->format('d.m.Y') }}</td>
                        <td class="px-6 py-4">
                            @switch($b->status)
                                @case('pending') Ожидает @break
                                @case('confirmed') Подтверждено @break
                                @case('canceled') Отменено @break
                                @case('completed') Завершено @break
                            @endswitch
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
