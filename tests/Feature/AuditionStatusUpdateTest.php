<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Audition;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuditionStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $movie;
    protected $audition;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin role with proper permissions
        $adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
            'permissions' => ['manage_users', 'manage_movies', 'manage_roles', 'manage_settings']
        ]);
        
        // Create regular user role
        $userRole = Role::create([
            'name' => 'User',
            'description' => 'Regular user role',
            'permissions' => []
        ]);
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'status' => 'active'
        ]);
        
        // Create regular user
        $this->regularUser = User::factory()->create([
            'role_id' => $userRole->id,
            'status' => 'active'
        ]);
        
        // Create a movie
        $this->movie = Movie::factory()->create();
        
        // Create an audition
        $this->audition = Audition::create([
            'user_id' => $this->regularUser->id,
            'movie_id' => $this->movie->id,
            'role' => 'Lead Actor',
            'applicant_name' => $this->regularUser->name,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function admin_can_update_audition_status()
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.auditions.update-status', $this->audition), [
                'status' => 'shortlisted'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Audition status updated successfully.',
            'status' => 'shortlisted'
        ]);
        
        $this->assertDatabaseHas('auditions', [
            'id' => $this->audition->id,
            'status' => 'shortlisted'
        ]);
    }

    /** @test */
    public function admin_can_update_audition_status_to_viewed()
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.auditions.update-status', $this->audition), [
                'status' => 'viewed'
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Audition status updated successfully.',
            'status' => 'viewed'
        ]);
        
        $this->assertDatabaseHas('auditions', [
            'id' => $this->audition->id,
            'status' => 'viewed'
        ]);
    }

    /** @test */
    public function regular_user_cannot_update_audition_status()
    {
        $response = $this->actingAs($this->regularUser)
            ->postJson(route('admin.auditions.update-status', $this->audition), [
                'status' => 'shortlisted'
            ]);

        // Regular users should get a 403 forbidden response
        // Since this is an AJAX request, it should return 403 directly
        $response->assertStatus(403);
    }

    /** @test */
    public function audition_status_update_requires_valid_status()
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.auditions.update-status', $this->audition), [
                'status' => 'invalid_status'
            ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors'
        ]);
    }

    /** @test */
    public function audition_status_update_requires_status_field()
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson(route('admin.auditions.update-status', $this->audition), []);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'message',
            'errors'
        ]);
    }
}