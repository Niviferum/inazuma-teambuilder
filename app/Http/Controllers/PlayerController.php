<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // ← Ajoute cette ligne

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $query = Player::query();

        // Filtres
        if ($request->filled('position')) {
            $query->position($request->position);
        }

        if ($request->filled('element')) {
            $query->element($request->element);
        }

        if ($request->filled('team_origin')) {
            $query->teamOrigin($request->team_origin);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nickname', 'like', '%' . $request->search . '%');
            });
        }

        $players = $query->orderBy('total', 'desc')->paginate(20);

        return view('players.index', compact('players'));
    }

    public function show(Player $player)
    {
        return view('players.show', compact('player'));
    }

    public function create()
    {
        return view('players.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'nickname'    => 'nullable|string|max:255',
            'position'    => 'required|in:GK,DF,MF,FW',
            'element'     => 'required|in:Feu,Vent,Montagne,Forêt',
            'kick'        => 'required|integer|min:1|max:150',
            'control'     => 'required|integer|min:1|max:150',
            'technique'   => 'required|integer|min:1|max:150',
            'intelligence' => 'required|integer|min:1|max:150',
            'pressure'    => 'required|integer|min:1|max:150',
            'physical'    => 'required|integer|min:1|max:150',
            'agility'     => 'required|integer|min:1|max:150',
            'skill_1'     => 'nullable|string|max:255',
            'skill_2'     => 'nullable|string|max:255',
            'skill_3'     => 'nullable|string|max:255',
            'skill_4'     => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $total = array_sum([
            $validated['kick'],
            $validated['control'],
            $validated['technique'],
            $validated['intelligence'],
            $validated['pressure'],
            $validated['physical'],
            $validated['agility'],
        ]);

        if ($total > 680) {
            return back()->withErrors(['total' => 'Total stats cannot exceed 680!'])->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('players', 'public');
            $imagePath = asset('storage/' . $imagePath);
        }

        $maxId = Player::max('player_id') ?? 0;

        Player::create([
            ...$validated,
            'player_id'  => $maxId + 1,
            'total'      => $total,
            'image_url'  => $imagePath,
            'is_custom'  => true,
            'created_by' => Auth::id(),
            'rarity'     => 'Custom',
        ]);

        return redirect()->route('players.index')->with('success', 'Player created successfully!');
    }

    public function edit(Player $player)
    {
        if (!$player->isOwnedBy(Auth::user())) {
            abort(403);
        }

        return view('players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        if (!$player->isOwnedBy(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'nickname'    => 'nullable|string|max:255',
            'position'    => 'required|in:GK,DF,MF,FW',
            'element'     => 'required|in:Feu,Vent,Montagne,Forêt',
            'kick'        => 'required|integer|min:1|max:150',
            'control'     => 'required|integer|min:1|max:150',
            'technique'   => 'required|integer|min:1|max:150',
            'intelligence' => 'required|integer|min:1|max:150',
            'pressure'    => 'required|integer|min:1|max:150',
            'physical'    => 'required|integer|min:1|max:150',
            'agility'     => 'required|integer|min:1|max:150',
            'skill_1'     => 'required|string|max:25',
            'skill_2'     => 'nullable|string|max:25',
            'skill_3'     => 'nullable|string|max:25',
            'skill_4'     => 'nullable|string|max:25',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $total = array_sum([
            $validated['kick'],
            $validated['control'],
            $validated['technique'],
            $validated['intelligence'],
            $validated['pressure'],
            $validated['physical'],
            $validated['agility'],
        ]);

        if ($total > 680) {
            return back()->withErrors(['total' => 'Total stats cannot exceed 680!'])->withInput();
        }

        $imagePath = $player->image_url;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('players', 'public');
            $imagePath = asset('storage/' . $imagePath);
        }

        $player->update([
            ...$validated,
            'total'     => $total,
            'image_url' => $imagePath,
        ]);

        return redirect()->route('players.show', $player)->with('success', 'Player updated successfully!');
    }

    public function destroy(Player $player)
    {
        if (!$player->isOwnedBy(Auth::user())) {
            abort(403);
        }

        $player->delete();

        return redirect()->route('players.index')->with('success', 'Player deleted successfully!');
    }
}
