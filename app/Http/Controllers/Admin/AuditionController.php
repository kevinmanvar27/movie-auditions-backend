<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all auditions from the database
        $auditions = \App\Models\Audition::all();
        return view('admin.auditions.index', compact('auditions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.auditions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate and store the audition
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email',
            'movie_id' => 'required|exists:movies,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:255',
            'audition_date' => 'required|date',
            'audition_time' => 'nullable',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        // Create the audition in the database
        $audition = \App\Models\Audition::create($validated);

        return redirect()->route('admin.auditions.index')->with('success', 'Audition created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the audition from the database
        $audition = \App\Models\Audition::findOrFail($id);
        return view('admin.auditions.show', compact('audition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Fetch the audition from the database
        $audition = \App\Models\Audition::findOrFail($id);
        return view('admin.auditions.edit', compact('audition'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate and update the audition
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email',
            'movie_id' => 'required|exists:movies,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|max:255',
            'audition_date' => 'required|date',
            'audition_time' => 'nullable',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
        ]);

        // Find and update the audition in the database
        $audition = \App\Models\Audition::findOrFail($id);
        $audition->update($validated);

        return redirect()->route('admin.auditions.index')->with('success', 'Audition updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Delete the audition from the database
            $audition = \App\Models\Audition::findOrFail($id);
            $audition->delete();

            return redirect()->route('admin.auditions.index')->with('success', 'Audition deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.auditions.index')->with('error', 'Failed to delete audition: ' . $e->getMessage());
        }
    }
}