<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Профиль тренера</h1>

    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Основные данные</h2>

                <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                <input type="text" wire:model="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" value="{{ $email }}" disabled class="w-full rounded-md bg-gray-100 border-gray-200"/>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                    <input type="text" wire:model="phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    @error('phone') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Аватар</label>
                    @if($avatar)
                        <img src="{{ Storage::disk('public')->url($avatar) }}" class="h-16 w-16 rounded-full mb-2"/>
                    @endif
                    <input type="file" wire:model="avatar_upload" accept="image/*"/>
                    @error('avatar_upload') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Параметры тренера</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Биография</label>
                    <textarea wire:model="bio" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                    @error('bio') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Специализации (через запятую)</label>
                    <input type="text" wire:model.lazy="specializations" 
                           x-data 
                           x-on:change="$wire.specializations = $el.value.split(',').map(s=>s.trim()).filter(Boolean)"
                           value="{{ implode(', ', (array)$specializations) }}"
                           class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    @error('specializations') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Опыт (лет)</label>
                        <input type="number" wire:model="experience_years" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                        @error('experience_years') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Локации (до 3 вариантов)</label>
                    <div class="space-y-2">
                        <input type="text" wire:model="locations.0" placeholder="Например: г. Москва, ул. Спортивная, д. 1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                        <input type="text" wire:model="locations.1" placeholder="Дополнительная локация" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                        <input type="text" wire:model="locations.2" placeholder="Дополнительная локация" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                    @error('locations.*') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4 space-y-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model.live="supports_online" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Провожу онлайн-тренировки</span>
                    </label>
                    @if($supports_online)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ссылка для онлайн-тренировок</label>
                            <input type="text" wire:model="online_link" placeholder="Ссылка на Zoom или другую платформу" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"/>
                            @error('online_link') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                    @endif
                </div>
                

                
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">Сохранить</button>
            <button type="button" wire:click="openPasswordModal" class="text-sm text-gray-700 hover:underline">Поменять пароль</button>
        </div>
    </form>

    @if($showPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Смена пароля</h3>
                    <button type="button" wire:click="closePasswordModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">×</button>
                </div>
                <div class="space-y-4">
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
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="closePasswordModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Отмена</button>
                    <button type="button" wire:click="changePassword" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">Обновить пароль</button>
                </div>
            </div>
        </div>
    @endif
</div>
