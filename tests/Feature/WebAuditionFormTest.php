<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Role;

class WebAuditionFormTest extends TestCase
{
    use RefreshDatabase;

    protected $normalUser;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user role
        $this->userRole = Role::create([
            'name' => 'User',
            'description' => 'Regular user role',
            'permissions' => ['view_movies']
        ]);

        // Create user
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
    }

    /** @test */
    public function expired_movies_do_not_appear_in_audition_creation_form()
    {
        // Visit the audition creation page
        $response = $this->actingAs($this->normalUser)->get(route('auditions.create'));

        $response->assertStatus(200);
        
        // Check that only the active movie appears in the dropdown
        $response->assertSee('Active Movie');
        $response->assertDontSee('Expired Movie');
        
        // Let's examine what's actually in the response
        $content = $response->getContent();
        
        // Count occurrences of movie options
        $activeMovieCount = substr_count($content, '<option value="');
        $this->assertGreaterThan(0, $activeMovieCount);
        
        // Check that we have the "Choose a movie" option plus the active movie option
        // But we shouldn't have the expired movie option
        $this->assertStringContainsString('Choose a movie', $content);
        $this->assertStringContainsString('Active Movie', $content);
        $this->assertStringNotContainsString('Expired Movie', $content);
    }
}