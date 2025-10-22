<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyActivity>
 */
class DailyActivityFactory extends Factory
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
            'activity_date' => $this->faker->dateTimeThisMonth()->format('Y-m-d'),
            'has_logged_in' => $this->faker->boolean(),
            'has_played_game' => $this->faker->boolean(),
        ];
    }
}
