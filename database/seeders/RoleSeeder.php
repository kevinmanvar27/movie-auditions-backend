<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $roles = [
            [
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
            ],
            [
                'name' => 'Admin',
                'description' => 'Can manage users and movies',
                'permissions' => [
                    'manage_users',
                    'manage_movies',
                    'manage_auditions',
                    'view_dashboard'
                ]
            ],
            [
                'name' => 'Editor',
                'description' => 'Can manage movies',
                'permissions' => [
                    'manage_movies',
                    'manage_auditions',
                    'view_dashboard'
                ]
            ],
            [
                'name' => 'User',
                'description' => 'Basic user with limited access',
                'permissions' => [
                    'view_movies',
                    'view_dashboard'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}