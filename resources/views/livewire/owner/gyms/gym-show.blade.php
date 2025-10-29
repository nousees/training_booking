<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $gym->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex justify-between items-center">
                        <a href="{{ route('owner.gyms.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to Gyms
                        </a>
                        <x-primary-button>
                            <a href="{{ route('owner.gyms.edit', $gym) }}" class="text-white no-underline">
                                Edit Gym
                            </a>
                        </x-primary-button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Gym Details -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Gym Information</h3>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1">{{ $gym->name }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1">{{ $gym->description ?? 'No description' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1">{{ $gym->address }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Contact</dt>
                                    <dd class="mt-1">{{ $gym->phone }} | {{ $gym->email }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Working Hours</dt>
                                    <dd class="mt-1">{{ $gym->opening_time->format('H:i') }} - {{ $gym->closing_time->format('H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $gym->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $gym->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Statistics -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Statistics</h3>
                            <div class="space-y-4">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h4 class="font-semibold">Trainers</h4>
                                    <p class="text-2xl">0</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <h4 class="font-semibold">Training Types</h4>
                                    <p class="text-2xl">0</p>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <h4 class="font-semibold">Bookings</h4>
                                    <p class="text-2xl">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>