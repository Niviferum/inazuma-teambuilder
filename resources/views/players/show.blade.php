<x-public-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Player Details
        </h2>
        <div class="flex gap-2">
            @auth
                <button onclick="openTeamSelectModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    + Add to Team
                </button>
                @if($player->isOwnedBy(auth()->user()))
                    <a href="{{ route('players.edit', $player) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('players.destroy', $player) }}" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete
                        </button>
                    </form>
                @endif
            @endauth
            <a href="{{ route('players.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Players
            </a>
        </div>
    </div>
</x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="player-detail-container">

    <!-- GAUCHE : Hero Card - Image et infos principales -->
    @php
        $elementClass = match($player->element) {
            'Feu' => 'element-fire',
            'Vent' => 'element-wind',
            'Forêt' => 'element-forest',
            'Montagne' => 'element-mountain',
            default => ''
        };
        
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
    
    <div class="player-hero-card {{ $elementClass }}">
        @if($player->image_url)
            <img src="{{ $player->image_url }}" 
                 alt="{{ $player->name }}"
                 class="player-hero-image {{ $player->is_custom ? 'rounded-md' : '' }}">
        @else
            <div class="w-full max-w-xs h-64 bg-gray-200 rounded-md flex items-center justify-center mb-4">
                <span class="text-gray-400 text-4xl">?</span>
            </div>
        @endif

        <h1 class="player-hero-name">{{ $player->name }}</h1>
        <p class="player-hero-nickname">{{ $player->nickname }}</p>
        
        <div class="player-hero-badges">
            <span class="badge-position {{ $positionClass }}">
                {{ $player->position }}
            </span>
            @if($elementIcon)
                <img src="{{ asset('images/elements/' . $elementIcon) }}" 
                     alt="{{ $player->element }}"
                     class="element-icon"
                     title="{{ $player->element }}">
            @endif
            @if($player->is_custom)
                <span class="badge-position" style="background-color: #8b5cf6; color: white;">
                    CUSTOM
                </span>
            @endif
        </div>

        <p class="player-hero-ovr">{{ $player->total }}</p>
    </div>

    <!-- HAUT DROITE : Description -->
    @if($player->description)
        <div class="player-description-card">
            <h2 class="player-stats-title">Description</h2>
            <p class="text-sm text-gray-700">{{ $player->description }}</p>

            <div class="player-info-grid mt-4">
                <div class="player-info-item">
                    <span class="player-info-label">Rarity</span>
                    <span class="player-info-value">{{ $player->rarity }}</span>
                </div>
                @if($player->team_origin)
                    <div class="player-info-item">
                        <span class="player-info-label">Team Origin</span>
                        <span class="player-info-value">{{ $player->team_origin }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- MILIEU DROITE : Stats Card avec Radar Chart -->
    <div class="player-stats-card">
        <h2 class="player-stats-title">Statistics</h2>
        <div class="player-radar-container">
            <canvas id="statsRadar"></canvas>
        </div>
    </div>

    <!-- BAS DROITE : Skills Card -->
    <div class="player-skills-card">
        <h2 class="player-stats-title">Techniques</h2>
        <div class="player-skills-grid">
            @for($i = 1; $i <= 4; $i++)
                @php
                    $skillName = $player->{'skill_' . $i};
                @endphp
                <div class="skill-item {{ !$skillName ? 'skill-empty' : '' }}">
                    <div class="skill-number">Technique {{ $i }}</div>
                    <div class="skill-name">{{ $skillName ?: 'No technique' }}</div>
                </div>
            @endfor
        </div>
    </div>

</div>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('statsRadar');
    
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Kick', 'Control', 'Technique', 'Intelligence', 'Pressure', 'Physical', 'Agility'],
            datasets: [{
                label: '{{ $player->nickname }}',
                data: [
                    {{ $player->kick ?? 0 }},
                    {{ $player->control ?? 0 }},
                    {{ $player->technique ?? 0 }},
                    {{ $player->intelligence ?? 0 }},
                    {{ $player->pressure ?? 0 }},
                    {{ $player->physical ?? 0 }},
                    {{ $player->agility ?? 0 }}
                ],
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgb(59, 130, 246)',
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(59, 130, 246)',
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            elements: {
                line: {
                    borderWidth: 3
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    suggestedMin: 0,
                    suggestedMax: 150,
                    ticks: {
                        stepSize: 30,
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    pointLabels: {
                        font: {
                            size: 13,
                            weight: 'bold'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>

 <!-- Modal de sélection de team -->
    @auth
        <div id="teamSelectModal" class="team-select-modal">
            <div class="team-select-modal-content">
                <div class="team-select-modal-header">
                    <h3 class="team-select-modal-title">Add to Team</h3>
                    <button onclick="closeTeamSelectModal()" class="team-select-modal-close">&times;</button>
                </div>

                <div class="team-select-list">
                    @php
                        $userTeams = auth()->user()->teams;
                    @endphp

                    @if($userTeams->isEmpty())
                        <div class="team-select-empty">
                            <p class="font-semibold mb-2">You don't have any teams yet</p>
                            <a href="{{ route('teams.create') }}" class="text-blue-500 hover:underline">Create your first team</a>
                        </div>
                    @else
                        @foreach($userTeams as $team)
                            <div class="team-select-item">
                                <div class="team-select-item-info">
                                    <div class="team-select-item-name">{{ $team->name }}</div>
                                    <div class="team-select-item-stats">
                                        {{ $team->totalPlayers() }}/11 players
                                        @if($team->isFull())
                                            <span style="color: #ef4444;">• Full</span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('teams.players.add', $team) }}">
                                    @csrf
                                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                                    <button type="submit" 
                                            class="team-select-item-btn"
                                            {{ $team->isFull() ? 'disabled' : '' }}>
                                        Add
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <script>
            function openTeamSelectModal() {
                document.getElementById('teamSelectModal').classList.add('active');
            }

            function closeTeamSelectModal() {
                document.getElementById('teamSelectModal').classList.remove('active');
            }

            // Fermer si clic en dehors du modal
            document.getElementById('teamSelectModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeTeamSelectModal();
                }
            });
        </script>
    @endauth
</x-public-layout>

