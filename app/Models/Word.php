<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'word',
        'translation', // Adicionado
        'image_url',    // Adicionado
        'icon' // Adicionado
    ];

    /**
     * Get the level that owns the Word
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
