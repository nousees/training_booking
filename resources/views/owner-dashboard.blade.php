<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Owner Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Gym Owner Panel</h1>
                    <p>Welcome, {{ Auth::user()->name }}!</p>
                    <p class="mt-2">Your role: <strong>Owner</strong></p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="font-semibold">My Gyms</h3>
                            <p class="text-2xl">0</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Trainers</h3>
                            <p class="text-2xl">0</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <h3 class="font-semibold">Bookings</h3>
                            <p class="text-2xl">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="font-semibold">My Gyms</h3>
            <p class="text-2xl">{{ Auth::user()->ownedGyms->count() }}</p>
            <a href="{{ route('owner.gyms.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">Manage Gyms</a>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="font-semibold">Trainers</h3>
            <p class="text-2xl">0</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <h3 class="font-semibold">Bookings</h3>
            <p class="text-2xl">0</p>
        </div>
    </div>
</x-app-layout>