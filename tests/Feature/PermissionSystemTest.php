<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionSystemTest extends TestCase
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
            'permissions' => ['manage_users', 'manage_movies', 'manage_auditions', 'manage_roles', 'manage_settings']
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

        // Regular users should be redirected back with an error message
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_user_can_access_movies_management()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.movies.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_movies_management()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.movies.index'));

        // Regular users should be redirected back with an error message
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_user_can_access_auditions_management()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.auditions.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_auditions_management()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.auditions.index'));

        // Regular users should be redirected back with an error message
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_user_can_access_roles_management()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.roles.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_roles_management()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.roles.index'));

        // Regular users should be redirected back with an error message
        $response->assertSessionHas('error');
    }

    /** @test */
    public function admin_user_can_access_settings()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.settings.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_settings()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.settings.index'));

        // Regular users should be redirected back with an error message
        $response->assertSessionHas('error');
    }

    /** @test */
    public function all_authenticated_users_can_access_their_profile()
    {
        // Admin user can access profile
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.profile'));

        $response->assertStatus(200);

        // Regular user can also access profile
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.profile'));

        $response->assertStatus(200);
    }
}