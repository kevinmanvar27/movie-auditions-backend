<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Audition;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get dynamic statistics
        $totalMovies = Movie::count();
        $totalAuditions = Audition::count();
        $successfulAuditions = Audition::where('status', 'approved')->count();
        
        // Get recent activity (last 5 auditions)
        $recentActivity = Audition::with(['movie', 'user'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact('totalMovies', 'totalAuditions', 'successfulAuditions', 'recentActivity'));
    }
}