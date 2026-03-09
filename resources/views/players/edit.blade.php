<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Player: {{ $player->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="player-form-container sm:px-6 lg:px-8">
            <div class="player-form-card">
                <h1 class="player-form-title">Edit Player</h1>

                <form method="POST" action="{{ route('players.update', $player) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Section : Informations de base -->
                    <div class="form-section">
                        <h2 class="form-section-title">Basic Information</h2>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="name" class="form-label form-label-required">Full Name</label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $player->name) }}"
                                       required
                                       class="form-input"
                                       placeholder="e.g., Mark Evans">
                                @error('name')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nickname" class="form-label form-label-required">Nickname</label>
                                <input type="text" 
                                       name="nickname" 
                                       id="nickname" 
                                       value="{{ old('nickname', $player->nickname) }}"
                                       required
                                       class="form-input"
                                       placeholder="e.g., Endou">
                                @error('nickname')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="position" class="form-label form-label-required">Position</label>
                                <select name="position" id="position" required class="form-select">
                                    <option value="">Select a position</option>
                                    <option value="GK" {{ old('position', $player->position) == 'GK' ? 'selected' : '' }}>GK - Goalkeeper</option>
                                    <option value="DF" {{ old('position', $player->position) == 'DF' ? 'selected' : '' }}>DF - Defender</option>
                                    <option value="MF" {{ old('position', $player->position) == 'MF' ? 'selected' : '' }}>MF - Midfielder</option>
                                    <option value="FW" {{ old('position', $player->position) == 'FW' ? 'selected' : '' }}>FW - Forward</option>
                                </select>
                                @error('position')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="element" class="form-label form-label-required">Element</label>
                                <select name="element" id="element" required class="form-select">
                                    <option value="">Select an element</option>
                                    <option value="Feu" {{ old('element', $player->element) == 'Feu' ? 'selected' : '' }}>🔥 Fire</option>
                                    <option value="Vent" {{ old('element', $player->element) == 'Vent' ? 'selected' : '' }}>💨 Wind</option>
                                    <option value="Montagne" {{ old('element', $player->element) == 'Montagne' ? 'selected' : '' }}>⛰️ Mountain</option>
                                    <option value="Forêt" {{ old('element', $player->element) == 'Forêt' ? 'selected' : '' }}>🌳 Forest</option>
                                </select>
                                @error('element')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="team_origin" class="form-label">Team Origin</label>
                            <input type="text" 
                                   name="team_origin" 
                                   id="team_origin" 
                                   value="{{ old('team_origin', $player->team_origin) }}"
                                   class="form-input"
                                   placeholder="e.g., Raimon">
                            @error('team_origin')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-input form-textarea"
                                      placeholder="Player description...">{{ old('description', $player->description) }}</textarea>
                            @error('description')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image" class="form-label">Player Image</label>
                            
                            @if($player->image_url)
                                <div class="image-preview-container mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Current image:</p>
                                    <img src="{{ $player->image_url }}" class="image-preview" alt="Current image">
                                </div>
                            @endif

                            <input type="file" 
                                   name="image" 
                                   id="image" 
                                   accept="image/*"
                                   class="form-input"
                                   onchange="previewImage(event)">
                            <p class="form-help-text">Leave empty to keep current image. Maximum 2MB - JPG, PNG, GIF, WEBP</p>
                            @error('image')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <div id="imagePreview" class="image-preview-container hidden">
                                <p class="text-sm text-gray-600 mb-2">New image preview:</p>
                                <img id="preview" class="image-preview" src="" alt="Preview">
                            </div>
                        </div>
                    </div>

                    <!-- Section : Statistics -->
                    <div class="form-section">
                        <h2 class="form-section-title">Statistics</h2>

                        <div class="total-stats-indicator" id="totalStatsIndicator">
                            <div class="total-stats-label">Total OVR</div>
                            <div class="total-stats-value" id="totalStats">{{ $player->total }}</div>
                            <div class="total-stats-max">/ 680 max</div>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="kick" class="form-label form-label-required">Kick</label>
                                <div class="stat-input-group">
                                    <input type="number" 
                                           name="kick" 
                                           id="kick" 
                                           value="{{ old('kick', $player->kick) }}"
                                           min="0"
                                           max="150"
                                           required
                                           class="form-input"
                                           onchange="updateTotal()">
                                    <span class="stat-value-display" id="kickValue">{{ $player->kick }}</span>
                                </div>
                                @error('kick')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="control" class="form-label form-label-required">Control</label>
                                <div class="stat-input-group">
                                    <input type="number" 
                                           name="control" 
                                           id="control" 
                                           value="{{ old('control', $player->control) }}"
                                           min="0"
                                           max="150"
                                           required
                                           class="form-input"
                                           onchange="updateTotal()">
                                    <span class="stat-value-display" id="controlValue">{{ $player->control }}</span>
                                </div>
                                @error('control')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="technique" class="form-label form-label-required">Technique</label>
                                <div class="stat-input-group">
                                    <input type="number" 
                                           name="technique" 
                                           id="technique" 
                                           value="{{ old('technique', $player->technique) }}"
                                           min="0"
                                           max="150"
                                           required
                                           class="form-input"
                                           onchange="updateTotal()">
                                    <span class="stat-value-display" id="techniqueValue">{{ $player->technique }}</span>
                                </div>
                                @error('technique')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="intelligence" class="form-label form-label-required">Intelligence</label>
                                <div class="stat-input-group">
                                    <input type="number" 
                                           name="intelligence" 
                                           id="intelligence" 
                                           value="{{ old('intelligence', $player->intelligence) }}"
                                           min="0"
                                           max="150"
                                           required
                                           class="form-input"
                                           onchange="updateTotal()">
                                    <span class="stat-value-display" id="intelligenceValue">{{ $player->intelligence }}</span>
                                </div>
                                @error('intelligence')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="pressure" class="form-label form-label-required">Pressure</label>
                                <div class="stat-input-group">
                                    <input type="number" 
                                           name="pressure" 
                                           id="pressure" 
                                           value="{{ old('pressure', $player->pressure) }}"
                                           min="0"
                                           max="150"
                                           required
                                           class="form-input"
                                           onchange="updateTotal()">
                                    <span class="stat-value-display" id="pressureValue">{{ $player->pressure }}</span>
                                </div>
                                @error('pressure')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="physical" class="form-label form-label-required">Physical</label>
                                <div class="stat-input-group">
                                    <input type="number" 
                                           name="physical" 
                                           id="physical" 
                                           value="{{ old('physical', $player->physical) }}"
                                           min="0"
                                           max="150"
                                           required
                                           class="form-input"
                                           onchange="updateTotal()">
                                    <span class="stat-value-display" id="physicalValue">{{ $player->physical }}</span>
                                </div>
                                @error('physical')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="agility" class="form-label form-label-required">Agility</label>
                                <div class="stat-input-group">
                                    <input type="number" 
                                           name="agility" 
                                           id="agility" 
                                           value="{{ old('agility', $player->agility) }}"
                                           min="0"
                                           max="150"
                                           required
                                           class="form-input"
                                           onchange="updateTotal()">
                                    <span class="stat-value-display" id="agilityValue">{{ $player->agility }}</span>
                                </div>
                                @error('agility')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section : Skills -->
                    <div class="form-section">
                        <h2 class="form-section-title">Techniques</h2>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="skill_1" class="form-label">Skill 1</label>
                                <input type="text" 
                                       name="skill_1" 
                                       id="skill_1" 
                                       value="{{ old('skill_1', $player->skill_1) }}"
                                       class="form-input"
                                       placeholder="e.g., God Hand">
                                @error('skill_1')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="skill_2" class="form-label">Skill 2</label>
                                <input type="text" 
                                       name="skill_2" 
                                       id="skill_2" 
                                       value="{{ old('skill_2', $player->skill_2) }}"
                                       class="form-input">
                                @error('skill_2')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="skill_3" class="form-label">Skill 3</label>
                                <input type="text" 
                                       name="skill_3" 
                                       id="skill_3" 
                                       value="{{ old('skill_3', $player->skill_3) }}"
                                       class="form-input">
                                @error('skill_3')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="skill_4" class="form-label">Skill 4</label>
                                <input type="text" 
                                       name="skill_4" 
                                       id="skill_4" 
                                       value="{{ old('skill_4', $player->skill_4) }}"
                                       class="form-input">
                                @error('skill_4')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="form-submit-btn">
                            Update Player
                        </button>
                        <a href="{{ route('players.show', $player) }}" class="form-cancel-btn">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateTotal() {
            const stats = ['kick', 'control', 'technique', 'intelligence', 'pressure', 'physical', 'agility'];
            let total = 0;

            stats.forEach(stat => {
                const value = parseInt(document.getElementById(stat).value) || 0;
                document.getElementById(stat + 'Value').textContent = value;
                total += value;
            });

            document.getElementById('totalStats').textContent = total;
            
            const indicator = document.getElementById('totalStatsIndicator');
            if (total > 680) {
                indicator.classList.add('total-stats-warning');
            } else {
                indicator.classList.remove('total-stats-warning');
            }
        }

        function previewImage(event) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        // Initialiser au chargement
        window.onload = updateTotal;
    </script>
</x-app-layout>