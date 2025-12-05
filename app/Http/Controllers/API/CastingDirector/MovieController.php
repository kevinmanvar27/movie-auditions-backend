<?php

namespace App\Http\Controllers\API\CastingDirector;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Movie;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Casting Director Movies",
 *     description="API Endpoints for Movies Management by Casting Directors"
 * )
 */
class MovieController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/casting-director/movies",
     *      operationId="getCastingDirectorMoviesList",
     *      tags={"Casting Director Movies"},
     *      summary="Get list of movies created by the casting director",
     *      description="Returns list of movies created by the authenticated casting director",
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
        
        // Ensure the user has the Casting Director role
        if (!$user->hasRole('Casting Director')) {
            return $this->sendError('You are not authorized to access this resource.', [], 403);
        }
        
        // Get filter values
        $genreFilter = $request->input('genre');
        $statusFilter = $request->input('status');
        
        // Build query with filters for movies created by this casting director
        $query = Movie::where('created_by', $user->id);
        
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
     *      path="/api/v1/casting-director/movies",
     *      operationId="storeCastingDirectorMovie",
     *      tags={"Casting Director Movies"},
     *      summary="Store a new movie",
     *      description="Creates a new movie by the authenticated casting director",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","director","release_date","genre","status"},
     *              @OA\Property(property="title", type="string", example="Inception"),
     *              @OA\Property(property="director", type="string", example="Christopher Nolan"),
     *              @OA\Property(property="producer", type="string", example="Emma Thomas"),
     *              @OA\Property(property="release_date", type="string", format="date", example="2025-12-31"),
     *              @OA\Property(property="genre", type="array",
     *                  @OA\Items(type="string", example="Sci-Fi")
     *              ),
     *              @OA\Property(property="budget", type="number", format="float", example=160000000.00),
     *              @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
     *              @OA\Property(property="status", type="string", example="active")
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
        
        // Ensure the user has the Casting Director role
        if (!$user->hasRole('Casting Director')) {
            return $this->sendError('You are not authorized to create movies.', [], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'producer' => 'nullable|string|max:255',
            'release_date' => 'required|date',
            'genre' => 'required|array',
            'genre.*' => 'string|max:50',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,upcoming'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        $movie = new Movie();
        $movie->title = $request->title;
        $movie->director = $request->director;
        $movie->producer = $request->producer;
        $movie->release_date = $request->release_date;
        $movie->genre = $request->genre;
        $movie->budget = $request->budget;
        $movie->description = $request->description;
        $movie->status = $request->status;
        $movie->created_by = $user->id; // Track who created the movie
        
        if (!$movie->save()) {
            return $this->sendError('Error occurred while creating movie.');
        }
        
        return $this->sendResponse($movie->fresh(), 'Movie created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/casting-director/movies/{id}",
     *      operationId="getCastingDirectorMovieById",
     *      tags={"Casting Director Movies"},
     *      summary="Get movie information",
     *      description="Returns movie data created by the authenticated casting director",
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
        
        // Ensure the user has the Casting Director role
        if (!$user->hasRole('Casting Director')) {
            return $this->sendError('You are not authorized to access this resource.', [], 403);
        }
        
        // Ensure the movie was created by this casting director
        if ($movie->created_by !== $user->id) {
            return $this->sendError('You are not authorized to view this movie.', [], 403);
        }
        
        // Load roles relationship
        $movie->load('roles');
        
        return $this->sendResponse($movie, 'Movie retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/casting-director/movies/{id}",
     *      operationId="updateCastingDirectorMovie",
     *      tags={"Casting Director Movies"},
     *      summary="Update existing movie",
     *      description="Updates a movie created by the authenticated casting director",
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
     *              @OA\Property(property="producer", type="string", example="Emma Thomas"),
     *              @OA\Property(property="release_date", type="string", format="date", example="2025-12-31"),
     *              @OA\Property(property="genre", type="array",
     *                  @OA\Items(type="string", example="Sci-Fi")
     *              ),
     *              @OA\Property(property="budget", type="number", format="float", example=160000000.00),
     *              @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
     *              @OA\Property(property="status", type="string", example="active")
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
        
        // Ensure the user has the Casting Director role
        if (!$user->hasRole('Casting Director')) {
            return $this->sendError('You are not authorized to update movies.', [], 403);
        }
        
        // Ensure the movie was created by this casting director
        if ($movie->created_by !== $user->id) {
            return $this->sendError('You are not authorized to update this movie.', [], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'director' => 'sometimes|string|max:255',
            'producer' => 'nullable|string|max:255',
            'release_date' => 'sometimes|date',
            'genre' => 'sometimes|array',
            'genre.*' => 'string|max:50',
            'budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:active,inactive,upcoming'
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
        
        if ($request->has('producer')) {
            $movie->producer = $request->producer;
        }
        
        if ($request->has('release_date')) {
            $movie->release_date = $request->release_date;
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
        
        return $this->sendResponse($movie->fresh(), 'Movie updated successfully.');
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/casting-director/movies/{id}",
     *      operationId="deleteCastingDirectorMovie",
     *      tags={"Casting Director Movies"},
     *      summary="Delete movie",
     *      description="Deletes a movie created by the authenticated casting director",
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
        
        // Ensure the user has the Casting Director role
        if (!$user->hasRole('Casting Director')) {
            return $this->sendError('You are not authorized to delete movies.', [], 403);
        }
        
        // Ensure the movie was created by this casting director
        if ($movie->created_by !== $user->id) {
            return $this->sendError('You are not authorized to delete this movie.', [], 403);
        }
        
        if (!$movie->delete()) {
            return $this->sendError('Error occurred while deleting movie.');
        }
        
        return $this->sendResponse([], 'Movie deleted successfully.');
    }
}