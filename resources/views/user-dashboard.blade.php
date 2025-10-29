<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Client Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Client Panel</h1>
                    <p>Welcome, {{ Auth::user()->name }}!</p>
                    <p class="mt-2">Your role: <strong>Client</strong></p>
                    
                    <div class="mt-6">
                        <p>Here you can book training sessions and view your history.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>