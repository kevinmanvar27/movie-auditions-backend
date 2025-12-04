<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove the audition user percentage setting from the system_settings table
        DB::table('system_settings')->where('key', 'audition_user_percentage')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the audition user percentage setting if needed
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'audition_user_percentage'],
            [
                'value' => null,
                'description' => 'Percentage payment for audition users'
            ]
        );
    }
};