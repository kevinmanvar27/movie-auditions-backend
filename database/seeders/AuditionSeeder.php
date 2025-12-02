<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Audition;
use App\Models\User;
use App\Models\Movie;

class AuditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and movies for testing
        $users = User::take(3)->get();
        $movies = Movie::take(3)->get();
        
        if ($users->count() > 0 && $movies->count() > 0) {
            foreach ($users as $user) {
                foreach ($movies as $movie) {
                    Audition::create([
                        'user_id' => $user->id,
                        'movie_id' => $movie->id,
                        'role' => 'Lead Actor',
                        'applicant_name' => $user->name,
                        'uploaded_videos' => json_encode([
                            'https://example.com/videos/' . strtolower(str_replace(' ', '_', $movie->title)) . '_audition1.mp4',
                            'https://example.com/videos/' . strtolower(str_replace(' ', '_', $movie->title)) . '_audition2.mp4'
                        ]),
                        'old_video_backups' => null,
                        'notes' => 'This is a sample audition submission for the movie ' . $movie->title,
                        'status' => ['pending', 'viewed', 'shortlisted', 'rejected'][array_rand(['pending', 'viewed', 'shortlisted', 'rejected'])]
                    ]);
                }
            }
        }
    }
}