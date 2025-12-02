<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
// use App\Models\Audition;  // Removed audition import

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get dynamic statistics
        $totalMovies = Movie::count();
        $totalAuditions = 0;  // Removed audition count
        $successfulAuditions = 0;  // Removed successful audition count
        
        // Get recent activity (empty since auditions are removed)
        $recentActivity = collect();
        
        return view('admin.dashboard', compact('totalMovies', 'totalAuditions', 'successfulAuditions', 'recentActivity'));
    }
}