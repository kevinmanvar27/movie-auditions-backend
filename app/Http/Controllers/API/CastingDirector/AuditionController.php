<?php

namespace App\Http\Controllers\API\CastingDirector;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Audition;
use App\Models\Movie;

/**
 * @OA\Tag(
 *     name="Casting Director Auditions",
 *     description="API Endpoints for Auditions Management by Casting Directors"
 * )
 */
class AuditionController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/casting-director/auditions",
     *      operationId="getCastingDirectorAuditionsList",
     *      tags={"Casting Director Auditions"},
     *      summary="Get list of auditions for movies created by the casting director",
     *      description="Returns list of auditions for movies created by the authenticated casting director",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="movie_title",
     *          in="query",
     *          description="Filter by movie title",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="role",
     *          in="query",
     *          description="Filter by role",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Filter by status",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Sort by column",
     *          required=false,
     *          @OA\Schema(type="string", enum={"created_at", "movie_title", "role", "status"})
     *      ),
     *      @OA\Parameter(
     *          name="sort_order",
     *          in="query",
     *          description="Sort order",
     *          required=false,
     *          @OA\Schema(type="string", enum={"asc", "desc"})
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer"),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(ref="#/components/schemas/Audition")
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string"),
     *                  @OA\Property(property="from", type="integer"),
     *                  @OA\Property(property="last_page", type="integer"),
     *                  @OA\Property(property="last_page_url", type="string"),
     *                  @OA\Property(property="links", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="url", type="string"),
     *                          @OA\Property(property="label", type="string"),
     *                          @OA\Property(property="active", type="boolean")
     *                      )
     *                  ),
     *                  @OA\Property(property="next_page_url", type="string"),
     *                  @OA\Property(property="path", type="string"),
     *                  @OA\Property(property="per_page", type="integer"),
     *                  @OA\Property(property="prev_page_url", type="string"),
     *                  @OA\Property(property="to", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              ),
     *              @OA\Property(property="message", type="string", example="Auditions retrieved successfully.")
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
        
        // Get auditions for movies created by this casting director
        $query = Audition::with('movie')
            ->whereHas('movie', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        
        // Apply filters if provided
        if ($request->has('movie_title') && !empty($request->movie_title)) {
            $query->whereHas('movie', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->movie_title . '%');
            });
        }
        
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', 'like', '%' . $request->role . '%');
        }
        
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort order
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        
        // Validate sort by
        $validSortColumns = ['created_at', 'movie_title', 'role', 'status'];
        if (!in_array($sortBy, $validSortColumns)) {
            $sortBy = 'created_at';
            $sortOrder = 'desc';
        }
        
        switch ($sortBy) {
            case 'movie_title':
                $query->join('movies', 'auditions.movie_id', '=', 'movies.id')
                      ->orderBy('movies.title', $sortOrder);
                break;
            case 'role':
                $query->orderBy('role', $sortOrder);
                break;
            case 'status':
                $query->orderBy('status', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }
        
        $auditions = $query->select('auditions.*')->paginate(9);
        
        return $this->sendResponse($auditions, 'Auditions retrieved successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/casting-director/auditions/{id}",
     *      operationId="getCastingDirectorAuditionById",
     *      tags={"Casting Director Auditions"},
     *      summary="Get audition information",
     *      description="Returns audition data for movies created by the authenticated casting director",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Audition id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Audition"),
     *              @OA\Property(property="message", type="string", example="Audition retrieved successfully.")
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
     *          description="Audition not found"
     *      )
     *     )
     */
    public function show(Audition $audition)
    {
        $user = Auth::user();
        
        // Ensure the user has the Casting Director role
        if (!$user->hasRole('Casting Director')) {
            return $this->sendError('You are not authorized to access this resource.', [], 403);
        }
        
        // Ensure the audition is for a movie created by this casting director
        if ($audition->movie->created_by !== $user->id) {
            return $this->sendError('You are not authorized to view this audition.', [], 403);
        }
        
        // Load the movie relationship
        $audition->load('movie');
        
        return $this->sendResponse($audition, 'Audition retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/casting-director/auditions/{id}",
     *      operationId="updateCastingDirectorAudition",
     *      tags={"Casting Director Auditions"},
     *      summary="Update audition status",
     *      description="Updates the status of an audition for a movie created by the authenticated casting director",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Audition id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"status"},
     *              @OA\Property(property="status", type="string", example="approved", enum={"pending", "approved", "rejected"})
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Audition"),
     *              @OA\Property(property="message", type="string", example="Audition status updated successfully.")
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
     *          description="Audition not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     */
    public function update(Request $request, Audition $audition)
    {
        $user = Auth::user();
        
        // Ensure the user has the Casting Director role
        if (!$user->hasRole('Casting Director')) {
            return $this->sendError('You are not authorized to update auditions.', [], 403);
        }
        
        // Ensure the audition is for a movie created by this casting director
        if ($audition->movie->created_by !== $user->id) {
            return $this->sendError('You are not authorized to update this audition.', [], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,approved,rejected'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        $audition->status = $request->status;
        
        if (!$audition->save()) {
            return $this->sendError('Error occurred while updating audition status.');
        }
        
        return $this->sendResponse($audition->fresh(), 'Audition status updated successfully.');
    }
}