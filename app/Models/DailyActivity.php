<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_date',
        'has_logged_in',
        'has_played_game',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'has_logged_in' => 'boolean',
            'has_played_game' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the DailyActivity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
