<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SimpleRBACTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function role_based_access_control_works()
    {
        // Create roles
        $adminRole = Role::create([
            'name' => 'Admin',
            'description' => 'Administrator role',
            'permissions' => ['manage_users', 'manage_movies']
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'description' => 'Regular user role',
            'permissions' => ['view_movies']
        ]);

        // Create users with roles
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->id,
            'status' => 'active'
        ]);

        $regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'role_id' => $userRole->id,
            'status' => 'active'
        ]);

        // Test that admin user has manage_users permission
        $this->assertTrue($adminUser->hasPermission('manage_users'));

        // Test that regular user does not have manage_users permission
        $this->assertFalse($regularUser->hasPermission('manage_users'));

        // Test that regular user has view_movies permission
        $this->assertTrue($regularUser->hasPermission('view_movies'));

        // Test that admin user has the correct role
        $this->assertTrue($adminUser->hasRole('Admin'));

        // Test that regular user has the correct role
        $this->assertTrue($regularUser->hasRole('User'));
    }
}