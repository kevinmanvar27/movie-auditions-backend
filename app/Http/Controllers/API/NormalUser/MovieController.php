<?php

namespace App\Http\Controllers\API\NormalUser;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Movie;
use App\Models\Role;

/**
 * @OA\Tag(
 *     name="Normal User Movies",
 *     description="API Endpoints for Movies Viewing by Normal Users"
 * )
 */
class MovieController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/normal-user/movies",
     *      operationId="getNormalUserMoviesList",
     *      tags={"Normal User Movies"},
     *      summary="Get list of movies",
     *      description="Returns list of movies for normal users",
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
        
        // Ensure the user has the Normal User role
        $normalUserRole = Role::where('name', 'Normal User')->first();
        if (!$normalUserRole || $user->role_id !== $normalUserRole->id) {
            return $this->sendError('You are not authorized to access this resource.', [], 403);
        }
        
        // Get filter values
        $genreFilter = $request->input('genre');
        $statusFilter = $request->input('status');
        
        // Build query with filters
        $query = Movie::with('roles');
        
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
     *      path="/api/v1/normal-user/movies/{id}",
     *      operationId="getNormalUserMovieById",
     *      tags={"Normal User Movies"},
     *      summary="Get movie information",
     *      description="Returns movie data for normal users",
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
        
        // Ensure the user has the Normal User role
        $normalUserRole = Role::where('name', 'Normal User')->first();
        if (!$normalUserRole || $user->role_id !== $normalUserRole->id) {
            return $this->sendError('You are not authorized to access this resource.', [], 403);
        }
        
        // Load roles relationship
        $movie->load('roles');
        
        return $this->sendResponse($movie, 'Movie retrieved successfully.');
    }
}