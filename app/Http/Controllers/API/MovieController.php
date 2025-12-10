<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Movie;
use App\Models\Role;

/**
 * @OA\Tag(
 *     name="Movies",
 *     description="API Endpoints for Movies Management"
 * )
 */
class MovieController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/movies",
     *      operationId="getMoviesList",
     *      tags={"Movies"},
     *      summary="Get list of movies",
     *      description="Returns list of movies",
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
     *      )
     *     )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Check if user has permission to view movies
        if (!$user->hasPermission('view_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role;
                if (!$role || !$role->hasPermission('view_movies')) {
                    return $this->sendError('You are not authorized to view movies.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to view movies.', [], 403);
            }
        }
        
        // Get filter values
        $genreFilter = $request->input('genre');
        $statusFilter = $request->input('status');
        
        // Build query with filters
        // If user is admin, show all movies, otherwise show only movies created by the user
        if ($user->hasRole('Super Admin')) {
            $query = Movie::query();
        } else {
            $query = $user->movies();
        }
        
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
     * @OA\Get(
     *      path="/api/v1/movies/{id}",
     *      operationId="getMovieById",
     *      tags={"Movies"},
     *      summary="Get movie information",
     *      description="Returns movie data",
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
     *          response=404,
     *          description="Movie not found"
     *      )
     *     )
     */
    public function show(Request $request, Movie $movie)
    {
        $user = $request->user();
        
        // Check if user has permission to view movies
        if (!$user->hasPermission('view_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role;
                if (!$role || !$role->hasPermission('view_movies')) {
                    return $this->sendError('You are not authorized to view movies.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to view movies.', [], 403);
            }
        }
        
        // Check if the movie belongs to the user or if the user is admin
        if (!$user->hasRole('admin') && $movie->user_id != $user->id) {
            return $this->sendError('You are not authorized to view this movie.', [], 403);
        }
        
        // Load roles relationship
        $movie->load('roles');
        
        return $this->sendResponse($movie, 'Movie retrieved successfully.');
    }
}