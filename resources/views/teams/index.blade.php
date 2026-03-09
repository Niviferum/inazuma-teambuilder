<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Teams
            </h2>
            <a href="{{ route('teams.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Team
            </a>

        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if($teams->isEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <p class="text-gray-600 mb-4">You don't have any teams yet.</p>
                <a href="{{ route('teams.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Your First Team
                </a>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($teams as $team)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">{{ $team->name }}</h3>

                        @if($team->description)
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($team->description, 100) }}</p>
                        @endif

                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm">
                                <strong>{{ $team->players->count() }}</strong> / 16 players
                            </span>

                            @if($team->hasGoalkeeper())
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">✓ GK</span>
                            @else
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">No GK</span>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('teams.show', $team) }}"
                                class="flex-1 text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                View
                            </a>
                            <a href="{{ route('teams.edit', $team) }}"
                                class="flex-1 text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                            <a href="{{ route('teams.formation', $team) }}"
                                class="flex-1 text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Formation
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</x-app-layout>