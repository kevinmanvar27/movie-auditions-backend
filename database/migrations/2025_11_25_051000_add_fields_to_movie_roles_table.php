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
        Schema::table('movie_roles', function (Blueprint $table) {
            $table->string('role_type')->nullable();
            $table->string('gender')->nullable();
            $table->string('age_range')->nullable();
            $table->text('dialogue_sample')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie_roles', function (Blueprint $table) {
            $table->dropColumn(['role_type', 'gender', 'age_range', 'dialogue_sample']);
        });
    }
};