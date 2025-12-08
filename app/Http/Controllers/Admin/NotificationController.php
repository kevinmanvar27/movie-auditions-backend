<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Movie;
use App\Models\MovieRole;
use App\Models\Notification;
use App\Services\FirebaseNotificationService;

class NotificationController extends Controller
{
    protected $firebaseService;
    
    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    /**
     * Display the notification management page.
     */
    public function index()
    {
        // Get all sent notifications
        $notifications = Notification::orderBy('created_at', 'desc')->get();
        
        return view('admin.notifications.index', compact('notifications'));
    }
    
    /**
     * Display the notification sending page with advanced filtering.
     */
    public function create()
    {
        // Get all roles for filtering
        $roles = DB::table('roles')->get();
        
        // Get all movies for filtering
        $movies = Movie::all();
        
        return view('admin.notifications.create', compact('roles', 'movies'));
    }
    
    /**
     * Send a notification to filtered users.
     */
    public function send(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_roles' => 'nullable|array',
            'target_movies' => 'nullable|array',
            'gender' => 'nullable|string|in:male,female,other',
            'min_age' => 'nullable|integer|min:0|max:120',
            'max_age' => 'nullable|integer|min:0|max:120',
        ]);
        
        // Get filtered users based on criteria
        $users = $this->getFilteredUsers($validated);
        
        // Create a notification record
        $notification = Notification::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'filters_applied' => $validated,
            'recipient_count' => $users->count(),
            'status' => 'sent',
            'sent_at' => now(),
        ]);
        
        // Send notifications to users via Firebase
        $this->sendFirebaseNotifications($users, $validated['title'], $validated['message']);
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notification sent successfully to ' . $users->count() . ' users!');
    }
    
    /**
     * Send Firebase notifications to users
     */
    private function sendFirebaseNotifications($users, $title, $message)
    {
        // Get users who have device tokens
        $usersWithTokens = $users->filter(function ($user) {
            return !empty($user->device_token);
        });
        
        // Send notifications to users with device tokens
        foreach ($usersWithTokens as $user) {
            $this->firebaseService->sendToUser(
                $user->device_token,
                $title,
                $message,
                [
                    'user_id' => $user->id,
                    'notification_type' => 'admin_message'
                ]
            );
        }
        
        // For users without device tokens, we could send email notifications
        // This would be implemented in a real application
    }
    
    /**
     * Get filtered users based on the provided criteria.
     */
    private function getFilteredUsers($filters)
    {
        $query = User::query();
        
        // Filter by roles
        if (!empty($filters['target_roles'])) {
            $query->whereIn('role_id', $filters['target_roles']);
        }
        
        // Filter by movies (users who have applied to these movies)
        if (!empty($filters['target_movies'])) {
            $query->whereHas('auditions', function ($q) use ($filters) {
                $q->whereIn('movie_id', $filters['target_movies']);
            });
        }
        
        // Filter by gender
        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }
        
        // Filter by age range
        if (!empty($filters['min_age']) || !empty($filters['max_age'])) {
            // Calculate birth year ranges
            if (!empty($filters['min_age'])) {
                $maxBirthDate = now()->subYears($filters['min_age']);
                $query->where('date_of_birth', '<=', $maxBirthDate);
            }
            
            if (!empty($filters['max_age'])) {
                $minBirthDate = now()->subYears($filters['max_age']);
                $query->where('date_of_birth', '>=', $minBirthDate);
            }
        }
        
        return $query->get();
    }
    
    /**
     * Display details of a specific notification.
     */
    public function show(Notification $notification)
    {
        return view('admin.notifications.show', compact('notification'));
    }
}