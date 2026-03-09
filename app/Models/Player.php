<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    protected $fillable = [
        'player_id',
        'name',
        'nickname',
        'position',
        'element',
        'rarity',
        'team_origin',
        'description',
        'kick',
        'control',
        'technique',
        'intelligence',
        'pressure',
        'physical',
        'agility',
        'total',
        'image_url',
        'skill_1',
        'skill_2',
        'skill_3',
        'skill_4',
        'is_custom',
        'created_by',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    // Scopes pour les filtres
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeElement($query, $element)
    {
        return $query->where('element', $element);
    }

    public function scopeTeamOrigin($query, $team)
    {
        return $query->where('team_origin', 'like', '%' . $team . '%');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->is_custom && $this->created_by === $user->id;
    }
}
