<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::create([
            'title' => 'New Movie Audition Available',
            'message' => 'A new audition for the movie "Inception" is now available. Check it out!',
            'filters_applied' => [
                'target_roles' => [2], // Normal User role
                'gender' => 'female',
                'min_age' => 18,
                'max_age' => 35
            ],
            'recipient_count' => 42,
            'status' => 'sent',
            'sent_at' => now()->subDays(2)
        ]);
        
        Notification::create([
            'title' => 'Payment Reminder',
            'message' => 'This is a reminder to complete your payment for the audition submission.',
            'filters_applied' => [
                'target_roles' => [2], // Normal User role
            ],
            'recipient_count' => 18,
            'status' => 'sent',
            'sent_at' => now()->subDay()
        ]);
        
        Notification::create([
            'title' => 'System Maintenance Notice',
            'message' => 'Our system will undergo maintenance on Sunday from 2 AM to 4 AM. Some services may be temporarily unavailable.',
            'filters_applied' => [],
            'recipient_count' => 156,
            'status' => 'sent',
            'sent_at' => now()->subWeek()
        ]);
    }
}