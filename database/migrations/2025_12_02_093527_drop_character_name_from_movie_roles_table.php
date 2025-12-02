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
            if (Schema::hasColumn('movie_roles', 'character_name')) {
                $table->dropColumn('character_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movie_roles', function (Blueprint $table) {
            if (!Schema::hasColumn('movie_roles', 'character_name')) {
                $table->string('character_name');
            }
        });
    }
};
