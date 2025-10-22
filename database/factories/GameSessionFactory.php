<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameSession>
 */
class GameSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'current_level_id' => Level::factory(),
            'total_score' => $this->faker->numberBetween(0, 10000),
            'status' => 'active',
        ];
    }
}
