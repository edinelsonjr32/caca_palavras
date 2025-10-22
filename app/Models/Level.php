<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_number',
        'grid_size',
        'time_limit_seconds',
    ];

    /**
     * Get all of the words for the Level
     */
    public function words(): HasMany
    {
        return $this->hasMany(Word::class);
    }
}
