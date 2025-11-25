<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Models\Audition;
use App\Models\User;
use App\Models\MovieRole;
use Illuminate\Support\Facades\Hash;

class MovieAuditionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample movies
        $movies = [
            [
                'title' => 'Action Hero',
                'description' => 'An intense action movie with spectacular stunts.',
                'genre' => json_encode(['Action']),
                'release_date' => '2025-12-01',
                'director' => 'John Doe',
                'status' => 'active'
            ],
            [
                'title' => 'Romantic Dreams',
                'description' => 'A beautiful love story set in the hills of Switzerland.',
                'genre' => json_encode(['Romance']),
                'release_date' => '2026-01-20',
                'director' => 'Karan Johar',
                'status' => 'upcoming'
            ],
            [
                'title' => 'The Mystery Case',
                'description' => 'A detective investigates a series of mysterious deaths.',
                'genre' => json_encode(['Crime', 'Thriller']),
                'release_date' => '2026-02-10',
                'director' => 'Anurag Kashyap',
                'status' => 'active'
            ],
            [
                'title' => 'Comedy Nights',
                'description' => 'A hilarious comedy about a group of friends.',
                'genre' => json_encode(['Comedy']),
                'release_date' => '2026-03-05',
                'director' => 'Rajkumar Hirani',
                'status' => 'active'
            ],
            [
                'title' => 'Historical Epic',
                'description' => 'An epic tale of ancient kings and kingdoms.',
                'genre' => json_encode(['Drama', 'History']),
                'release_date' => '2026-04-18',
                'director' => 'Sanjay Leela Bhansali',
                'status' => 'inactive'
            ],
            [
                'title' => 'Space Odyssey',
                'description' => 'A sci-fi adventure in outer space.',
                'genre' => json_encode(['Sci-Fi', 'Adventure']),
                'release_date' => '2026-05-22',
                'director' => 'Imtiaz Ali',
                'status' => 'upcoming'
            ]
        ];

        foreach ($movies as $movieData) {
            Movie::create($movieData);
        }

        // Get all movies for audition creation
        $allMovies = Movie::all();
        $allUsers = User::all();

        // Create sample auditions with different statuses
        $auditionData = [];
        $statuses = ['pending', 'approved', 'rejected'];
        
        for ($i = 1; $i <= 15; $i++) {
            $auditionData[] = [
                'user_id' => $allUsers->random()->id,
                'movie_id' => $allMovies->random()->id,
                'applicant_name' => 'Applicant ' . $i,
                'applicant_email' => 'applicant' . $i . '@example.com',
                'role' => 'Character Role ' . rand(1, 5), // Updated to match schema
                'audition_date' => date('Y-m-d', strtotime('+ ' . rand(1, 30) . ' days')),
                'audition_time' => date('H:i:s', strtotime(rand(9, 18) . ':00:00')), // Fixed time format
                'status' => $statuses[array_rand($statuses)],
                'notes' => 'Sample audition notes for applicant ' . $i
            ];
        }

        foreach ($auditionData as $data) {
            Audition::create($data);
        }

        // Create sample users with different roles and statuses
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        $userRole = \App\Models\Role::where('name', 'User')->first();
        
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole ? $adminRole->id : 1,
                'status' => 'active',
                'email_verified_at' => now()
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $userRole ? $userRole->id : 2,
                'status' => 'active',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $userRole ? $userRole->id : 2,
                'status' => 'inactive',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Robert Johnson',
                'email' => 'robert@example.com',
                'password' => Hash::make('password123'),
                'role_id' => $userRole ? $userRole->id : 2,
                'status' => 'active',
                'email_verified_at' => now()
            ]
        ];

        foreach ($users as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            if (!$existingUser) {
                User::create($userData);
            }
        }
    }
}