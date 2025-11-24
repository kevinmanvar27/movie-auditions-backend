<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all movies from the database
        $movies = \App\Models\Movie::all();
        return view('admin.movies.index', compact('movies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log the incoming request data
            Log::info('Movie store request data:', $request->all());
            
            // Validate and store the movie
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'genre' => 'required|string|max:100',
                'release_date' => 'required|date',
                'director' => 'required|string|max:100',
                'status' => 'required|string|in:active,inactive,upcoming',
            ]);

            // Log the validated data
            Log::info('Validated movie data:', $validated);
            
            // Create the movie in the database
            $movie = \App\Models\Movie::create($validated);
            
            Log::info('Movie created successfully with ID: ' . $movie->id);

            return redirect()->route('admin.movies.index')->with('success', 'Movie created successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create movie: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Failed to create movie. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the movie from the database
        $movie = \App\Models\Movie::findOrFail($id);
        return view('admin.movies.show', compact('movie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Fetch the movie from the database
        $movie = \App\Models\Movie::findOrFail($id);
        return view('admin.movies.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validate and update the movie
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'genre' => 'required|string|max:100',
                'release_date' => 'required|date',
                'director' => 'required|string|max:100',
                'status' => 'required|string|in:active,inactive,upcoming',
            ]);

            // Find and update the movie in the database
            $movie = \App\Models\Movie::findOrFail($id);
            $movie->update($validated);

            return redirect()->route('admin.movies.index')->with('success', 'Movie updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update movie: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update movie. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Delete the movie from the database
            $movie = \App\Models\Movie::findOrFail($id);
            $movie->delete();

            return redirect()->route('admin.movies.index')->with('success', 'Movie deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.movies.index')->with('error', 'Failed to delete movie: ' . $e->getMessage());
        }
    }
}