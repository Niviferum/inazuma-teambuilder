<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;  // ← Ajoute cette ligne
use Illuminate\Routing\Controller as BaseController;

class TeamController extends BaseController
{
    use AuthorizesRequests;


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $teams = Auth::user()->teams()->with('players')->get();
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team = Auth::user()->teams()->create($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    public function show(Team $team)
    {
        $this->authorize('update', $team);
        
        $team->load('players');
        
        // Récupérer tous les joueurs qui ne sont pas déjà dans la team
        $availablePlayers = Player::whereNotIn('id', $team->players->pluck('id'))
            ->orderBy('total', 'desc')
            ->get();
        
        return view('teams.show', compact('team', 'availablePlayers'));
    }

    public function edit(Team $team)
    {
        $this->authorize('update', $team);

        return view('teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }

    public function addPlayer(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
        ]);

        if ($team->isFull()) {
            return back()->with('error', 'Team is full! Maximum 11 players allowed.');
        }

        $team->load('players'); // Force le rechargement de la relation

        if ($team->players->contains($validated['player_id'])) {
            $currentQuantity = $team->players->find($validated['player_id'])->pivot->quantity;
            $team->players()->updateExistingPivot($validated['player_id'], [
                'quantity' => $currentQuantity + 1
            ]);
            return back()->with('success', 'Added another copy of this player!');
        }

        $team->players()->attach($validated['player_id'], ['quantity' => 1]);

        return back()->with('success', 'Player added to team!');
    }

    public function removePlayer(Team $team, Player $player)
    {
        $this->authorize('update', $team);

        $currentQuantity = $team->players->find($player->id)->pivot->quantity;

        if ($currentQuantity > 1) {
            $team->players()->updateExistingPivot($player->id, [
                'quantity' => $currentQuantity - 1
            ]);
        } else {
            $team->players()->detach($player->id);
        }

        return back()->with('success', 'Player removed from team!');
    }

    public function formation(Team $team)
    {
        $this->authorize('update', $team);

        $team->load('players');
        $formations = $team->getFormationPositions();

        $playersData = $team->players->keyBy('id')->map(function ($player) {
            return [
                'name'         => $player->name,
                'nickname'     => $player->nickname,
                'image'        => $player->image_url,
                'position'     => $player->position,
                'element'      => $player->element,
                'kick'         => $player->kick ?? 0,
                'control'      => $player->control ?? 0,
                'technique'    => $player->technique ?? 0,
                'intelligence' => $player->intelligence ?? 0,
                'pressure'     => $player->pressure ?? 0,
                'physical'     => $player->physical ?? 0,
                'agility'      => $player->agility ?? 0,
                'skill_1'      => $player->skill_1 ?? '',
                'skill_2'      => $player->skill_2 ?? '',
                'skill_3'      => $player->skill_3 ?? '',
                'skill_4'      => $player->skill_4 ?? '',
            ];
        });

        return view('teams.formation', compact('team', 'formations', 'playersData'));
    }

    public function saveFormation(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        \Log::info('Formation save data:', [
            'formation' => $request->formation,
            'positions' => $request->positions,
        ]);

        $validated = $request->validate([
            'formation'   => 'required|in:4-3-3,4-4-2,3-5-2',
            'positions'   => 'nullable|array',
            'positions.*' => 'nullable|exists:players,id',
        ]);

        // Réinitialiser uniquement les positions, pas détacher les joueurs
        foreach ($team->players as $player) {
            $team->players()->updateExistingPivot($player->id, [
                'formation_position' => null,
                'formation'          => $validated['formation'],
            ]);
        }

        // Assigner les nouvelles positions
        if (!empty($validated['positions'])) {
            foreach ($validated['positions'] as $position => $playerId) {
                if ($playerId) {
                    // Vérifier que le joueur est bien dans l'équipe
                    if ($team->players->contains($playerId)) {
                        $team->players()->updateExistingPivot($playerId, [
                            'formation_position' => $position,
                            'formation'          => $validated['formation'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('teams.formation', $team)
            ->with('success', 'Formation saved successfully!');
    }
}
