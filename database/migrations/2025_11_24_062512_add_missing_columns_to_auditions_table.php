<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->string('role')->nullable();
            $table->string('video_url')->nullable();
            // Remove the movie_role_id column and foreign key if it exists
            $table->dropForeign(['movie_role_id']);
            $table->dropColumn('movie_role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auditions', function (Blueprint $table) {
            $table->dropColumn(['role', 'video_url']);
            // Re-add the movie_role_id column and foreign key
            $table->foreignId('movie_role_id')->constrained()->onDelete('cascade');
        });
    }
};
