<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $adminRole;
    protected $userRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
            'permissions' => ['manage_users', 'manage_movies', 'manage_auditions', 'view_dashboard']
        ]);

        $this->userRole = Role::create([
            'name' => 'User',
            'description' => 'Regular user role',
            'permissions' => ['view_movies', 'view_dashboard']
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
    public function admin_user_can_access_dashboard()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_can_access_dashboard()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_without_dashboard_permission_cannot_access_dashboard()
    {
        // Create a role without dashboard permission
        $restrictedRole = Role::create([
            'name' => 'Restricted',
            'description' => 'Restricted user role',
            'permissions' => ['view_movies']
        ]);

        $restrictedUser = User::factory()->create([
            'role_id' => $restrictedRole->id,
            'status' => 'active'
        ]);

        $response = $this->actingAs($restrictedUser)
            ->get(route('admin.dashboard'));

        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_user_can_manage_auditions()
    {
        // This would test a route that requires manage_auditions permission
        // For now, we're just testing that the permission exists
        $this->assertTrue($this->adminUser->hasPermission('manage_auditions'));
    }

    /** @test */
    public function regular_user_cannot_manage_auditions()
    {
        $this->assertFalse($this->regularUser->hasPermission('manage_auditions'));
    }
}