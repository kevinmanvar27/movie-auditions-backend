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
                'name' => 'Casting Director',
                'description' => 'Can create movies and roles, manage their own movies and auditions',
                'permissions' => [
                    'manage_movies',
                    'manage_auditions',
                    'view_dashboard',
                    'view_movies'
                ]
            ],
            [
                'name' => 'Normal User',
                'description' => 'Can view movies and apply for auditions',
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