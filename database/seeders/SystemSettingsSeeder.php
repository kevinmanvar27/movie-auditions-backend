<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default system settings
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Movie Auditions Platform',
                'description' => 'The name of the website'
            ],
            [
                'key' => 'site_description',
                'value' => 'A platform for managing movies and casting.',
                'description' => 'Description of the website'
            ],
            [
                'key' => 'admin_email',
                'value' => 'admin@example.com',
                'description' => 'Administrator email address'
            ],
            [
                'key' => 'logo_path',
                'value' => null,
                'description' => 'Path to the site logo'
            ],
            // Payment settings
            [
                'key' => 'casting_director_amount',
                'value' => null,
                'description' => 'Fixed payment amount for casting directors'
            ],
            [
                'key' => 'casting_director_percentage',
                'value' => null,
                'description' => 'Percentage payment for casting directors'
            ],
            [
                'key' => 'audition_user_amount',
                'value' => null,
                'description' => 'Fixed payment amount for audition users'
            ],
            // Razorpay settings
            [
                'key' => 'razorpay_key_id',
                'value' => null,
                'description' => 'Razorpay Key ID for payment processing'
            ],
            [
                'key' => 'razorpay_key_secret',
                'value' => null,
                'description' => 'Razorpay Key Secret for payment processing'
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}