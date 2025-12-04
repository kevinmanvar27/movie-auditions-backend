<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Audition;

class MovieController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter values
        $genreFilter = $request->input('genre');
        $statusFilter = $request->input('status');
        
        // Build query with filters
        $query = \App\Models\Movie::query();
        
        // Apply genre filter if provided
        if ($genreFilter) {
            $query->whereJsonContains('genre', $genreFilter);
        }
        
        // Apply status filter if provided
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        
        // Fetch movies based on filters
        $movies = $query->get();
        
        // Get all unique genres for filter dropdown
        $allGenres = \App\Models\Movie::select('genre')->get();
        $genres = [];
        foreach ($allGenres as $movie) {
            foreach ($movie->genre_list as $genre) {
                if (!in_array($genre, $genres)) {
                    $genres[] = $genre;
                }
            }
        }
        
        // Define status options
        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'upcoming' => 'Upcoming'
        ];
        
        return view('admin.movies.index', compact('movies', 'genres', 'statuses', 'genreFilter', 'statusFilter'));
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
                'genre' => 'required|array|max:100',
                'genre.*' => 'string|max:100',
                'end_date' => 'required|date',
                'director' => 'required|string|max:100',
                'status' => 'required|string|in:active,inactive,upcoming',
                // Role fields validation
                'roles' => 'nullable|array',
                'roles.*.role_type' => 'nullable|string|max:50',
                'roles.*.gender' => 'nullable|string|max:20',
                'roles.*.age_range' => 'nullable|string|max:20',
                'roles.*.dialogue_sample' => 'nullable|string|max:1000',
                // Payment verification fields
                'razorpay_payment_id' => 'required|string',
                'razorpay_order_id' => 'required|string',
                'razorpay_signature' => 'required|string',
            ]);

            // Log the validated data
            Log::info('Validated movie data:', $validated);
            
            // Extract movie data and role data
            $movieData = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'genre' => json_encode($validated['genre']),
                'end_date' => $validated['end_date'],
                'director' => $validated['director'],
                'status' => $validated['status'],
            ];
            
            // Create the movie in the database
            $movie = \App\Models\Movie::create($movieData);
            
            // Process roles if provided
            if (!empty($validated['roles'])) {
                foreach ($validated['roles'] as $roleData) {
                    // Only save if role data is not empty
                    if (!empty(array_filter($roleData))) {
                        $role = new \App\Models\MovieRole($roleData);
                        $movie->roles()->save($role);
                    }
                }
            }
            
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
    public function show(Request $request, string $id)
    {
        // Fetch the movie from the database
        $movie = \App\Models\Movie::findOrFail($id);
        
        // Get the role filter if provided
        $roleFilter = $request->get('role');
        
        // Get the status filter if provided
        $statusFilter = $request->get('status');
        
        // Build the query for auditions
        $auditionQuery = $movie->auditions()->with('user');
        
        // Apply role filter if provided
        if ($roleFilter) {
            $auditionQuery->where('role', $roleFilter);
        }
        
        // Apply status filter if provided
        if ($statusFilter) {
            $auditionQuery->where('status', $statusFilter);
        }
        
        // Get auditions with pagination
        $auditions = $auditionQuery->paginate(9); // 9 per page to fit the 3-column grid
        
        // Get all unique roles for the filter dropdown
        $uniqueRoles = $movie->auditions()->pluck('role')->unique()->sort();
        
        // Define status options for the filter dropdown
        $statusOptions = [
            'pending' => 'Pending',
            'viewed' => 'Viewed',
            'shortlisted' => 'Shortlisted',
            'rejected' => 'Rejected'
        ];
        
        // Pass movie, paginated auditions, filters, and unique values to the view
        return view('admin.movies.show', compact('movie', 'auditions', 'roleFilter', 'statusFilter', 'uniqueRoles', 'statusOptions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Fetch the movie from the database with its roles
        $movie = \App\Models\Movie::with('roles')->findOrFail($id);
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
                'genre' => 'required|array|max:100',
                'genre.*' => 'string|max:100',
                'end_date' => 'required|date',
                'director' => 'required|string|max:100',
                'status' => 'required|string|in:active,inactive,upcoming',
                // Role fields validation
                'roles' => 'nullable|array',
                'roles.*.id' => 'nullable|exists:movie_roles,id',
                'roles.*.role_type' => 'nullable|string|max:50',
                'roles.*.gender' => 'nullable|string|max:20',
                'roles.*.age_range' => 'nullable|string|max:20',
                'roles.*.dialogue_sample' => 'nullable|string|max:1000',
                'roles.*.deleted' => 'nullable|boolean',
            ]);

            // Find and update the movie in the database
            $movie = \App\Models\Movie::findOrFail($id);
            $movie->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'genre' => json_encode($validated['genre']),
                'end_date' => $validated['end_date'],
                'director' => $validated['director'],
                'status' => $validated['status'],
            ]);

            // Process roles if provided
            if (isset($validated['roles'])) {
                // Get all existing role IDs for this movie before processing
                $allExistingRoleIds = $movie->roles()->pluck('id')->toArray();
                $processedRoleIds = [];
                
                foreach ($validated['roles'] as $roleData) {

                    // Check if role data is not empty (excluding id and deleted fields)
                    $filteredRoleData = array_filter($roleData, function($value, $key) {
                        return $key !== 'id' && $key !== 'deleted' && !is_null($value);
                    }, ARRAY_FILTER_USE_BOTH);
                    
                    if (!empty($filteredRoleData)) {
                        if (isset($roleData['id'])) {
                            // Update existing role
                            $processedRoleIds[] = $roleData['id'];
                            $role = \App\Models\MovieRole::findOrFail($roleData['id']);
                            
                            // Check if role should be deleted
                            if (isset($roleData['deleted']) && $roleData['deleted']) {
                                $role->delete();
                            } else {
                                $role->update($roleData);
                            }
                        } else {
                            // Create new role
                            $role = new \App\Models\MovieRole($roleData);
                            $movie->roles()->save($role);
                        }
                    } elseif (isset($roleData['id'])) {
                        // If role data is empty but has an ID, mark for deletion
                        $processedRoleIds[] = $roleData['id'];
                        $role = \App\Models\MovieRole::findOrFail($roleData['id']);
                        $role->delete();
                    } elseif (isset($roleData['deleted']) && $roleData['deleted']) {
                        // If it's a new role marked as deleted, we just ignore it
                        // No action needed, it won't be created
                    }
                }
                
                // Delete roles that were not included in the request (soft delete)
                $rolesToDelete = array_diff($allExistingRoleIds, $processedRoleIds);
                if (!empty($rolesToDelete)) {
                    $movie->roles()->whereIn('id', $rolesToDelete)->delete();
                }
            } else {
                // If no roles were provided, delete all existing roles
                $movie->roles()->delete();
            }

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
    
    /**
     * Update the status of an audition.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Audition $audition
     * @return \Illuminate\Http\Response
     */
    public function updateAuditionStatus(Request $request, \App\Models\Audition $audition)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'status' => 'required|in:pending,viewed,shortlisted,rejected'
            ]);
            
            // Update the audition status
            $audition->status = $validated['status'];
            $audition->save();
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Audition status updated successfully.',
                'status' => $audition->status
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to update audition status: ' . $e->getMessage()
            ], 500);
        }
    }
}