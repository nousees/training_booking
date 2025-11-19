<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Тренеры</h1>

    <div class="mb-4">
        <input type="text" wire:model.live="search" placeholder="Поиск по имени" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Имя</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($trainers as $t)
                    <tr>
                        <td class="px-6 py-4">{{ $t->name }}</td>
                        <td class="px-6 py-4">{{ $t->email }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">Нет тренеров</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $trainers->links() }}</div>
</div>
