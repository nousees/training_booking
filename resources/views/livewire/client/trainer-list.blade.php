<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Найти тренера</h1>
        <p class="text-sm text-gray-500 mb-4">Подберите тренера по цене, специализации и рейтингу</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                       placeholder="Имя или описание...">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Специализация</label>
                <select wire:model.live="specialization" 
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">Все</option>
                    @foreach($allSpecializations as $spec)
                        <option value="{{ $spec }}">{{ ucfirst($spec) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Мин. цена</label>
                <input type="number" wire:model.live.debounce.300ms="minPrice" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                       placeholder="0">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Макс. цена</label>
                <input type="number" wire:model.live.debounce.300ms="maxPrice" 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                       placeholder="100000">
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Мин. рейтинг</label>
            <input type="number" wire:model.live.debounce.300ms="minRating" min="1" max="5" step="0.1"
                   class="w-32 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                   placeholder="0">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($trainers as $profile)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                @php
                    $normalize = function ($path) {
                        if (!$path) return null;

                        if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
                            return $path;
                        }

                        $clean = str_replace('\\\\', '/', $path);
                        $clean = preg_replace('/^(public|storage)\//','', $clean);

                        return url('/storage/' . ltrim($clean, '/'));
                    };

                    $img = $profile->images[0] ?? null;
                    $url = $normalize($img);
                    if (!$url && ($profile->user->avatar ?? null)) {
                        $url = $normalize($profile->user->avatar);
                    }
                @endphp
                @if($url)
                    <img src="{{ $url }}" alt="{{ $profile->user->name }}" 
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">Нет фото</span>
                    </div>
                @endif
                
                <div class="p-4">
                    <h3 class="text-xl font-semibold text-green-900 mb-2">
                        <a href="{{ route('trainer.show', $profile->user) }}" 
                           class="hover:text-green-600">
                            {{ $profile->user->name }}
                        </a>
                    </h3>
                    
                    <div class="flex items-center mb-2">
                        <span class="text-yellow-400">★</span>
                        <span class="ml-1 text-gray-700">{{ number_format($profile->rating, 1) }}</span>
                        <span class="ml-4 text-gray-500">Опыт: {{ $profile->experience_years }} лет</span>
                    </div>
                    
                    @if($profile->bio)
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $profile->bio }}</p>
                    @endif
                    
                    <div class="flex flex-wrap gap-1 mb-3">
                        @foreach(array_slice($profile->specializations ?? [], 0, 3) as $spec)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                {{ ucfirst($spec) }}
                            </span>
                        @endforeach
                    </div>
                    
                    <div class="flex items-center justify-end">
                        <a href="{{ route('trainer.show', $profile->user) }}" 
                           class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Смотреть профиль
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">Тренеры не найдены.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $trainers->links() }}
    </div>
</div>

