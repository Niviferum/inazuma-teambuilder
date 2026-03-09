<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Formation - {{ $team->name }}
            </h2>
            <a href="{{ route('teams.show', $team) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Team
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

            <form method="POST" action="{{ route('teams.formation.save', $team) }}" id="formation-form">
                @csrf

                <!-- Sélecteur de formation -->
                <div class="formation-selector-card">
                    <h3 class="formation-selector-title">⚽ Choose Formation</h3>
                    <select name="formation" 
                            id="formation-select"
                            class="formation-select"
                            onchange="updateFormation(this.value)">
                        @foreach(array_keys($formations) as $formationName)
                            <option value="{{ $formationName }}" 
                                {{ ($team->pitchPlayers->first()?->pivot->formation ?? '4-3-3') == $formationName ? 'selected' : '' }}>
                                {{ $formationName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Terrain -->
                    <div class="lg:col-span-2 formation-pitch-card">
                        <h3 class="formation-selector-title">🏟️ Pitch</h3>

                        <div class="formation-pitch">
                            <!-- Lignes du terrain -->
                            <div class="pitch-border"></div>
                            <div class="pitch-midline"></div>
                            <div class="pitch-center-circle"></div>
                            <div class="pitch-center-dot"></div>
                            <div class="pitch-penalty-area-top"></div>
                            <div class="pitch-penalty-area-bottom"></div>

                            @php
                                $currentFormation = $team->pitchPlayers->first()?->pivot->formation ?? '4-3-3';
                                
                                $assignedPlayers = [];
                                foreach ($team->pitchPlayers as $player) {
                                    if ($player->pivot->formation_position) {
                                        $assignedPlayers[$player->pivot->formation_position] = $player;
                                    }
                                }
                            @endphp

                            <!-- Conteneur des joueurs (positions 1 à 11) -->
                            <div id="player-positions">
                                @for($i = 1; $i <= 11; $i++)
                                    @php
                                        $assignedPlayer = $assignedPlayers[$i] ?? null;
                                        $coords = $formations[$currentFormation][$i];
                                    @endphp
                                    <div class="player-position"
                                         data-position="{{ $i }}"
                                         style="top: {{ $coords['top'] }}; left: {{ $coords['left'] }};">
                                        
                                        <div class="position-slot"
                                            onclick="openPlayerSelector({{ $i }})"
                                            data-player-id="{{ $assignedPlayer?->id ?? '' }}"
                                            data-position="{{ $i }}">

                                            @if($assignedPlayer)
                                                <img src="{{ $assignedPlayer->image_url }}"
                                                    alt="{{ $assignedPlayer->name }}"
                                                    class="player-avatar">
                                                <span class="player-nickname">
                                                    {{ $assignedPlayer->nickname }}
                                                </span>
                                            @else
                                                <div class="empty-slot">
                                                    <span class="empty-slot-number">{{ $i }}</span>
                                                </div>
                                                <span class="empty-slot-label">
                                                    {{ $coords['label'] }}
                                                </span>
                                            @endif
                                        </div>

                                        <input type="hidden"
                                            name="positions[{{ $i }}]"
                                            id="input_{{ $i }}"
                                            value="{{ $assignedPlayer?->id ?? '' }}">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Panneau latéral : Bench -->
                    <div class="formation-roster-card">
                        <h3 class="formation-selector-title">🪑 Bench ({{ $team->benchCount() }}/5)</h3>
                        <div class="formation-roster-list">
                            @if($team->benchPlayers->isEmpty())
                                <div class="text-center py-8 text-gray-500">
                                    <p class="text-sm">No players on bench</p>
                                    <p class="text-xs mt-2">Go to Team page to add players</p>
                                </div>
                            @else
                                @foreach($team->benchPlayers as $player)
                                    @php
                                        $positionClass = match($player->position) {
                                            'GK' => 'badge-position-gk',
                                            'DF' => 'badge-position-df',
                                            'MF' => 'badge-position-mf',
                                            'FW' => 'badge-position-fw',
                                            default => 'badge-position-gk'
                                        };
                                    @endphp
                                    
                                    <div class="roster-player-item player-item"
                                        data-player-id="{{ $player->id }}"
                                        data-player-name="{{ $player->nickname }}"
                                        data-player-position="{{ $player->position }}"
                                        data-player-image="{{ $player->image_url }}">
                                        @if($player->image_url)
                                            <img src="{{ $player->image_url }}" class="roster-player-image {{ $player->is_custom ? 'object-cover' : 'object-contain' }}">
                                        @endif
                                        <div class="roster-player-info">
                                            <p class="roster-player-name">{{ $player->name }}</p>
                                            <div class="roster-player-meta">
                                                <span class="badge-position {{ $positionClass }}">{{ $player->position }}</span>
                                                <span class="roster-player-ovr">OVR: {{ $player->total }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                💾 Save Formation
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Modal de sélection de joueur -->
    <div id="player-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-96 max-h-96 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg" id="modal-title">Select Player</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="modal-player-list" class="space-y-2"></div>
        </div>
    </div>

    <script>
        const formations = @json($formations);
        const playersData = @json($playersData);
        
        let currentPosition = null;

        function updateFormation(formationName) {
            const formationCoords = formations[formationName];
            
            for (let i = 1; i <= 11; i++) {
                const playerPos = document.querySelector(`.player-position[data-position="${i}"]`);
                const coords = formationCoords[i];
                
                playerPos.style.top = coords.top;
                playerPos.style.left = coords.left;
                
                const slot = playerPos.querySelector('.position-slot');
                if (!slot.dataset.playerId) {
                    const labelSpan = playerPos.querySelector('.empty-slot-label');
                    if (labelSpan) {
                        labelSpan.textContent = coords.label;
                    }
                }
            }
        }

        function openPlayerSelector(position) {
            currentPosition = position;

            document.getElementById('modal-title').textContent = `Select Player for Position ${position}`;

            const players = document.querySelectorAll('.player-item');
            const modalList = document.getElementById('modal-player-list');
            modalList.innerHTML = '';

            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'border rounded p-2 cursor-pointer hover:bg-red-50 text-red-500 font-semibold text-sm';
            emptyDiv.textContent = '✕ Remove player from this position';
            emptyDiv.onclick = () => selectPlayer(null, null, null);
            modalList.appendChild(emptyDiv);

            players.forEach(player => {
                const div = document.createElement('div');
                div.className = 'border rounded p-2 flex items-center gap-2 cursor-pointer hover:bg-blue-50';
                div.innerHTML = `
                    <img src="${player.dataset.playerImage}" class="w-10 h-10 object-contain">
                    <div>
                        <p class="font-semibold text-sm">${player.dataset.playerName}</p>
                        <p class="text-xs text-gray-500">${player.dataset.playerPosition}</p>
                    </div>
                `;
                div.onclick = () => selectPlayer(
                    player.dataset.playerId,
                    player.dataset.playerName,
                    player.dataset.playerImage
                );
                modalList.appendChild(div);
            });

            document.getElementById('player-modal').classList.remove('hidden');
        }

        function selectPlayer(playerId, playerName, playerImage) {
            const input = document.getElementById(`input_${currentPosition}`);
            const slot = document.querySelector(`.position-slot[data-position="${currentPosition}"]`);
            const formation = document.getElementById('formation-select').value;
            const label = formations[formation][currentPosition].label;

            input.value = playerId ?? '';

            if (playerId) {
                slot.setAttribute('data-player-id', playerId);
                slot.innerHTML = `
                    <img src="${playerImage}"
                         alt="${playerName}"
                         class="player-avatar">
                    <span class="player-nickname">
                        ${playerName}
                    </span>
                `;
            } else {
                slot.removeAttribute('data-player-id');
                slot.innerHTML = `
                    <div class="empty-slot">
                        <span class="empty-slot-number">${currentPosition}</span>
                    </div>
                    <span class="empty-slot-label">
                        ${label}
                    </span>
                `;
            }

            closeModal();
        }

        function closeModal() {
            document.getElementById('player-modal').classList.add('hidden');
            currentPosition = null;
        }

        document.getElementById('player-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>

</x-app-layout>