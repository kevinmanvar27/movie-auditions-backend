<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Movie;
use App\Models\MovieRole;

/**
 * @OA\Tag(
 *     name="Admin Movies",
 *     description="API Endpoints for Admin Movies Management"
 * )
 */
class MovieController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/admin/movies",
     *      operationId="getAdminMoviesList",
     *      tags={"Admin Movies"},
     *      summary="Get list of movies (Admin)",
     *      description="Returns list of all movies for admin",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="genre",
     *          in="query",
     *          description="Filter by genre",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Filter by status",
     *          required=false,
     *          @OA\Schema(type="string", enum={"active", "inactive", "upcoming"})
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(ref="#/components/schemas/Movie")
     *              ),
     *              @OA\Property(property="message", type="string", example="Movies retrieved successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index(Request $request)
    {
        // Get filter values
        $genreFilter = $request->input('genre');
        $statusFilter = $request->input('status');
        
        // Build query with filters
        $query = Movie::query();
        
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
        
        return $this->sendResponse($movies, 'Movies retrieved successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/movies",
     *      operationId="storeAdminMovie",
     *      tags={"Admin Movies"},
     *      summary="Store a new movie (Admin)",
     *      description="Creates a new movie",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","genre","end_date","director","status"},
     *              @OA\Property(property="title", type="string", example="Inception"),
     *              @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
     *              @OA\Property(property="genre", type="array", 
     *                  @OA\Items(type="string", example="Sci-Fi")
     *              ),
     *              @OA\Property(property="end_date", type="string", format="date", example="2025-12-31"),
     *              @OA\Property(property="director", type="string", example="Christopher Nolan"),
     *              @OA\Property(property="budget", type="number", example=160000000),
     *              @OA\Property(property="status", type="string", enum={"active", "inactive", "upcoming"}, example="active"),
     *              @OA\Property(property="roles", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="role_type", type="string", example="Lead Actor"),
     *                      @OA\Property(property="gender", type="string", example="Male"),
     *                      @OA\Property(property="age_range", type="string", example="25-35"),
     *                      @OA\Property(property="dialogue_sample", type="string", example="Sample dialogue...")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Movie"),
     *              @OA\Property(property="message", type="string", example="Movie created successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     */
    public function store(Request $request)
    {
        try {
            // Validate and store the movie
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'genre' => 'required|array|max:100',
                'genre.*' => 'string|max:100',
                'end_date' => 'required|date',
                'director' => 'required|string|max:100',
                'budget' => 'nullable|numeric|min:0',
                'status' => 'required|string|in:active,inactive,upcoming',
                // Role fields validation
                'roles' => 'nullable|array',
                'roles.*.role_type' => 'nullable|string|max:50',
                'roles.*.gender' => 'nullable|string|max:20',
                'roles.*.age_range' => 'nullable|string|max:20',
                'roles.*.dialogue_sample' => 'nullable|string|max:1000',
            ];
            
            $validated = $request->validate($rules);

            // Extract movie data and role data
            $movieData = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'genre' => json_encode($validated['genre']),
                'end_date' => $validated['end_date'],
                'director' => $validated['director'],
                'budget' => $validated['budget'] ?? null,
                'status' => $validated['status'],
            ];
            
            // Create the movie in the database
            $movie = Movie::create($movieData);
            
            // Process roles if provided
            if (!empty($validated['roles'])) {
                foreach ($validated['roles'] as $roleData) {
                    // Only save if role data is not empty
                    if (!empty(array_filter($roleData))) {
                        $role = new MovieRole($roleData);
                        $movie->roles()->save($role);
                    }
                }
            }

            return $this->sendResponse($movie->fresh(), 'Movie created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create movie: ' . $e->getMessage());
            return $this->sendError('Failed to create movie. Please try again.');
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/movies/{id}",
     *      operationId="getAdminMovieById",
     *      tags={"Admin Movies"},
     *      summary="Get movie information (Admin)",
     *      description="Returns movie data with roles",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Movie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Movie"),
     *              @OA\Property(property="message", type="string", example="Movie retrieved successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Movie not found"
     *      )
     *     )
     */
    public function show(Movie $movie)
    {
        // Load roles relationship
        $movie->load('roles');
        
        return $this->sendResponse($movie, 'Movie retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/movies/{id}",
     *      operationId="updateAdminMovie",
     *      tags={"Admin Movies"},
     *      summary="Update existing movie (Admin)",
     *      description="Updates a movie",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Movie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","genre","end_date","director","status"},
     *              @OA\Property(property="title", type="string", example="Inception"),
     *              @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
     *              @OA\Property(property="genre", type="array", 
     *                  @OA\Items(type="string", example="Sci-Fi")
     *              ),
     *              @OA\Property(property="end_date", type="string", format="date", example="2025-12-31"),
     *              @OA\Property(property="director", type="string", example="Christopher Nolan"),
     *              @OA\Property(property="budget", type="number", example=160000000),
     *              @OA\Property(property="status", type="string", enum={"active", "inactive", "upcoming"}, example="active"),
     *              @OA\Property(property="roles", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="role_type", type="string", example="Lead Actor"),
     *                      @OA\Property(property="gender", type="string", example="Male"),
     *                      @OA\Property(property="age_range", type="string", example="25-35"),
     *                      @OA\Property(property="dialogue_sample", type="string", example="Sample dialogue..."),
     *                      @OA\Property(property="deleted", type="boolean", example=false)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Movie"),
     *              @OA\Property(property="message", type="string", example="Movie updated successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Movie not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     */
    public function update(Request $request, Movie $movie)
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
                'budget' => 'nullable|numeric|min:0',
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

            // Update the movie in the database
            $movie->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'genre' => json_encode($validated['genre']),
                'end_date' => $validated['end_date'],
                'director' => $validated['director'],
                'budget' => $validated['budget'] ?? null,
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
                            $role = MovieRole::findOrFail($roleData['id']);
                            
                            // Check if role should be deleted
                            if (isset($roleData['deleted']) && $roleData['deleted']) {
                                $role->delete();
                            } else {
                                $role->update($roleData);
                            }
                        } else {
                            // Create new role
                            $role = new MovieRole($roleData);
                            $movie->roles()->save($role);
                        }
                    } elseif (isset($roleData['id'])) {
                        // If role data is empty but has an ID, mark for deletion
                        $processedRoleIds[] = $roleData['id'];
                        $role = MovieRole::findOrFail($roleData['id']);
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

            // Load the roles relationship before sending the response
            $movie->load('roles');

            return $this->sendResponse($movie, 'Movie updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update movie: ' . $e->getMessage());
            return $this->sendError('Failed to update movie. Please try again.');
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/movies/{id}",
     *      operationId="deleteAdminMovie",
     *      tags={"Admin Movies"},
     *      summary="Delete movie (Admin)",
     *      description="Deletes a movie",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Movie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Movie deleted successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Movie not found"
     *      )
     *     )
     */
    public function destroy(Movie $movie)
    {
        try {
            // Delete the movie from the database
            $movie->delete();

            return $this->sendResponse([], 'Movie deleted successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to delete movie: ' . $e->getMessage());
        }
    }
}