<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\SendOTP;

class OTPController extends Controller
{
    /**
     * Send OTP for registration
     */
    public function sendRegistrationOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate 6-digit OTP
        $otpCode = rand(100000, 999999);
        
        // Store user data temporarily with OTP
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'otp_code' => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ];

        // Store in session or cache for temporary registration data
        $tempToken = Str::random(40);
        Cache::put('registration_' . $tempToken, $userData, 600); // 10 minutes

        // Send OTP to user's email
        $user = new User(['email' => $request->email]);
        $user->notify(new SendOTP($otpCode, 'registration'));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to your email',
            'temp_token' => $tempToken,
        ]);
    }

    /**
     * Verify registration OTP
     */
    public function verifyRegistrationOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temp_token' => 'required',
            'otp_code' => 'required|numeric|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Retrieve temporary registration data
        $tempData = Cache::get('registration_' . $request->temp_token);
        
        if (!$tempData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired temp token',
            ], 400);
        }

        // Check if OTP matches and hasn't expired
        if ($tempData['otp_code'] != $request->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code',
            ], 400);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($tempData['otp_expires_at']))) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired',
            ], 400);
        }

        // Create the user
        $user = User::create([
            'name' => $tempData['name'],
            'email' => $tempData['email'],
            'password' => $tempData['password'],
            'role_id' => $tempData['role_id'],
            'is_verified' => true,
            'email_verified_at' => Carbon::now(),
        ]);

        // Clear the temporary data
        Cache::forget('registration_' . $request->temp_token);

        // Generate token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration completed successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Send OTP for password reset
     */
    public function sendPasswordResetOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Generate 6-digit OTP
        $otpCode = rand(100000, 999999);
        
        // Save OTP to user record
        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP to user's email
        $user->notify(new SendOTP($otpCode, 'forgot_password'));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to your email',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Verify password reset OTP
     */
    public function verifyPasswordResetOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp_code' => 'required|numeric|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find the user
        $user = User::find($request->user_id);

        // Check if OTP matches and hasn't expired
        if ($user->otp_code != $request->otp_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code',
            ], 400);
        }

        if (Carbon::now()->greaterThan(Carbon::parse($user->otp_expires_at))) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired',
            ], 400);
        }

        // Generate a password reset token
        $token = Str::random(40);
        Cache::put('password_reset_' . $token, $user->id, 600); // 10 minutes

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'reset_token' => $token,
        ]);
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Retrieve user ID from cache
        $userId = Cache::get('password_reset_' . $request->reset_token);
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset token',
            ], 400);
        }

        // Find the user
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Clear the reset token
        Cache::forget('password_reset_' . $request->reset_token);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
        ]);
    }
}