<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)
            ->withPivot('quantity', 'formation_position', 'formation')
            ->withTimestamps();
    }

    // Vérifier si l'équipe est complète (16 joueurs max)
    public function isFull(): bool
    {
        return $this->players()->sum('quantity') >= 16;
    }


    public function totalPlayers(): int
    {
        return $this->players()->sum('quantity');
    }

    // Vérifier si l'équipe a un gardien
    public function hasGoalkeeper(): bool
    {
        return $this->players()->where('position', 'GK')->exists();
    }

    public function getFormationPositions(): array
    {
        return [
            '4-3-3' => [
                1  => ['top' => '88%', 'left' => '50%', 'label' => 'GK'],   // GK
                2  => ['top' => '70%', 'left' => '15%', 'label' => 'DF'],   // DF
                3  => ['top' => '70%', 'left' => '38%', 'label' => 'DF'],
                4  => ['top' => '70%', 'left' => '62%', 'label' => 'DF'],
                5  => ['top' => '70%', 'left' => '85%', 'label' => 'DF'],
                6  => ['top' => '48%', 'left' => '20%', 'label' => 'MF'],   // MF
                7  => ['top' => '48%', 'left' => '50%', 'label' => 'MF'],
                8  => ['top' => '48%', 'left' => '80%', 'label' => 'MF'],
                9  => ['top' => '20%', 'left' => '20%', 'label' => 'FW'],   // FW
                10 => ['top' => '15%', 'left' => '50%', 'label' => 'FW'],
                11 => ['top' => '20%', 'left' => '80%', 'label' => 'FW'],
            ],
            '4-4-2' => [
                1  => ['top' => '88%', 'left' => '50%', 'label' => 'GK'],
                2  => ['top' => '70%', 'left' => '15%', 'label' => 'DF'],
                3  => ['top' => '70%', 'left' => '38%', 'label' => 'DF'],
                4  => ['top' => '70%', 'left' => '62%', 'label' => 'DF'],
                5  => ['top' => '70%', 'left' => '85%', 'label' => 'DF'],
                6  => ['top' => '48%', 'left' => '15%', 'label' => 'MF'],
                7  => ['top' => '48%', 'left' => '38%', 'label' => 'MF'],
                8  => ['top' => '48%', 'left' => '62%', 'label' => 'MF'],
                9  => ['top' => '48%', 'left' => '85%', 'label' => 'MF'],
                10 => ['top' => '20%', 'left' => '35%', 'label' => 'FW'],
                11 => ['top' => '20%', 'left' => '65%', 'label' => 'FW'],
            ],
            '3-5-2' => [
                1  => ['top' => '88%', 'left' => '50%', 'label' => 'GK'],
                2  => ['top' => '70%', 'left' => '20%', 'label' => 'DF'],
                3  => ['top' => '70%', 'left' => '50%', 'label' => 'DF'],
                4  => ['top' => '70%', 'left' => '80%', 'label' => 'DF'],
                5  => ['top' => '48%', 'left' => '10%', 'label' => 'MF'],
                6  => ['top' => '48%', 'left' => '28%', 'label' => 'MF'],
                7  => ['top' => '48%', 'left' => '50%', 'label' => 'MF'],
                8  => ['top' => '48%', 'left' => '72%', 'label' => 'MF'],
                9  => ['top' => '48%', 'left' => '90%', 'label' => 'MF'],
                10 => ['top' => '20%', 'left' => '35%', 'label' => 'FW'],
                11 => ['top' => '20%', 'left' => '65%', 'label' => 'FW'],
            ],
        ];
    }
}
