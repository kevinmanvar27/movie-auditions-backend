<?php

namespace App\Http\Controllers\API\Unified;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Movie;
use App\Models\User;
use Carbon\Carbon;

/**
 * @OA\Tag(
 *     name="Unified Movies",
 *     description="API Endpoints for Movies Management - Unified for all roles"
 * )
 */
class MovieController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/movies",
     *      operationId="getMoviesList",
     *      tags={"Unified Movies"},
     *      summary="Get list of movies based on user role",
     *      description="Returns list of movies based on the authenticated user's role",
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
        $user = Auth::user();
        
        // Get filter values
        $genreFilter = $request->input('genre');
        $statusFilter = $request->input('status');
        
        // Build query with filters
        $query = Movie::with('roles');
        
        // Check if user has permission to manage movies (admins and casting directors)
        $canManageMovies = false;
        if ($user->hasPermission('manage_movies')) {
            $canManageMovies = true;
        } else {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if ($role && $role->hasPermission('manage_movies')) {
                    $canManageMovies = true;
                }
            }
        }
        
        // Check if user has permission to view movies (normal users)
        $canViewMovies = false;
        if ($user->hasPermission('view_movies')) {
            $canViewMovies = true;
        } else {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if ($role && $role->hasPermission('view_movies')) {
                    $canViewMovies = true;
                }
            }
        }
        
        // Apply filters based on user role
        if ($canManageMovies) {
            // Admins and Casting Directors can see all movies
            // Apply genre filter if provided
            if ($genreFilter) {
                // Since genre is stored as JSON, we need to decode it to check
                $query->whereRaw("json_extract(genre, '$[*]') LIKE ?", ['%"' . $genreFilter . '"%']);
            }
            
            // Apply status filter if provided
            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }
        } elseif ($canViewMovies) {
            // Normal users can only see active movies that haven't expired
            $query->where('status', 'active')
                  ->where('end_date', '>=', Carbon::today());
            
            // Apply genre filter if provided
            if ($genreFilter) {
                $query->whereRaw("json_extract(genre, '$[*]') LIKE ?", ['%"' . $genreFilter . '"%']);
            }
        } else {
            return $this->sendError('You are not authorized to view movies.', [], 403);
        }
        
        // Fetch movies based on filters
        $movies = $query->get();
        
        return $this->sendResponse($movies, 'Movies retrieved successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/v1/movies",
     *      operationId="storeMovie",
     *      tags={"Unified Movies"},
     *      summary="Store a new movie",
     *      description="Creates a new movie - only for users with manage_movies permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","director","end_date","genre","status"},
     *              @OA\Property(property="title", type="string", example="Inception"),
     *              @OA\Property(property="director", type="string", example="Christopher Nolan"),
     *              @OA\Property(property="end_date", type="string", format="date", example="2025-12-31"),
     *              @OA\Property(property="genre", type="array",
     *                  @OA\Items(type="string", example="Sci-Fi")
     *              ),
     *              @OA\Property(property="budget", type="number", format="float", example=160000000.00),
     *              @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
     *              @OA\Property(property="status", type="string", example="active"),
     *              @OA\Property(property="roles", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="role_type", type="string", example="Lead Actor"),
     *                      @OA\Property(property="gender", type="string", example="Male"),
     *                      @OA\Property(property="age_range", type="string", example="25-35"),
     *                      @OA\Property(property="dialogue_sample", type="string", example="Sample dialogue for this role")
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
        $user = Auth::user();
        
        // Only users with manage_movies permission can create movies
        if (!$user->hasPermission('manage_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('manage_movies')) {
                    return $this->sendError('You are not authorized to create movies.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to create movies.', [], 403);
            }
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'end_date' => 'required|date',
            'genre' => 'required|array',
            'genre.*' => 'string|max:50',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,upcoming',
            // Role fields validation
            'roles' => 'nullable|array',
            'roles.*.role_type' => 'nullable|string|max:50',
            'roles.*.gender' => 'nullable|string|max:20',
            'roles.*.age_range' => 'nullable|string|max:20',
            'roles.*.dialogue_sample' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        $movie = new Movie();
        $movie->title = $request->title;
        $movie->director = $request->director;
        $movie->end_date = $request->end_date;
        $movie->genre = $request->genre;
        $movie->budget = $request->budget;
        $movie->description = $request->description;
        $movie->status = $request->status;
        // Note: We're not setting created_by since the Movie model doesn't have this field
        
        if (!$movie->save()) {
            return $this->sendError('Error occurred while creating movie.');
        }
        
        // Process roles if provided
        if ($request->has('roles') && is_array($request->roles)) {
            foreach ($request->roles as $roleData) {
                // Only save if role data is not empty
                if (!empty(array_filter($roleData))) {
                    $role = new \App\Models\MovieRole($roleData);
                    $movie->roles()->save($role);
                }
            }
        }
        
        return $this->sendResponse($movie->fresh()->load('roles'), 'Movie created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/movies/{id}",
     *      operationId="getMovieById",
     *      tags={"Unified Movies"},
     *      summary="Get movie information",
     *      description="Returns movie data based on the authenticated user's role",
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
        $user = Auth::user();
        
        // Check if user has permission to manage movies (admins and casting directors)
        $canManageMovies = false;
        if ($user->hasPermission('manage_movies')) {
            $canManageMovies = true;
        } else {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if ($role && $role->hasPermission('manage_movies')) {
                    $canManageMovies = true;
                }
            }
        }
        
        // Check if user has permission to view movies (normal users)
        $canViewMovies = false;
        if ($user->hasPermission('view_movies')) {
            $canViewMovies = true;
        } else {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if ($role && $role->hasPermission('view_movies')) {
                    $canViewMovies = true;
                }
            }
        }
        
        // Check permissions based on user role
        if ($canManageMovies) {
            // Admins and Casting Directors can see any movie
            // Load roles relationship
            $movie->load('roles');
        } elseif ($canViewMovies) {
            // Normal users can only see active movies that haven't expired
            if ($movie->status !== 'active' || $movie->end_date < Carbon::today()) {
                return $this->sendError('Movie not found.', [], 404);
            }
            // Load roles relationship
            $movie->load('roles');
        } else {
            return $this->sendError('You are not authorized to view this movie.', [], 403);
        }
        
        return $this->sendResponse($movie, 'Movie retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/movies/{id}",
     *      operationId="updateMovie",
     *      tags={"Unified Movies"},
     *      summary="Update existing movie",
     *      description="Updates a movie - only for users with manage_movies permission",
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
     *              @OA\Property(property="title", type="string", example="Inception"),
     *              @OA\Property(property="director", type="string", example="Christopher Nolan"),
     *              @OA\Property(property="end_date", type="string", format="date", example="2025-12-31"),
     *              @OA\Property(property="genre", type="array",
     *                  @OA\Items(type="string", example="Sci-Fi")
     *              ),
     *              @OA\Property(property="budget", type="number", format="float", example=160000000.00),
     *              @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
     *              @OA\Property(property="status", type="string", example="active"),
     *              @OA\Property(property="roles", type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="role_type", type="string", example="Lead Actor"),
     *                      @OA\Property(property="gender", type="string", example="Male"),
     *                      @OA\Property(property="age_range", type="string", example="25-35"),
     *                      @OA\Property(property="dialogue_sample", type="string", example="Sample dialogue for this role")
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
        $user = Auth::user();
        
        // Only users with manage_movies permission can update movies
        if (!$user->hasPermission('manage_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('manage_movies')) {
                    return $this->sendError('You are not authorized to update movies.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to update movies.', [], 403);
            }
        }
        
        // Note: We're removing the Casting Director check since the Movie model doesn't have created_by field
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'director' => 'sometimes|string|max:255',
            'end_date' => 'sometimes|date',
            'genre' => 'sometimes|array',
            'genre.*' => 'string|max:50',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:active,inactive,upcoming',
            // Role fields validation
            'roles' => 'nullable|array',
            'roles.*.id' => 'nullable|exists:movie_roles,id',
            'roles.*.role_type' => 'nullable|string|max:50',
            'roles.*.gender' => 'nullable|string|max:20',
            'roles.*.age_range' => 'nullable|string|max:20',
            'roles.*.dialogue_sample' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        if ($request->has('title')) {
            $movie->title = $request->title;
        }
        
        if ($request->has('director')) {
            $movie->director = $request->director;
        }
        
        if ($request->has('end_date')) {
            $movie->end_date = $request->end_date;
        }
        
        if ($request->has('genre')) {
            $movie->genre = $request->genre;
        }
        
        if ($request->has('budget')) {
            $movie->budget = $request->budget;
        }
        
        if ($request->has('description')) {
            $movie->description = $request->description;
        }
        
        if ($request->has('status')) {
            $movie->status = $request->status;
        }
        
        if (!$movie->save()) {
            return $this->sendError('Error occurred while updating movie.');
        }
        
        // Process roles if provided
        if ($request->has('roles') && is_array($request->roles)) {
            $processedRoleIds = [];
            
            foreach ($request->roles as $roleData) {
                // Check if role data is not empty (excluding id field)
                $filteredRoleData = array_filter($roleData, function($value, $key) {
                    return $key !== 'id' && !is_null($value);
                }, ARRAY_FILTER_USE_BOTH);
                
                if (!empty($filteredRoleData)) {
                    if (isset($roleData['id'])) {
                        // Update existing role
                        $processedRoleIds[] = $roleData['id'];
                        $role = \App\Models\MovieRole::findOrFail($roleData['id']);
                        $role->update($roleData);
                    } else {
                        // Create new role
                        $role = new \App\Models\MovieRole($roleData);
                        $movie->roles()->save($role);
                    }
                } elseif (isset($roleData['id'])) {
                    // If only id is present and no other data, mark for cleanup
                    $processedRoleIds[] = $roleData['id'];
                }
            }
            
            // Delete roles that were not included in the update
            $movie->roles()->whereNotIn('id', $processedRoleIds)->delete();
        }
        
        return $this->sendResponse($movie->fresh()->load('roles'), 'Movie updated successfully.');
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/movies/{id}",
     *      operationId="deleteMovie",
     *      tags={"Unified Movies"},
     *      summary="Delete movie",
     *      description="Deletes a movie - only for users with manage_movies permission",
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
        $user = Auth::user();
        
        // Only users with manage_movies permission can delete movies
        if (!$user->hasPermission('manage_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('manage_movies')) {
                    return $this->sendError('You are not authorized to delete movies.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to delete movies.', [], 403);
            }
        }
        
        // Note: We're removing the Casting Director check since the Movie model doesn't have created_by field
        
        if (!$movie->delete()) {
            return $this->sendError('Error occurred while deleting movie.');
        }
        
        return $this->sendResponse([], 'Movie deleted successfully.');
    }
}