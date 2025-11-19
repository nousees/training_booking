<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Модерация отзывов</h1>

    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4 flex items-center space-x-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Фильтр по тренеру</label>
            <select wire:model="trainerId" class="rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Все тренеры</option>
                @foreach($trainers as $trainerProfile)
                    <option value="{{ $trainerProfile->user_id }}">{{ $trainerProfile->user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Пользователь</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тренер</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Оценка</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Комментарий</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reviews as $review)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $review->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $review->trainer->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-yellow-400">★</span>
                                <span class="ml-1 text-gray-700">{{ $review->rating }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($review->comment, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $review->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            <button wire:click="delete({{ $review->id }})" 
                                    wire:confirm="Удалить этот отзыв?"
                                    class="px-3 py-1.5 rounded-md border border-red-600 text-red-600 hover:bg-red-50">
                                Удалить
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Отзывов пока нет.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
</div>

