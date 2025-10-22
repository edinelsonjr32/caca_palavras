<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all of the game sessions for the User
     */
    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * Get all of the rankings for the User
     */
    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }

    /**
     * Get all of the daily activities for the User
     */
    public function dailyActivities(): HasMany
    {
        return $this->hasMany(DailyActivity::class);
    }
}
