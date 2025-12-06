<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add manage_notifications permission to the Super Admin role
        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
        
        if ($superAdminRole) {
            // Decode the existing permissions
            $permissions = json_decode($superAdminRole->permissions, true) ?? [];
            
            // Add manage_notifications permission if it doesn't exist
            if (!in_array('manage_notifications', $permissions)) {
                $permissions[] = 'manage_notifications';
                
                // Update the role with the new permissions
                DB::table('roles')
                    ->where('id', $superAdminRole->id)
                    ->update([
                        'permissions' => json_encode($permissions),
                        'updated_at' => now(),
                    ]);
            }
        }
    }
}