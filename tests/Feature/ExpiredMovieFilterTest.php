<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Role;

class ExpiredMovieFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser;
    protected $adminRole;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
            'permissions' => ['manage_movies', 'manage_users', 'view_movies']
        ]);

        $this->userRole = Role::create([
            'name' => 'User',
            'description' => 'Regular user role',
            'permissions' => ['view_movies']
        ]);

        // Create users
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->userRole->id,
            'status' => 'active'
        ]);

        // Create test movies
        Movie::create([
            'title' => 'Active Movie',
            'description' => 'An active movie',
            'genre' => json_encode(['Action']),
            'end_date' => now()->addDays(30),
            'director' => 'Test Director',
            'status' => 'active'
        ]);

        Movie::create([
            'title' => 'Expired Movie',
            'description' => 'An expired movie',
            'genre' => json_encode(['Drama']),
            'end_date' => now()->subDays(1),
            'director' => 'Test Director',
            'status' => 'active'
        ]);

        Movie::create([
            'title' => 'Future Movie',
            'description' => 'A future movie',
            'genre' => json_encode(['Comedy']),
            'end_date' => now()->addDays(60),
            'director' => 'Test Director',
            'status' => 'upcoming'
        ]);
    }

    /** @test */
    public function normal_users_cannot_see_expired_movies_in_api_response()
    {
        $response = $this->actingAs($this->normalUser)->getJson('/api/v1/movies');

        $response->assertStatus(200);
        $movies = $response->json('data');

        // Should only see the active movie (not expired or upcoming)
        $this->assertCount(1, $movies);
        $this->assertEquals('Active Movie', $movies[0]['title']);
    }

    /** @test */
    public function admins_can_see_all_movies_in_api_response()
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/v1/movies');

        $response->assertStatus(200);
        $movies = $response->json('data');

        // Admins should see all movies
        $this->assertCount(3, $movies);
        
        $titles = array_column($movies, 'title');
        $this->assertContains('Active Movie', $titles);
        $this->assertContains('Expired Movie', $titles);
        $this->assertContains('Future Movie', $titles);
    }

    /** @test */
    public function normal_users_cannot_access_expired_movie_details()
    {
        $expiredMovie = Movie::where('title', 'Expired Movie')->first();

        $response = $this->actingAs($this->normalUser)->getJson("/api/v1/movies/{$expiredMovie->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function admins_can_access_expired_movie_details()
    {
        $expiredMovie = Movie::where('title', 'Expired Movie')->first();

        $response = $this->actingAs($this->adminUser)->getJson("/api/v1/movies/{$expiredMovie->id}");

        $response->assertStatus(200);
        $this->assertEquals('Expired Movie', $response->json('data.title'));
    }
}