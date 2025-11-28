<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'short_name',
        'coach_name',
        'contact_email',
        'contact_phone',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function tournaments(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tournament::class, 'tournament_team')
            ->withTimestamps()
            ->withPivot('group');
    }
}
