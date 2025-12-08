<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert the new setting for casting director maximum amount if it doesn't exist
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'casting_director_max_amount'],
            [
                'value' => '5000000',
                'description' => 'Maximum payment amount for casting directors (in rupees)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the casting director maximum amount setting
        DB::table('system_settings')->where('key', 'casting_director_max_amount')->delete();
    }
};