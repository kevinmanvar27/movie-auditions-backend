<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemSetting;
use App\Models\User;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get all system settings
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
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
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'admin_email' => 'nullable|email',
            'video_upload_limit' => 'nullable|integer|min:1|max:' . ($phpUploadLimitMb * 1024), // Convert MB to KB
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Logo validation
            // Payment settings
            'casting_director_amount' => 'nullable|numeric|min:0',
            'casting_director_percentage' => 'nullable|numeric|min:0|max:100',
            'audition_user_amount' => 'nullable|numeric|min:0',
            // Payment requirement settings
            'casting_director_payment_required' => 'nullable|in:0,1',
            'audition_user_payment_required' => 'nullable|in:0,1',
            // Razorpay keys
            'razorpay_key_id' => 'nullable|string',
            'razorpay_key_secret' => 'nullable|string',
            // Firebase notification settings
            'firebase_api_key' => 'nullable|string',
            'firebase_auth_domain' => 'nullable|string',
            'firebase_project_id' => 'nullable|string',
            'firebase_storage_bucket' => 'nullable|string',
            'firebase_messaging_sender_id' => 'nullable|string',
            'firebase_app_id' => 'nullable|string',
            'firebase_measurement_id' => 'nullable|string',
        ], [
            'video_upload_limit.max' => 'The video upload limit cannot exceed the server limit of ' . $phpUploadLimitMb . 'MB.',
            'logo.image' => 'The logo must be an image.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'The logo may not be greater than 2MB.',
            // Payment validation messages
            'casting_director_amount.numeric' => 'The casting director amount must be a valid number.',
            'casting_director_amount.min' => 'The casting director amount must be zero or greater.',
            'casting_director_percentage.numeric' => 'The casting director percentage must be a valid number.',
            'casting_director_percentage.min' => 'The casting director percentage must be zero or greater.',
            'casting_director_percentage.max' => 'The casting director percentage cannot exceed 100%.',
            'audition_user_amount.numeric' => 'The audition user amount must be a valid number.',
            'audition_user_amount.min' => 'The audition user amount must be zero or greater.',
            // Payment requirement validation messages
            'casting_director_payment_required.in' => 'The casting director payment requirement must be either 0 or 1.',
            'audition_user_payment_required.in' => 'The audition user payment requirement must be either 0 or 1.',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            $oldLogoPath = SystemSetting::where('key', 'logo_path')->value('value');
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }
            
            // Store new logo
            $logoPath = $request->file('logo')->store('logos', 'public');
            
            // Save logo path to settings
            SystemSetting::updateOrCreate(
                ['key' => 'logo_path'],
                ['value' => $logoPath]
            );
        }
        
        // Save other settings to database
        foreach ($validated as $key => $value) {
            // Skip logo as it's handled separately
            if ($key === 'logo') {
                continue;
            }
            
            // Convert video upload limit from MB to KB for storage
            if ($key === 'video_upload_limit' && $value !== null) {
                $value = $value * 1024; // Convert MB to KB
            }
            
            SystemSetting::updateOrCreate(
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

        // Update the user using the update method
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