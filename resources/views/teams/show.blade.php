<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Team Management
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('teams.formation', $team) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Formation
                </a>
                <a href="{{ route('teams.edit', $team) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Edit Team
                </a>
                <a href="{{ route('teams.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    My Teams
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Team Header -->
            <div class="team-header-card">
                <div class="team-header-content">
                    <div class="team-header-info">
                        <h1>{{ $team->name }}</h1>
                        @if($team->description)
                            <p class="team-header-description">{{ $team->description }}</p>
                        @endif
                        <div class="team-header-stats">
                            <div class="team-stat-item">
                                <span class="team-stat-label">Players</span>
                                <span class="team-stat-value">{{ $team->totalPlayers() }} / 11</span>
                            </div>
                            <div class="team-stat-item">
                                <span class="team-stat-label">Status</span>
                                <span class="team-stat-value">
                                    @if($team->isFull())
                                        <span style="color: #22c55e;">FULL</span>
                                    @else
                                        <span style="color: #f59e0b;">{{ 11 - $team->totalPlayers() }} slots</span>
                                    @endif
                                </span>
                            </div>
                            <div class="team-stat-item">
                                <span class="team-stat-label">Goalkeeper</span>
                                <span class="team-stat-value">
                                    @if($team->hasGoalkeeper()) 
                                        <span style="color: #22c55e;">YES</span>
                                    @else
                                        <span style="color: #ef4444;">NO</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="team-show-container">
                
                <!-- Current Roster -->
                <div class="team-roster-card">
                    <h2 class="team-roster-title">Team Roster</h2>

                    @if($team->players->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-lg font-semibold mb-2">No players yet</p>
                            <p class="text-sm">Add players from the available list →</p>
                        </div>
                    @else
                        <div class="team-roster-list">
                            @foreach($team->players as $player)
                                @php
                                    $positionClass = match($player->position) {
                                        'GK' => 'badge-position-gk',
                                        'DF' => 'badge-position-df',
                                        'MF' => 'badge-position-mf',
                                        'FW' => 'badge-position-fw',
                                        default => 'badge-position-gk'
                                    };
                                @endphp

                                @for($i = 0; $i < $player->pivot->quantity; $i++)
                                    <div class="roster-player-item">
                                        @if($player->image_url)
                                            <img src="{{ $player->image_url }}" 
                                                 alt="{{ $player->name }}"
                                                 class="roster-player-image {{ $player->is_custom ? 'object-cover' : 'object-contain' }}">
                                        @else
                                            <div class="roster-player-image bg-gray-200"></div>
                                        @endif

                                        <div class="roster-player-info">
                                            <div class="roster-player-name">{{ $player->nickname }}</div>
                                            <div class="roster-player-meta">
                                                <span class="roster-player-position badge-position {{ $positionClass }}">
                                                    {{ $player->position }}
                                                </span>
                                                <span class="roster-player-ovr">OVR: {{ $player->total }}</span>
                                            </div>
                                        </div>

                                        @if($player->pivot->quantity > 1)
                                            <span class="roster-player-quantity">x{{ $player->pivot->quantity }}</span>
                                        @endif

                                        <div class="roster-player-actions">
                                            <a href="{{ route('players.show', $player) }}" 
                                               class="roster-action-btn roster-action-view">
                                                View
                                            </a>
                                            <form method="POST" 
                                                action="{{ route('teams.players.remove', [$team, $player]) }}" 
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="roster-action-btn roster-action-remove">
                                                Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endfor
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Available Players -->
                <div class="available-players-card">    
                    <h2 class="team-roster-title">Add Players</h2>

                    <div class="available-players-search">
                        <input type="text" 
                               id="playerSearch" 
                               placeholder="Search players..."
                               onkeyup="filterPlayers()">
                    </div>

                    <div class="available-players-list" id="availablePlayersList">
                        @foreach($availablePlayers as $player)
                            @php
                                $positionClass = match($player->position) {
                                    'GK' => 'badge-position-gk',
                                    'DF' => 'badge-position-df',
                                    'MF' => 'badge-position-mf',
                                    'FW' => 'badge-position-fw',
                                    default => 'badge-position-gk'
                                };
                            @endphp

                            <div class="available-player-item" data-name="{{ strtolower($player->name . ' ' . $player->nickname) }}">
                                @if($player->image_url)
                                    <img src="{{ $player->image_url }}" 
                                         alt="{{ $player->name }}"
                                         class="available-player-image {{ $player->is_custom ? 'object-cover' : 'object-contain' }}">
                                @else
                                    <div class="available-player-image bg-gray-200"></div>
                                @endif

                                <div class="available-player-info">
                                    <div class="available-player-name">{{ $player->nickname }}</div>
                                    <span class="available-player-position badge-position {{ $positionClass }}">
                                        {{ $player->position }}
                                    </span>
                                </div>

                                <form method="POST" action="{{ route('teams.players.add', $team) }}" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                                    <button type="submit" 
                                            class="available-player-add"
                                            {{ $team->isFull() ? 'disabled' : '' }}>
                                        + Add
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function filterPlayers() {
            const search = document.getElementById('playerSearch').value.toLowerCase();
            const players = document.querySelectorAll('.available-player-item');

            players.forEach(player => {
                const name = player.getAttribute('data-name');
                if (name.includes(search)) {
                    player.style.display = 'flex';
                } else {
                    player.style.display = 'none';
                }
            });
        }
    </script>
</x-app-layout>