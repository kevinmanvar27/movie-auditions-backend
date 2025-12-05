<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the admin user already exists
        $adminUser = User::where('email', 'rektech.uk@gmail.com')->first();
        
        if (!$adminUser) {
            // Get the Super Admin role
            $superAdminRole = \App\Models\Role::where('name', 'Super Admin')->first();
            
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'rektech.uk@gmail.com',
                'password' => Hash::make('RekTech@27'),
                'email_verified_at' => now(),
                'role_id' => $superAdminRole ? $superAdminRole->id : null,
            ]);
        } else {
            // If user exists but doesn't have a role, assign Super Admin role
            if (!$adminUser->role_id) {
                $superAdminRole = \App\Models\Role::where('name', 'Super Admin')->first();
                if ($superAdminRole) {
                    $adminUser->role_id = $superAdminRole->id;
                    $adminUser->save();
                }
            }
        }
    }
}