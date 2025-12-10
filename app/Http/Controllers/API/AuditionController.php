<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Audition;
use App\Models\Movie;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Auditions",
 *     description="API Endpoints for Auditions Management"
 * )
 */
class AuditionController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/auditions",
     *      operationId="getAuditionsList",
     *      tags={"Auditions"},
     *      summary="Get list of auditions",
     *      description="Returns list of auditions for the authenticated user",
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
        
        // Check if user has permission to view auditions
        if (!$user->hasPermission('view_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('view_movies')) {
                    return $this->sendError('You are not authorized to view auditions.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to view auditions.', [], 403);
            }
        }
        
        // Get user's auditions with movie information
        $query = Audition::with('movie')->where('user_id', $user->id);
        
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
     * @OA\Post(
     *      path="/api/v1/auditions",
     *      operationId="storeAudition",
     *      tags={"Auditions"},
     *      summary="Store a new audition",
     *      description="Creates a new audition for the authenticated user",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="movie_id", type="integer", example=1),
     *                  @OA\Property(property="role", type="string", example="Lead Actor"),
     *                  @OA\Property(property="applicant_name", type="string", example="John Doe"),
     *                  @OA\Property(property="notes", type="string", example="Experienced actor with 5 years in the industry"),
     *                  @OA\Property(property="uploaded_videos", type="string", format="binary")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Audition"),
     *              @OA\Property(property="message", type="string", example="Audition created successfully.")
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
        
        // Check if user has permission to create auditions
        if (!$user->hasPermission('view_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('view_movies')) {
                    return $this->sendError('You are not authorized to create auditions.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to create auditions.', [], 403);
            }
        }
        
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required|exists:movies,id',
            'role' => 'required|string|max:255',
            'applicant_name' => 'required|string|max:255',
            'uploaded_videos' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,webm',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $audition = new Audition();
        $audition->user_id = Auth::id();
        $audition->movie_id = $request->movie_id;
        $audition->role = $request->role;
        $audition->applicant_name = $request->applicant_name;
        $audition->uploaded_videos = json_encode([]); // Will be updated when file is processed
        $audition->old_video_backups = null;
        $audition->notes = $request->notes;
        $audition->status = 'pending';

        if (!$audition->save()) {
            return $this->sendError('Error occurred while creating audition.');
        }

        return $this->sendResponse($audition->fresh(), 'Audition created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/auditions/{id}",
     *      operationId="getAuditionById",
     *      tags={"Auditions"},
     *      summary="Get audition information",
     *      description="Returns audition data for the authenticated user",
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
        
        // Check if user has permission to view auditions
        if (!$user->hasPermission('view_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('view_movies')) {
                    return $this->sendError('You are not authorized to view auditions.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to view auditions.', [], 403);
            }
        }
        
        // Ensure user can only view their own auditions
        if ($audition->user_id !== Auth::id()) {
            return $this->sendError('You are not authorized to view this audition.', [], 403);
        }
        
        // Load the movie relationship
        $audition->load('movie');
        
        return $this->sendResponse($audition, 'Audition retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/auditions/{id}",
     *      operationId="updateAudition",
     *      tags={"Auditions"},
     *      summary="Update existing audition",
     *      description="Updates an audition for the authenticated user",
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
     *              @OA\Property(property="role", type="string", example="Lead Actor"),
     *              @OA\Property(property="applicant_name", type="string", example="John Doe"),
     *              @OA\Property(property="notes", type="string", example="Updated notes")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Audition"),
     *              @OA\Property(property="message", type="string", example="Audition updated successfully.")
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
        
        // Check if user has permission to update auditions
        if (!$user->hasPermission('view_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('view_movies')) {
                    return $this->sendError('You are not authorized to update auditions.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to update auditions.', [], 403);
            }
        }
        
        // Ensure user can only update their own auditions
        if ($audition->user_id !== Auth::id()) {
            return $this->sendError('You are not authorized to update this audition.', [], 403);
        }
        
        // Prepare validation rules
        $rules = [
            'role' => 'sometimes|string|max:255',
            'applicant_name' => 'sometimes|string|max:255',
            'notes' => 'nullable|string',
            'new_videos' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,webm',
            'remove_video_url' => 'nullable|string'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        // Handle video removal if requested
        if ($request->has('remove_video_url')) {
            $videoUrl = $request->input('remove_video_url');
            
            // Decode the current videos array
            $currentVideos = json_decode($audition->uploaded_videos, true) ?? [];
            $oldBackups = json_decode($audition->old_video_backups, true) ?? [];
            
            // Check if the video exists in the current videos
            if (in_array($videoUrl, $currentVideos)) {
                // Remove the video from current videos
                $currentVideos = array_diff($currentVideos, [$videoUrl]);
                
                // Add the video URL to old backups
                $oldBackups[] = $videoUrl;
                
                // Update the audition
                $audition->uploaded_videos = json_encode(array_values($currentVideos));
                $audition->old_video_backups = json_encode(array_values($oldBackups));
            }
        }
        
        // Handle new video upload
        if ($request->hasFile('new_videos')) {
            $file = $request->file('new_videos');
            if ($file && $file->isValid()) {
                try {
                    // Decode the current videos array
                    $currentVideos = json_decode($audition->uploaded_videos, true) ?? [];
                    
                    // Move current video to backups if exists
                    if (!empty($currentVideos)) {
                        $oldBackups = json_decode($audition->old_video_backups, true) ?? [];
                        $oldBackups = array_merge($oldBackups, $currentVideos);
                        $audition->old_video_backups = json_encode(array_values($oldBackups));
                    }
                    
                    // Store the new file in the 'audition_videos' directory using the public disk
                    $path = $file->store('audition_videos', 'public');
                    // Generate the full URL for web access using the asset helper
                    $videoUrl = asset('storage/' . $path);
                    // Set as the current video
                    $currentVideos = [$videoUrl];
                    
                    // Update the audition videos
                    $audition->uploaded_videos = json_encode(array_values($currentVideos));
                } catch (\Exception $e) {
                    // Log the error
                    Log::error('File upload error: ' . $e->getMessage());
                    return $this->sendError('Failed to upload the file: ' . $e->getMessage(), [], 500);
                }
            }
        }
        
        // Update text fields if provided
        if ($request->has('role')) {
            $audition->role = $request->role;
        }
        
        if ($request->has('applicant_name')) {
            $audition->applicant_name = $request->applicant_name;
        }
        
        if ($request->has('notes')) {
            $audition->notes = $request->notes;
        }

        if (!$audition->save()) {
            return $this->sendError('Error occurred while updating audition.');
        }

        return $this->sendResponse($audition->fresh(), 'Audition updated successfully.');
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/auditions/{id}",
     *      operationId="deleteAudition",
     *      tags={"Auditions"},
     *      summary="Delete audition",
     *      description="Deletes an audition for the authenticated user",
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
     *              @OA\Property(property="message", type="string", example="Audition deleted successfully.")
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
    public function destroy(Audition $audition)
    {
        $user = Auth::user();
        
        // Check if user has permission to delete auditions
        if (!$user->hasPermission('view_movies')) {
            // Alternative check for users with role_id
            if ($user->role_id) {
                $role = $user->role()->first();
                if (!$role || !$role->hasPermission('view_movies')) {
                    return $this->sendError('You are not authorized to delete auditions.', [], 403);
                }
            } else {
                return $this->sendError('You are not authorized to delete auditions.', [], 403);
            }
        }
        
        // Ensure user can only delete their own auditions
        if ($audition->user_id !== Auth::id()) {
            return $this->sendError('You are not authorized to delete this audition.', [], 403);
        }
        
        if (!$audition->delete()) {
            return $this->sendError('Error occurred while deleting audition.');
        }
        
        return $this->sendResponse([], 'Audition deleted successfully.');
    }
}