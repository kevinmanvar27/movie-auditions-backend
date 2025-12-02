<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RBACSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $adminRole;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles with proper permissions
        $this->adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
            'permissions' => ['manage_users', 'manage_movies', 'manage_auditions', 'manage_roles']
        ]);

        $this->userRole = Role::create([
            'name' => 'User',
            'description' => 'Regular user role',
            'permissions' => ['view_movies', 'apply_for_auditions']
        ]);

        // Create users with roles
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->regularUser = User::factory()->create([
            'role_id' => $this->userRole->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function admin_user_can_access_users_management()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_users_management()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.users.index'));

        // Regular users should be redirected or shown an error
        // Depending on how the middleware is implemented
        // With our new permission system, they should be redirected back with an error
        $response->assertSessionHas('error');
    }

    /** @test */
    public function roles_can_be_created_successfully()
    {
        $roleData = [
            'name' => 'Editor',
            'description' => 'Can edit content',
            'permissions' => ['manage_movies']
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.roles.store'), $roleData);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Editor']);
    }

    /** @test */
    public function user_permissions_are_checked_correctly()
    {
        // Admin user should have manage_users permission
        $this->assertTrue($this->adminUser->hasPermission('manage_users'));

        // Regular user should not have manage_users permission
        $this->assertFalse($this->regularUser->hasPermission('manage_users'));

        // Regular user should have view_movies permission
        $this->assertTrue($this->regularUser->hasPermission('view_movies'));
    }

    /** @test */
    public function roles_can_be_updated()
    {
        $updatedData = [
            'name' => 'Updated Admin',
            'description' => 'Updated administrator role',
            'permissions' => ['manage_users', 'manage_roles']
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.roles.update', $this->adminRole), $updatedData);

        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Updated Admin']);
    }
}