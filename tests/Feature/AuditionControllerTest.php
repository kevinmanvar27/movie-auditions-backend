<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Audition;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuditionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $movie;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user for authentication
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'status' => 'active'
        ]);
        
        // Create a movie for the auditions
        $this->movie = Movie::factory()->create();
    }

    /** @test */
    public function it_displays_all_auditions()
    {
        Audition::factory()->count(3)->create([
            'movie_id' => $this->movie->id,
            'user_id' => $this->adminUser->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.auditions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('auditions');
    }

    /** @test */
    public function it_creates_an_audition_with_valid_data()
    {
        $auditionData = [
            'movie_id' => $this->movie->id,
            'user_id' => $this->adminUser->id,
            'applicant_name' => 'Test Applicant',
            'applicant_email' => 'applicant@example.com',
            'role' => 'Lead Role',
            'audition_date' => '2023-01-01',
            'audition_time' => '10:00:00',
            'status' => 'pending',
            'notes' => 'Test notes',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.auditions.store'), $auditionData);

        $response->assertRedirect(route('admin.auditions.index'));
        $this->assertDatabaseHas('auditions', ['applicant_name' => 'Test Applicant']);
    }

    /** @test */
    public function it_updates_an_audition_with_valid_data()
    {
        $audition = Audition::factory()->create([
            'movie_id' => $this->movie->id,
            'user_id' => $this->adminUser->id
        ]);
        
        $updatedData = [
            'movie_id' => $this->movie->id,
            'user_id' => $this->adminUser->id,
            'applicant_name' => 'Updated Applicant',
            'applicant_email' => 'updated@example.com',
            'role' => 'Supporting Role',
            'audition_date' => '2023-12-31',
            'audition_time' => '14:00:00',
            'status' => 'approved',
            'notes' => 'Updated notes',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.auditions.update', $audition), $updatedData);

        $response->assertRedirect(route('admin.auditions.index'));
        $this->assertDatabaseHas('auditions', ['applicant_name' => 'Updated Applicant']);
    }

    /** @test */
    public function it_deletes_an_audition()
    {
        $audition = Audition::factory()->create([
            'movie_id' => $this->movie->id,
            'user_id' => $this->adminUser->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.auditions.destroy', $audition));

        $response->assertRedirect(route('admin.auditions.index'));
        $this->assertDatabaseMissing('auditions', ['id' => $audition->id]);
    }
}