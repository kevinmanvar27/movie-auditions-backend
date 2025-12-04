<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Movie;
use App\Models\Role;

class MovieRoleCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_movie_with_multiple_roles()
    {
        // Create admin role with proper permissions
        $adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
            'permissions' => ['manage_users', 'manage_movies', 'manage_roles', 'manage_settings']
        ]);

        // Create an admin user with proper role_id
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'status' => 'active'
        ]);

        // Authenticate as admin
        $this->actingAs($admin);

        // Define movie data with roles
        $movieData = [
            'title' => 'Test Movie',
            'description' => 'A test movie description',
            'genre' => ['Action'], // Changed from string to array
            'end_date' => '2025-12-25',
            'director' => 'Test Director',
            'budget' => '5000000',
            'status' => 'active',
            'razorpay_payment_id' => 'pay_test123',
            'razorpay_order_id' => 'order_test123',
            'razorpay_signature' => 'sig_test123',
            'roles' => [
                [
                    'role_type' => 'Lead Actor',
                    'gender' => 'Male',
                    'age_range' => '30-40',
                    'dialogue_sample' => 'This is a sample dialogue for the lead role.'
                ],
                [
                    'role_type' => 'Supporting Actress',
                    'gender' => 'Female',
                    'age_range' => '25-35',
                    'dialogue_sample' => 'This is a sample dialogue for the supporting role.'
                ]
            ]
        ];

        // Submit the form
        $response = $this->post(route('admin.movies.store'), $movieData);

        // Assert redirection and success message
        $response->assertStatus(302); // Redirect status
        $response->assertSessionHas('success', 'Movie created successfully!');

        // Verify movie was created
        $this->assertDatabaseHas('movies', [
            'title' => 'Test Movie',
            'director' => 'Test Director'
        ]);

        // Verify roles were created
        $movie = Movie::where('title', 'Test Movie')->first();
        $this->assertCount(2, $movie->roles);

        // Verify role details
        $this->assertDatabaseHas('movie_roles', [
            'movie_id' => $movie->id,
            'role_type' => 'Lead Actor',
            'gender' => 'Male',
            'age_range' => '30-40'
        ]);

        $this->assertDatabaseHas('movie_roles', [
            'movie_id' => $movie->id,
            'role_type' => 'Supporting Actress',
            'gender' => 'Female',
            'age_range' => '25-35'
        ]);
    }
}