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
        // Convert existing string genres to JSON format
        DB::table('movies')->whereNotNull('genre')->get()->each(function ($movie) {
            if (!empty($movie->genre) && !str_starts_with($movie->genre, '[') && !str_starts_with($movie->genre, '{')) {
                // Convert single genre string to JSON array
                DB::table('movies')->where('id', $movie->id)->update([
                    'genre' => json_encode([$movie->genre])
                ]);
            }
        });
        
        Schema::table('movies', function (Blueprint $table) {
            $table->json('genre')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert JSON genres back to string format (take first genre)
        DB::table('movies')->whereNotNull('genre')->get()->each(function ($movie) {
            if (!empty($movie->genre)) {
                $genres = json_decode($movie->genre, true);
                if (is_array($genres) && !empty($genres)) {
                    DB::table('movies')->where('id', $movie->id)->update([
                        'genre' => $genres[0]
                    ]);
                }
            }
        });
        
        Schema::table('movies', function (Blueprint $table) {
            $table->string('genre')->change();
        });
    }
};
