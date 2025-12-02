<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Movie;
use App\Models\MovieRole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovieRole>
 */
class MovieRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['open', 'closed', 'filled']),
            'role_type' => $this->faker->randomElement(['leading', 'supporting', 'minor']),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other', 'None']),
            'age_range' => $this->faker->randomElement(['18-25', '25-35', '35-45', '45-60']),
            'dialogue_sample' => $this->faker->paragraph(),
        ];
    }
}