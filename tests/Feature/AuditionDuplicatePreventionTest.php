<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Audition;

class AuditionDuplicatePreventionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $movie;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->movie = Movie::factory()->create();
    }

    /** @test */
    public function user_cannot_create_multiple_auditions_for_same_movie_role_combination()
    {
        Storage::fake('public');
        
        // Create first audition
        $videoFile = UploadedFile::fake()->create('video.mp4', 1000, 'video/mp4');
        
        $auditionData = [
            'movie_id' => $this->movie->id,
            'role' => 'Lead Actor',
            'applicant_name' => $this->user->name,
            'uploaded_videos' => $videoFile,
            'notes' => 'Test audition notes',
        ];

        // Submit first audition
        $response = $this->actingAs($this->user)->post(route('auditions.store'), $auditionData);
        
        $response->assertRedirect(route('auditions.index'));
        $response->assertSessionHas('success');
        
        // Verify first audition was created
        $this->assertDatabaseHas('auditions', [
            'user_id' => $this->user->id,
            'movie_id' => $this->movie->id,
            'role' => 'Lead Actor'
        ]);

        // Try to submit the same audition again
        $response2 = $this->actingAs($this->user)->post(route('auditions.store'), $auditionData);
        
        // Should redirect back with error
        $response2->assertRedirect();
        $response2->assertSessionHas('error');
        $response2->assertSessionHas('error', 'You have already submitted an audition for this movie-role combination.');
        
        // Verify only one audition exists in database
        $this->assertEquals(1, Audition::where([
            'user_id' => $this->user->id,
            'movie_id' => $this->movie->id,
            'role' => 'Lead Actor'
        ])->count());
    }

    /** @test */
    public function user_can_create_auditions_for_different_roles_in_same_movie()
    {
        Storage::fake('public');
        
        // Create first audition for Lead Actor role
        $videoFile1 = UploadedFile::fake()->create('video1.mp4', 1000, 'video/mp4');
        
        $auditionData1 = [
            'movie_id' => $this->movie->id,
            'role' => 'Lead Actor',
            'applicant_name' => $this->user->name,
            'uploaded_videos' => $videoFile1,
            'notes' => 'Test audition notes for lead actor',
        ];

        $response1 = $this->actingAs($this->user)->post(route('auditions.store'), $auditionData1);
        
        $response1->assertRedirect(route('auditions.index'));
        $response1->assertSessionHas('success');

        // Create second audition for Supporting Actor role in same movie
        $videoFile2 = UploadedFile::fake()->create('video2.mp4', 1000, 'video/mp4');
        
        $auditionData2 = [
            'movie_id' => $this->movie->id,
            'role' => 'Supporting Actor',
            'applicant_name' => $this->user->name,
            'uploaded_videos' => $videoFile2,
            'notes' => 'Test audition notes for supporting actor',
        ];

        $response2 = $this->actingAs($this->user)->post(route('auditions.store'), $auditionData2);
        
        $response2->assertRedirect(route('auditions.index'));
        $response2->assertSessionHas('success');
        
        // Verify both auditions exist
        $this->assertDatabaseHas('auditions', [
            'user_id' => $this->user->id,
            'movie_id' => $this->movie->id,
            'role' => 'Lead Actor'
        ]);
        
        $this->assertDatabaseHas('auditions', [
            'user_id' => $this->user->id,
            'movie_id' => $this->movie->id,
            'role' => 'Supporting Actor'
        ]);
        
        // Should have two auditions for this user and movie
        $this->assertEquals(2, Audition::where([
            'user_id' => $this->user->id,
            'movie_id' => $this->movie->id
        ])->count());
    }

    /** @test */
    public function user_can_create_auditions_for_same_role_in_different_movies()
    {
        Storage::fake('public');
        
        $movie1 = $this->movie;
        $movie2 = Movie::factory()->create();
        
        // Create first audition for Lead Actor role in first movie
        $videoFile1 = UploadedFile::fake()->create('video1.mp4', 1000, 'video/mp4');
        
        $auditionData1 = [
            'movie_id' => $movie1->id,
            'role' => 'Lead Actor',
            'applicant_name' => $this->user->name,
            'uploaded_videos' => $videoFile1,
            'notes' => 'Test audition notes for first movie',
        ];

        $response1 = $this->actingAs($this->user)->post(route('auditions.store'), $auditionData1);
        
        $response1->assertRedirect(route('auditions.index'));
        $response1->assertSessionHas('success');

        // Create second audition for Lead Actor role in second movie
        $videoFile2 = UploadedFile::fake()->create('video2.mp4', 1000, 'video/mp4');
        
        $auditionData2 = [
            'movie_id' => $movie2->id,
            'role' => 'Lead Actor',
            'applicant_name' => $this->user->name,
            'uploaded_videos' => $videoFile2,
            'notes' => 'Test audition notes for second movie',
        ];

        $response2 = $this->actingAs($this->user)->post(route('auditions.store'), $auditionData2);
        
        $response2->assertRedirect(route('auditions.index'));
        $response2->assertSessionHas('success');
        
        // Verify both auditions exist
        $this->assertDatabaseHas('auditions', [
            'user_id' => $this->user->id,
            'movie_id' => $movie1->id,
            'role' => 'Lead Actor'
        ]);
        
        $this->assertDatabaseHas('auditions', [
            'user_id' => $this->user->id,
            'movie_id' => $movie2->id,
            'role' => 'Lead Actor'
        ]);
        
        // Should have two auditions for this user with same role but different movies
        $this->assertEquals(2, Audition::where([
            'user_id' => $this->user->id,
            'role' => 'Lead Actor'
        ])->count());
    }
}