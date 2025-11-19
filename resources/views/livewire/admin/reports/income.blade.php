<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Доходы по тренерам</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тренер</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Доход (₽)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($incomeByTrainer as $name => $sum)
                    <tr>
                        <td class="px-6 py-4">{{ $name }}</td>
                        <td class="px-6 py-4">{{ number_format($sum, 2, '.', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">Нет данных</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
