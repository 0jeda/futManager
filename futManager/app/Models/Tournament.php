<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'name',
        'category',
        'format',
        'status',
        'start_date',
        'end_date',
        'description',
        'is_bracket',
        'bracket_size',
        'bracket_data',
        'winner_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_bracket' => 'boolean',
        'bracket_data' => 'array',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class, 'tournament_team')
            ->withTimestamps()
            ->withPivot('group');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Game::class, 'tournament_id')->orderBy('scheduled_at');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }
}
