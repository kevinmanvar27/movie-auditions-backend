<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        // Get PHP's upload limit in MB
        $phpUploadLimitMb = (int)(ini_get('upload_max_filesize'));
        
        // Validate and update settings
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'admin_email' => 'required|email',
            'video_upload_limit' => 'nullable|integer|min:1|max:' . ($phpUploadLimitMb * 1024), // Convert MB to KB
        ], [
            'video_upload_limit.max' => 'The video upload limit cannot exceed the server limit of ' . $phpUploadLimitMb . 'MB.'
        ]);

        // Save settings to database
        foreach ($validated as $key => $value) {
            // Convert video upload limit from MB to KB for storage
            if ($key === 'video_upload_limit' && $value !== null) {
                $value = $value * 1024; // Convert MB to KB
            }
            
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully!');
    }

    /**
     * Display the user profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validate and update user profile
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'mobile_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo) {
                Storage::delete($user->profile_photo);
            }
            
            // Store new profile photo
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $validated['profile_photo'] = $path;
        } else {
            // Keep existing photo if no new one uploaded
            unset($validated['profile_photo']);
        }

        // Update the user
        $user->update($validated);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the user's password from profile.
     */
    public function updateProfilePassword(Request $request)
    {
        $user = Auth::user();
        
        // Validate password update
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update the password
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully!');
    }
}