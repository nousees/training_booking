<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
                    
                    @auth
                    @if(auth()->user()->isOwner())
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-2">Owner Panel</h2>
                            <p class="mb-4">Welcome, {{ auth()->user()->name }}!</p>
                            <x-primary-button>
                                <a href="{{ route('owner.dashboard') }}" class="text-white no-underline">
                                    Go to Owner Panel
                                </a>
                            </x-primary-button>
                        </div>
                    @elseif(auth()->user()->isManager())
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-2">Manager Panel</h2>
                            <p class="mb-4">Welcome, {{ auth()->user()->name }}!</p>
                            <x-primary-button>
                                <a href="{{ route('manager.dashboard') }}" class="text-white no-underline">
                                    Go to Manager Panel
                                </a>
                            </x-primary-button>
                        </div>
                    @else
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold mb-2">Client Panel</h2>
                            <p class="mb-4">Welcome, {{ auth()->user()->name }}!</p>
                            <x-primary-button>
                                <a href="{{ route('user.dashboard') }}" class="text-white no-underline">
                                    Go to Client Panel
                                </a>
                            </x-primary-button>
                        </div>
                    @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>