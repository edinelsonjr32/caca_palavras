<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_level_id',
        'total_score',
        'status',
    ];

    /**
     * Get the user that owns the GameSession
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the current level for the GameSession
     */
    public function currentLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'current_level_id');
    }
}
