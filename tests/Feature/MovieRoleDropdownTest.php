<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\MovieRole;

class MovieRoleDropdownTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_fetches_movie_roles_via_ajax()
    {
        $movie = Movie::factory()->create(['status' => 'active']);
        
        // Create some roles for the movie
        $roles = MovieRole::factory()->count(3)->create([
            'movie_id' => $movie->id,
            'status' => 'open'
        ]);
        
        // Create an inactive role that should not be returned
        MovieRole::factory()->create([
            'movie_id' => $movie->id,
            'status' => 'closed'
        ]);

        $response = $this->actingAs($this->user)->getJson(route('movies.roles', $movie));
        
        $response->assertStatus(200);
        $response->assertJsonCount(3); // Should only return open roles
        
        // Check that the returned data has the expected structure
        $response->assertJsonStructure([
            '*' => ['id', 'movie_id', 'status']
        ]);
    }

    /** @test */
    public function it_returns_empty_array_for_movie_with_no_roles()
    {
        $movie = Movie::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->user)->getJson(route('movies.roles', $movie));
        
        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }
}