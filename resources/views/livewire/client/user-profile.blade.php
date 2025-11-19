<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Личный кабинет</h1>
    <p class="text-sm text-gray-500 mb-6">Управляйте профилем и вашими тренировками</p>

    @if(session()->has('profile_saved'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">{{ session('profile_saved') }}</div>
    @endif
    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">{{ session('message') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-1">
            <h2 class="text-xl font-semibold mb-4">Профиль</h2>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                <input type="text" wire:model="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ $email }}" disabled class="w-full rounded-md bg-gray-100 border-gray-200"/>
            </div>

            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                <input type="text" wire:model="phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                @error('phone') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            

            

            <div class="mb-4 space-y-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" wire:model="notify_email" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="text-sm text-gray-700">Получать уведомления на email</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" wire:model="notify_in_app" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="text-sm text-gray-700">Показывать уведомления в приложении</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button wire:click="saveProfile" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">Сохранить</button>
                <a href="{{ route('notifications') }}" class="text-sm text-green-700 hover:underline">Уведомления</a>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-lg font-semibold mb-4">Смена пароля</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Текущий пароль</label>
                        <input type="password" wire:model="current_password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                        @error('current_password') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Новый пароль</label>
                        <input type="password" wire:model="password" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                        @error('password') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Подтверждение</label>
                        <input type="password" wire:model="password_confirmation" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                </div>
                <div class="mt-4">
                    <button wire:click="changePassword" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">Обновить пароль</button>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Предстоящие тренировки</div>
                <div class="divide-y">
                    @forelse($upcoming as $booking)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-900">{{ $booking->session->date->format('d.m.Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->session->end_time)->format('H:i') }}</div>
                                <div class="text-sm text-gray-700 mt-1">Тренер: {{ $booking->session->trainer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->session->location }}</div>
                                <div class="mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        @switch($booking->status)
                                            @case('pending') Ожидает @break
                                            @case('confirmed') Подтверждено @break
                                            @default {{ ucfirst($booking->status) }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                            <div>
                                @if($booking->status === 'pending')
                                    <button wire:click="cancel({{ $booking->id }})" class="px-3 py-1.5 rounded-md border border-red-600 text-red-600 hover:bg-red-50">Отменить</button>
                                @elseif($booking->status === 'confirmed' && $booking->canBeCanceled())
                                    <button wire:click="cancel({{ $booking->id }})" class="px-3 py-1.5 rounded-md border border-red-600 text-red-600 hover:bg-red-50">Отменить</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-gray-500">Нет предстоящих тренировок</div>
                    @endforelse
                </div>
                <div class="px-6 py-3">{{ $upcoming->links() }}</div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Завершённые тренировки</div>
                <div class="divide-y">
                    @forelse($completed as $booking)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-900">{{ $booking->session->date->format('d.m.Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->session->end_time)->format('H:i') }}</div>
                                <div class="text-sm text-gray-700 mt-1">Тренер: {{ $booking->session->trainer->name }}</div>
                            </div>
                            @if(!$booking->review)
                                <a href="{{ route('reviews.create', ['booking' => $booking->id]) }}" class="text-green-600 hover:text-green-800">Оставить отзыв</a>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-gray-500">Нет завершённых тренировок</div>
                    @endforelse
                </div>
                <div class="px-6 py-3">{{ $completed->links() }}</div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Отменённые</div>
                <div class="divide-y">
                    @forelse($canceled as $booking)
                        <div class="p-4">
                            <div class="text-sm text-gray-900">{{ $booking->session->date->format('d.m.Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->session->end_time)->format('H:i') }}</div>
                            <div class="text-sm text-gray-700 mt-1">Тренер: {{ $booking->session->trainer->name }}</div>
                        </div>
                    @empty
                        <div class="p-6 text-gray-500">Нет отменённых тренировок</div>
                    @endforelse
                </div>
                <div class="px-6 py-3">{{ $canceled->links() }}</div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 font-semibold">Мои отзывы</div>
                <div class="divide-y">
                    @forelse($myReviews as $review)
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-900">Тренер: {{ $review->trainer?->name }}</div>
                                <div class="text-sm text-yellow-600 font-semibold">★ {{ $review->rating }}</div>
                            </div>
                            @if($review->comment)
                                <div class="text-sm text-gray-700 mt-1">{{ $review->comment }}</div>
                            @endif
                            <div class="text-xs text-gray-400 mt-1">{{ $review->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                    @empty
                        <div class="p-6 text-gray-500">Вы ещё не оставляли отзывов</div>
                    @endforelse
                </div>
                <div class="px-6 py-3">{{ $myReviews->links() }}</div>
            </div>
        </div>
    </div>
</div>

