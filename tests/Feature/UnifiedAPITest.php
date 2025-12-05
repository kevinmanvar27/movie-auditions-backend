<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class UnifiedAPITest extends TestCase
{
    use RefreshDatabase;

    protected $superAdminUser;
    protected $castingDirectorUser;
    protected $normalUser;
    protected $superAdminRole;
    protected $castingDirectorRole;
    protected $normalUserRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles with proper permissions based on our RoleSeeder
        $this->superAdminRole = Role::create([
            'name' => 'Super Admin',
            'description' => 'Full access to all system features',
            'permissions' => [
                'manage_users',
                'manage_roles',
                'manage_movies',
                'manage_auditions',
                'view_reports',
                'manage_settings',
                'view_movies',
                'view_dashboard'
            ]
        ]);

        $this->castingDirectorRole = Role::create([
            'name' => 'Casting Director',
            'description' => 'Can create movies and roles, manage their own movies and auditions',
            'permissions' => [
                'manage_movies',
                'manage_auditions',
                'view_dashboard',
                'view_movies'
            ]
        ]);

        $this->normalUserRole = Role::create([
            'name' => 'Normal User',
            'description' => 'Can view movies and apply for auditions',
            'permissions' => [
                'view_movies',
                'view_dashboard'
            ]
        ]);

        // Create users with roles
        $this->superAdminUser = User::factory()->create([
            'role_id' => $this->superAdminRole->id,
            'status' => 'active'
        ]);

        $this->castingDirectorUser = User::factory()->create([
            'role_id' => $this->castingDirectorRole->id,
            'status' => 'active'
        ]);

        $this->normalUser = User::factory()->create([
            'role_id' => $this->normalUserRole->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function super_admin_can_access_all_movie_endpoints()
    {
        // Test index
        Sanctum::actingAs($this->superAdminUser);
        $response = $this->getJson('/api/v1/movies');
        $response->assertStatus(200);

        // Test store
        $movieData = [
            'title' => 'Test Movie',
            'director' => 'Test Director',
            'end_date' => '2025-12-31',
            'genre' => ['Action', 'Adventure'],
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/movies', $movieData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('movies', ['title' => 'Test Movie']);

        // Get the created movie
        $movie = Movie::where('title', 'Test Movie')->first();

        // Test show
        $response = $this->getJson("/api/v1/movies/{$movie->id}");
        $response->assertStatus(200);

        // Test update
        $updateData = [
            'title' => 'Updated Test Movie',
            'status' => 'inactive'
        ];

        $response = $this->putJson("/api/v1/movies/{$movie->id}", $updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('movies', ['title' => 'Updated Test Movie']);

        // Test destroy
        $response = $this->deleteJson("/api/v1/movies/{$movie->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('movies', ['title' => 'Updated Test Movie']);
    }

    /** @test */
    public function casting_director_can_manage_movies()
    {
        // Test index
        Sanctum::actingAs($this->castingDirectorUser);
        $response = $this->getJson('/api/v1/movies');
        $response->assertStatus(200);

        // Test store
        $movieData = [
            'title' => 'CD Test Movie',
            'director' => 'CD Test Director',
            'end_date' => '2025-12-31',
            'genre' => ['Drama'],
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/movies', $movieData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('movies', ['title' => 'CD Test Movie']);

        // Get the created movie
        $movie = Movie::where('title', 'CD Test Movie')->first();

        // Test show
        $response = $this->getJson("/api/v1/movies/{$movie->id}");
        $response->assertStatus(200);

        // Test update
        $updateData = [
            'title' => 'Updated CD Test Movie'
        ];

        $response = $this->putJson("/api/v1/movies/{$movie->id}", $updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('movies', ['title' => 'Updated CD Test Movie']);

        // Test destroy
        $response = $this->deleteJson("/api/v1/movies/{$movie->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('movies', ['title' => 'Updated CD Test Movie']);
    }

    /** @test */
    public function normal_user_has_limited_access_to_movies()
    {
        // Create a movie as super admin first
        Sanctum::actingAs($this->superAdminUser);
        $movieData = [
            'title' => 'Public Movie',
            'director' => 'Public Director',
            'end_date' => '2025-12-31',
            'genre' => ['Comedy'],
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/movies', $movieData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('movies', ['title' => 'Public Movie']);

        // Get the created movie
        $movie = Movie::where('title', 'Public Movie')->first();
        $this->assertNotNull($movie);

        // Test index as normal user
        Sanctum::actingAs($this->normalUser);
        $response = $this->getJson('/api/v1/movies');
        $response->assertStatus(200);

        // Test show as normal user
        $response = $this->getJson("/api/v1/movies/{$movie->id}");
        $response->assertStatus(200);

        // Test that normal user cannot create movies
        $movieData = [
            'title' => 'Normal User Movie',
            'director' => 'Normal User Director',
            'end_date' => '2025-12-31',
            'genre' => ['Horror'],
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/movies', $movieData);
        $response->assertStatus(403); // Forbidden

        // Test that normal user cannot update movies
        $updateData = [
            'title' => 'Try to Update Public Movie'
        ];

        $response = $this->putJson("/api/v1/movies/{$movie->id}", $updateData);
        $response->assertStatus(403); // Forbidden

        // Test that normal user cannot delete movies
        $response = $this->deleteJson("/api/v1/movies/{$movie->id}");
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function super_admin_can_manage_users()
    {
        Sanctum::actingAs($this->superAdminUser);

        // Test index
        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(200);

        // Test store
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->normalUserRole->id
        ];

        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        // Get the created user
        $user = User::where('email', 'test@example.com')->first();

        // Test show
        $response = $this->getJson("/api/v1/users/{$user->id}");
        $response->assertStatus(200);

        // Test update
        $updateData = [
            'name' => 'Updated Test User',
            'email' => 'updated@example.com',
            'role_id' => $this->castingDirectorRole->id
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'updated@example.com']);

        // Test destroy
        $response = $this->deleteJson("/api/v1/users/{$user->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['email' => 'updated@example.com']);
    }

    /** @test */
    public function casting_director_cannot_manage_users()
    {
        Sanctum::actingAs($this->castingDirectorUser);

        // Test that casting director cannot access users list
        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(403); // Forbidden

        // Test that casting director cannot create users
        $userData = [
            'name' => 'CD Test User',
            'email' => 'cdtest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->normalUserRole->id
        ];

        $response = $this->postJson('/api/v1/users', $userData);
        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function normal_user_can_only_view_their_own_profile()
    {
        Sanctum::actingAs($this->normalUser);

        // Test that normal user can view their own profile
        $response = $this->getJson("/api/v1/users/{$this->normalUser->id}");
        $response->assertStatus(200);

        // Test that normal user cannot view other users' profiles
        $response = $this->getJson("/api/v1/users/{$this->superAdminUser->id}");
        $response->assertStatus(403); // Forbidden

        // Test that normal user can update their own profile
        $updateData = [
            'name' => 'Updated Normal User',
            'email' => 'updatednormal@example.com'
        ];

        $response = $this->putJson("/api/v1/users/{$this->normalUser->id}", $updateData);
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'updatednormal@example.com']);

        // Test that normal user cannot update other users' profiles
        $updateData = [
            'name' => 'Try to Update Admin',
            'email' => 'trytohackadmin@example.com'
        ];

        $response = $this->putJson("/api/v1/users/{$this->superAdminUser->id}", $updateData);
        $response->assertStatus(403); // Forbidden
    }
}