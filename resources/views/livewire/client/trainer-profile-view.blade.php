<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @if($profile)
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <div class="md:flex">
                <div class="md:w-1/3">
                    @php
                        $images = is_array($profile->images ?? null) ? $profile->images : [];
                        $first = $images[0] ?? null;
                        $url = null;
                        $normalize = function($path) {
                            if (!$path) return null;
                            if (\Illuminate\Support\Str::startsWith($path, ['http://','https://'])) {
                                return $path;
                            }
                            $clean = str_replace('\\\\', '/', $path);
                            $clean = preg_replace('/^(public|storage)\//','', $clean);
                            return url('/storage/' . ltrim($clean, '/'));
                        };
                        if ($first) {
                            $url = $normalize($first);
                        }
                        if (!$url && !empty($trainer->avatar)) {
                            $url = $normalize($trainer->avatar);
                        }
                    @endphp
                    @if($url)
                        <img src="{{ $url }}" alt="{{ $trainer->name }}" class="w-full h-64 md:h-full object-cover">
                    @endif
                </div>
                
                <div class="p-6 md:w-2/3">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $trainer->name }}</h1>
                    
                    <div class="flex items-center mb-4">
                        <span class="text-yellow-400 text-2xl">★</span>
                        <span class="ml-2 text-xl font-semibold text-gray-700">
                            {{ number_format($profile->rating, 1) }}
                        </span>
                        <span class="ml-4 text-gray-600">Опыт: {{ $profile->experience_years }} лет</span>
                    </div>
                    
                    @if($profile->bio)
                        <p class="text-gray-700 mb-4">{{ $profile->bio }}</p>
                    @endif
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($profile->specializations ?? [] as $spec)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                {{ ucfirst($spec) }}
                            </span>
                        @endforeach
                    </div>
                    
                    

                    @if(!empty($images))
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach($images as $img)
                                @php 
                                    $clean = str_replace('\\\\', '/', $img);
                                    $clean = preg_replace('/^(public|storage)\//','', $clean);
                                    $imgUrl = \Illuminate\Support\Str::startsWith($img, ['http://','https://'])
                                        ? $img
                                        : url('/storage/' . ltrim($clean, '/'));
                                @endphp
                                <img src="{{ $imgUrl }}" class="h-20 w-20 object-cover rounded" alt="Фото" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Доступные слоты (7 дней)</h2>
        
        @if($sessions->isEmpty())
            <p class="text-gray-500">Нет доступных слотов.</p>
        @else
            <div class="space-y-4">
                @foreach($sessions as $date => $dateSessions)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">
                            {{ \Carbon\Carbon::parse($date)->locale('ru')->translatedFormat('d.m.Y, l') }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($dateSessions as $session)
                                <div class="border rounded-lg p-4 hover:border-green-500 transition-colors 
                                    {{ $selectedSession == $session->id ? 'border-green-500 bg-green-50' : '' }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <div class="font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">{{ $session->location }}</div>
                                        </div>
                                        <div class="text-lg font-bold text-gray-900">
                                            {{ number_format($session->price, 2, '.', ' ') }} ₽
                                        </div>
                                    </div>
                                    
                                    @auth
                                        @if(auth()->user()->isClient())
                                            <button wire:click="selectSession({{ $session->id }})" 
                                                    class="w-full mt-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                                                Забронировать
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" 
                                           class="block w-full mt-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-center">
                                            Войти, чтобы забронировать
                                        </a>
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if($selectedSession && auth()->check() && auth()->user()->isClient())
        <livewire:client.booking-form :session-id="$selectedSession" />
    @endif
</div>

