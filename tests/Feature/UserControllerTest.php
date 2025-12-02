<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles first
        $this->userRole = Role::create([
            'name' => 'User',
            'description' => 'Basic user role',
            'permissions' => ['view_movies', 'apply_for_auditions']
        ]);
        
        // Create an admin role with proper permissions
        $adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
            'permissions' => ['manage_users', 'manage_movies', 'manage_auditions', 'manage_roles', 'manage_settings']
        ]);
        
        // Create an admin user for authentication with proper role_id
        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_displays_all_users()
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    /** @test */
    public function it_creates_a_user_with_valid_data()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->userRole->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    /** @test */
    public function it_updates_a_user_with_valid_data()
    {
        $user = User::factory()->create();
        
        $updatedData = [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'role_id' => $this->userRole->id,
            'status' => 'inactive',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.users.update', $user), $updatedData);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'updated@example.com']);
    }

    /** @test */
    public function it_deletes_a_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}