<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Level>
 */
class LevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // O número do nível será definido no Seeder para garantir a sequência
            'grid_size' => $this->faker->numberBetween(8, 20),
            'time_limit_seconds' => $this->faker->numberBetween(120, 300), // Entre 2 e 5 minutos
        ];
    }
}
