<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Пользователи</h1>

    <div class="mb-6 flex flex-col sm:flex-row gap-3 sm:gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" 
               placeholder="Поиск по имени или email"
               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">

        <select wire:model.live="roleFilter" 
                class="rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            <option value="">Все роли</option>
            <option value="client">Клиент</option>
            <option value="trainer">Тренер</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Имя</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Роль</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Создан</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $disabled = ($user->role === 'owner') || (auth()->id() === $user->id); @endphp
                            <select @disabled($disabled)
                                    wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                    class="text-sm rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:bg-gray-100 disabled:text-gray-500">
                                <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Клиент</option>
                                <option value="trainer" {{ $user->role === 'trainer' ? 'selected' : '' }}>Тренер</option>
                            </select>
                            @if($disabled)
                                <div class="text-xs text-gray-500 mt-1">Роль нельзя изменить</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->isBlocked())
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Заблокирован</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Активен</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                            @php $blockDisabled = ($user->role === 'owner') || (auth()->id() === $user->id); @endphp
                            <button @disabled($blockDisabled)
                                    wire:click="toggleBlock({{ $user->id }})"
                                    class="px-3 py-1.5 rounded-md border border-red-600 text-red-600 hover:bg-red-50 disabled:text-gray-400">
                                {{ $user->isBlocked() ? 'Разблокировать' : 'Блокировать' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Пользователи не найдены
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

