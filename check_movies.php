<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Check what movies we have
$movies = DB::table('movies')->get();
echo 'Total movies: ' . count($movies) . PHP_EOL;

foreach ($movies as $movie) {
    echo 'ID: ' . $movie->id . ', Title: ' . $movie->title . ', Genre: ' . $movie->genre . ', Status: ' . $movie->status . PHP_EOL;
}

