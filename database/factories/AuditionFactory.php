<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Movie;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Audition>
 */
class AuditionFactory extends Factory
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
            'movie_id' => Movie::factory(),
            'role' => $this->faker->jobTitle(),
            'applicant_name' => $this->faker->name(),
            'uploaded_videos' => json_encode([$this->faker->url(), $this->faker->url()]),
            'old_video_backups' => null,
            'notes' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'viewed', 'shortlisted', 'rejected']),
        ];
    }
}