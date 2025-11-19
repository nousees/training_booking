<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Оставить отзыв</h1>

    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4 max-w-xl">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Оценка</label>
            <select wire:model="rating" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
            @error('rating') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Комментарий</label>
            <textarea wire:model="comment" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            @error('comment') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Сохранить</button>
    </form>
</div>
