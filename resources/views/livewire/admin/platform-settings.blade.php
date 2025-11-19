<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Настройки платформы</h1>

    @if(session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form wire:submit.prevent="save">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Комиссия платформы, %
                    </label>
                    <input type="number" wire:model="platformCommissionPercent" step="0.01" min="0" max="100"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('platformCommissionPercent') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Окно отмены (часы)
                    </label>
                    <input type="number" wire:model="cancellationWindowHours" min="1"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('cancellationWindowHours') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Валюта (3 буквы)
                    </label>
                    <input type="text" wire:model="currency" maxlength="3"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('currency') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="maintenanceMode"
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <span class="ml-2 text-sm text-gray-700">Режим обслуживания</span>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                    Сохранить
                </button>
            </div>
        </form>
    </div>
</div>

