<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MovieControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user for authentication
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_movie()
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.movies.store'), []);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_validates_required_fields_when_updating_a_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->actingAs($this->user)
            ->put(route('admin.movies.update', $movie), []);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function it_creates_a_movie_with_valid_data()
    {
        $movieData = [
            'title' => 'Test Movie',
            'description' => 'A test movie description',
            'genre' => 'Action',
            'release_date' => '2023-01-01',
            'director' => 'Test Director',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.movies.store'), $movieData);

        $response->assertRedirect(route('admin.movies.index'));
        $this->assertDatabaseHas('movies', ['title' => 'Test Movie']);
    }

    /** @test */
    public function it_displays_all_movies()
    {
        Movie::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.movies.index'));

        $response->assertStatus(200);
        $response->assertViewHas('movies');
    }

    /** @test */
    public function it_displays_a_single_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->actingAs($this->user)
            ->get(route('admin.movies.show', $movie));

        $response->assertStatus(200);
        $response->assertViewHas('movie');
    }

    /** @test */
    public function it_updates_a_movie_with_valid_data()
    {
        $movie = Movie::factory()->create();
        $updatedData = [
            'title' => 'Updated Movie',
            'description' => 'An updated movie description',
            'genre' => 'Comedy',
            'release_date' => '2023-12-31',
            'director' => 'Updated Director',
            'status' => 'inactive',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.movies.update', $movie), $updatedData);

        $response->assertRedirect(route('admin.movies.index'));
        $this->assertDatabaseHas('movies', ['title' => 'Updated Movie']);
    }

    /** @test */
    public function it_deletes_a_movie()
    {
        $movie = Movie::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('admin.movies.destroy', $movie));

        $response->assertRedirect(route('admin.movies.index'));
        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
    }
}