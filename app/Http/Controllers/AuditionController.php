<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Audition;
use App\Models\Movie;
use Carbon\Carbon;

class AuditionController extends Controller
{
    /**
     * Create a new controller instance.
     * Only authenticated users can access audition features.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
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
        
        return view('auditions.index', compact('auditions'));
    }

    /**
     * Show the form for creating a new audition.
     */
    public function create()
    {
        // Get all active movies that haven't expired for the dropdown
        $movies = Movie::where('status', 'active')
                      ->where('end_date', '>=', Carbon::today())
                      ->get();
        
        return view('auditions.create', compact('movies'));
    }
    
    /**
     * Get movie roles for dependent dropdown
     */
    public function getMovieRoles(Movie $movie)
    {
        // Get open roles for the movie (available for audition)
        $roles = $movie->roles()->where('status', 'open')->get();
        
        return response()->json($roles);
    }
    
    /**
     * Store a newly created audition in storage.
     */
    public function store(Request $request)
    {
        // Check if payment is required for audition users
        $paymentRequired = is_audition_user_payment_required();
        
        // First validate the request data
        $rules = [
            'movie_id' => 'required|exists:movies,id',
            'role' => 'required|string|max:255',
            'applicant_name' => 'required|string|max:255',
            'uploaded_videos' => 'nullable|file|mimes:mp4,mov,avi,wmv,flv,webm',
        ];
        
        // Add payment verification fields only if payment is required
        if ($paymentRequired) {
            $rules['razorpay_payment_id'] = 'required|string';
            $rules['razorpay_order_id'] = 'required|string';
            $rules['razorpay_signature'] = 'required|string';
        }
        
        $validator = Validator::make($request->all(), $rules, [
            'uploaded_videos.mimes' => 'The video file must be a file of type: mp4, mov, avi, wmv, flv, webm.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Check if an audition already exists for the same user, movie, and role combination
        $existingAudition = Audition::where('user_id', Auth::id())
            ->where('movie_id', $request->movie_id)
            ->where('role', $request->role)
            ->first();
            
        if ($existingAudition) {
            return redirect()->back()->with('error', 'You have already submitted an audition for this movie-role combination.')->withInput();
        }

        // Handle file upload
        $videoUrl = null;
        $uploadError = null;
        if ($request->hasFile('uploaded_videos')) {
            $file = $request->file('uploaded_videos');
            if ($file && $file->isValid()) {
                try {
                    // Store the file in the 'audition_videos' directory using the public disk
                    $path = $file->store('audition_videos', 'public');
                    // Generate the full URL for web access using the asset helper
                    $videoUrl = asset('storage/' . $path);
                } catch (\Exception $e) {
                    // Log the error
                    Log::error('File upload error: ' . $e->getMessage());
                    // Set error message
                    $uploadError = "Failed to upload the video file. Please try again.";
                }
            } else {
                // Handle invalid file
                if ($file) {
                    $uploadError = "The video file is invalid or corrupted.";
                }
            }
        }
        
        // If there was an upload error, redirect back with error
        if ($uploadError) {
            return redirect()->back()->with('error', $uploadError)->withInput();
        }

        $audition = new Audition();
        $audition->user_id = Auth::id();
        $audition->movie_id = $request->movie_id;
        $audition->role = $request->role;
        $audition->applicant_name = $request->applicant_name;
        $audition->uploaded_videos = $videoUrl ? json_encode([$videoUrl]) : json_encode([]);
        $audition->old_video_backups = null; // Will be populated when videos are updated
        $audition->notes = $request->notes;
        $audition->status = 'pending';

        if (!$audition->save()) {
            return redirect()->back()->with('error', 'Failed to save audition. Please try again.')->withInput();
        }

        return redirect()->route('auditions.index')->with('success', 'Audition submitted successfully!');
    }

    /**
     * Display the specified audition.
     */
    public function show(Audition $audition)
    {
        // Ensure user can only view their own auditions
        if ($audition->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Load the movie relationship
        $audition->load('movie');
        
        // Check if request is AJAX
        if (request()->ajax()) {
            // Decode the uploaded_videos JSON and ensure it's an array
            $uploadedVideos = json_decode($audition->uploaded_videos, true);
            if (!is_array($uploadedVideos)) {
                $uploadedVideos = [];
            }
            $audition->uploaded_videos = $uploadedVideos;
            
            // Return JSON response for modal
            return response()->json([
                'success' => true,
                'data' => $audition
            ]);
        }
        
        return view('auditions.show', compact('audition'));
    }

    /**
     * Remove a video from an audition and store its URL in old_video_backups.
     */
    public function removeVideo(Request $request, Audition $audition)
    {
        // Ensure user can only modify their own auditions
        if ($audition->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'video_url' => 'required|string'
        ]);
        
        $videoUrl = $request->input('video_url');
        
        // Decode the current videos array
        $currentVideos = json_decode($audition->uploaded_videos, true) ?? [];
        $oldBackups = json_decode($audition->old_video_backups, true) ?? [];
        
        // Check if the video exists in the current videos
        if (!in_array($videoUrl, $currentVideos)) {
            return response()->json(['success' => false, 'message' => 'Video not found'], 404);
        }
        
        // Remove the video from current videos
        $currentVideos = array_diff($currentVideos, [$videoUrl]);
        
        // Add the video URL to old backups
        $oldBackups[] = $videoUrl;
        
        // Update the audition
        $audition->uploaded_videos = json_encode(array_values($currentVideos));
        $audition->old_video_backups = json_encode(array_values($oldBackups));
        $audition->save();
        
        return response()->json(['success' => true, 'message' => 'Video removed successfully']);
    }

    /**
     * Upload new videos to an audition.
     */
    public function uploadVideos(Request $request, Audition $audition)
    {
        // Ensure user can only modify their own auditions
        if ($audition->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'new_videos' => 'required|file|mimes:mp4,mov,avi,wmv,flv,webm',
        ]);
        
        if ($validator->fails()) {
            $errorMessage = implode(', ', $validator->errors()->all());
            return response()->json(['success' => false, 'message' => $errorMessage], 422);
        }
        
        // Decode the current videos array
        $currentVideos = json_decode($audition->uploaded_videos, true) ?? [];
        
        // Handle file upload
        if ($request->hasFile('new_videos')) {
            $file = $request->file('new_videos');
            if ($file && $file->isValid()) {
                try {
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
                } catch (\Exception $e) {
                    // Log the error
                    Log::error('File upload error: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Failed to upload the file: ' . $e->getMessage()], 500);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid file provided'], 400);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'No file provided'], 400);
        }
        
        // Update the audition
        $audition->uploaded_videos = json_encode(array_values($currentVideos));
        
        if (!$audition->save()) {
            return response()->json(['success' => false, 'message' => 'Failed to save audition data'], 500);
        }
        
        return response()->json(['success' => true, 'message' => 'Video uploaded successfully']);
    }
}