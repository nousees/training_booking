<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
     x-data="{ open: @entangle('sessionId') }"
     x-show="open"
     @click.away="open = false">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Подтверждение бронирования</h3>
            
            @if($this->session)
                <div class="mb-4">
                    <p class="text-gray-700"><strong>Дата:</strong> {{ $this->session->date->format('d.m.Y') }}</p>
                    <p class="text-gray-700"><strong>Время:</strong> 
                        {{ \Carbon\Carbon::parse($this->session->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($this->session->end_time)->format('H:i') }}
                    </p>
                    <p class="text-gray-700"><strong>Локация:</strong> {{ $this->session->location }}</p>
                    <p class="text-gray-700"><strong>Цена:</strong> {{ number_format($this->session->price, 2, '.', ' ') }} ₽</p>
                </div>

                
                
                @if($errorMessage)
                    <div class="mb-3 text-sm text-red-600">
                        {{ $errorMessage }}
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('sessionId', null)" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Отмена
                    </button>
                    <button wire:click="book" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                        Подтвердить
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

