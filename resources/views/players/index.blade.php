<x-public-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Players
            </h2>
            @auth
                <a href="{{ route('players.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Create Player
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filtres -->
            <div class="filters-container">
                <form method="GET" action="{{ route('players.index') }}" class="filters-grid">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Name or nickname..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <select name="position" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">All positions</option>
                            <option value="GK" {{ request('position') == 'GK' ? 'selected' : '' }}>GK - Goalkeeper</option>
                            <option value="DF" {{ request('position') == 'DF' ? 'selected' : '' }}>DF - Defender</option>
                            <option value="MF" {{ request('position') == 'MF' ? 'selected' : '' }}>MF - Midfielder</option>
                            <option value="FW" {{ request('position') == 'FW' ? 'selected' : '' }}>FW - Forward</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Element</label>
                        <select name="element" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">All elements</option>
                            <option value="Feu" {{ request('element') == 'Feu' ? 'selected' : '' }}>🔥 Fire</option>
                            <option value="Vent" {{ request('element') == 'Vent' ? 'selected' : '' }}>💨 Wind</option>
                            <option value="Montagne" {{ request('element') == 'Montagne' ? 'selected' : '' }}>⛰️ Mountain</option>
                            <option value="Forêt" {{ request('element') == 'Forêt' ? 'selected' : '' }}>🌳 Forest</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des joueurs -->
            <div class="players-grid">
                @foreach($players as $player)
                    @php
                        $positionClass = match($player->position) {
                            'GK' => 'badge-position-gk',
                            'DF' => 'badge-position-df',
                            'MF' => 'badge-position-mf',
                            'FW' => 'badge-position-fw',
                            default => 'badge-position-gk'
                        };
                        
                        $elementIcons = [
                            'Feu'      => 'Fire_icon.webp',
                            'Vent'     => 'Wind_icon.webp',
                            'Montagne' => 'Mountain_icon.webp',
                            'Forêt'    => 'Forest_icon.webp',
                        ];
                        $elementIcon = $elementIcons[$player->element] ?? null;
                    @endphp

                    <a href="{{ route('players.show', $player) }}" class="player-card">
                        
                        @if($player->image_url)
                            <img src="{{ $player->image_url }}" 
                                 alt="{{ $player->name }}"
                                 class="player-card-image {{ $player->is_custom ? 'rounded-md' : '' }}">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-full mb-3 flex items-center justify-center">
                                <span class="text-gray-400 text-2xl">?</span>
                            </div>
                        @endif

                        <h3 class="player-card-name">{{ $player->name }}</h3>
                        <p class="player-card-nickname">{{ $player->nickname }}</p>
                        
                        <div class="player-badges">
                            <span class="badge-position {{ $positionClass }}">
                                {{ $player->position }}
                            </span>
                            @if($elementIcon)
                                <img src="{{ asset('images/elements/' . $elementIcon) }}" 
                                     alt="{{ $player->element }}"
                                     class="element-icon"
                                     title="{{ $player->element }}">
                            @endif
                        </div>

                        <p class="player-ovr">{{ $player->total }}</p>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $players->links() }}
            </div>
        </div>
    </div>
</x-public-layout>