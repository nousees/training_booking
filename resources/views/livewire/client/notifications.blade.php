<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Уведомления</h1>

    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">{{ session('message') }}</div>
    @endif

    <div class="mb-4">
        <select wire:model.live="show" class="rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
            <option value="all">Все</option>
            <option value="unread">Непрочитанные</option>
        </select>
    </div>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse($items as $n)
            <div class="p-4 flex items-start justify-between hover:bg-gray-50">
                <div>
                    <div class="font-semibold {{ $n->isRead() ? 'text-gray-700' : 'text-green-700' }}">{{ $n->title }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ $n->message }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $n->created_at->format('d.m.Y H:i') }}</div>
                </div>
                @if(!$n->isRead())
                    <button wire:click="markRead({{ $n->id }})" class="text-sm px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">Отметить прочитанным</button>
                @endif
            </div>
        @empty
            <div class="p-6 text-gray-500">Нет уведомлений</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
</div>
