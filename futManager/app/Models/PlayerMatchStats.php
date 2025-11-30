<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMatchStats extends Model
{
    protected $fillable = [
        'match_id',
        'player_id',
        'team_id',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
    ];

    protected $casts = [
        'goals' => 'integer',
        'assists' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'match_id');
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
