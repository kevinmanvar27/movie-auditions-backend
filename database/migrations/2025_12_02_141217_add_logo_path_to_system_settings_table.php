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
        // No schema changes needed since we're using the existing key-value structure
        // The logo path will be stored as a setting with key 'logo_path'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the logo_path setting if it exists
        DB::table('system_settings')->where('key', 'logo_path')->delete();
    }
};