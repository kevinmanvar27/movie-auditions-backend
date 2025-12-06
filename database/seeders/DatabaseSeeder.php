<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the AdminUserSeeder first
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            MovieAuditionUserSeeder::class,
            AuditionSeeder::class,
            SystemSettingsSeeder::class,
            NotificationSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}