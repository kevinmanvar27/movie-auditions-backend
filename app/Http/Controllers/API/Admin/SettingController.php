<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SystemSetting;

/**
 * @OA\Tag(
 *     name="Admin Settings",
 *     description="API Endpoints for Admin Settings Management"
 * )
 */
class SettingController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/admin/settings",
     *      operationId="getAdminSettings",
     *      tags={"Admin Settings"},
     *      summary="Get system settings (Admin)",
     *      description="Returns all system settings for admin",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/SystemSetting"),
     *              @OA\Property(property="message", type="string", example="Settings retrieved successfully.")
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
    public function index()
    {
        $settings = SystemSetting::first();
        
        return $this->sendResponse($settings, 'Settings retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/settings",
     *      operationId="updateAdminSettings",
     *      tags={"Admin Settings"},
     *      summary="Update system settings (Admin)",
     *      description="Updates system settings",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="site_name", type="string", example="Movie Auditions"),
     *              @OA\Property(property="site_description", type="string", example="Platform for movie auditions"),
     *              @OA\Property(property="contact_email", type="string", format="email", example="admin@example.com"),
     *              @OA\Property(property="casting_director_payment_required", type="boolean", example=true),
     *              @OA\Property(property="audition_user_payment_required", type="boolean", example=true),
     *              @OA\Property(property="video_upload_limit_mb", type="integer", example=100)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/SystemSetting"),
     *              @OA\Property(property="message", type="string", example="Settings updated successfully.")
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
    public function update(Request $request)
    {
        $settings = SystemSetting::first();
        
        $validator = Validator::make($request->all(), [
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'contact_email' => 'nullable|email|max:255',
            'casting_director_payment_required' => 'nullable|boolean',
            'audition_user_payment_required' => 'nullable|boolean',
            'video_upload_limit_mb' => 'nullable|integer|min:1|max:10000',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $settings->update($request->only([
            'site_name',
            'site_description',
            'contact_email',
            'casting_director_payment_required',
            'audition_user_payment_required',
            'video_upload_limit_mb'
        ]));

        return $this->sendResponse($settings->fresh(), 'Settings updated successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/profile",
     *      operationId="getAdminProfile",
     *      tags={"Admin Settings"},
     *      summary="Get admin profile",
     *      description="Returns authenticated user's profile information",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/User"),
     *              @OA\Property(property="message", type="string", example="Profile retrieved successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     *     )
     */
    public function profile()
    {
        $user = auth()->user();
        $user->load('role');
        
        return $this->sendResponse($user, 'Profile retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/profile",
     *      operationId="updateAdminProfile",
     *      tags={"Admin Settings"},
     *      summary="Update admin profile",
     *      description="Updates authenticated user's profile information",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="phone", type="string", example="+1234567890"),
     *              @OA\Property(property="address", type="string", example="123 Main St")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/User"),
     *              @OA\Property(property="message", type="string", example="Profile updated successfully.")
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
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return $this->sendResponse($user->fresh(), 'Profile updated successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/profile/password",
     *      operationId="updateAdminProfilePassword",
     *      tags={"Admin Settings"},
     *      summary="Update admin password",
     *      description="Updates authenticated user's password",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"current_password","password","password_confirmation"},
     *              @OA\Property(property="current_password", type="string", format="password", example="currentpassword"),
     *              @OA\Property(property="password", type="string", format="password", example="newpassword"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Password updated successfully.")
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
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     */
    public function updateProfilePassword(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Check if current password is correct
        if (!\Hash::check($request->current_password, $user->password)) {
            return $this->sendError('Current password is incorrect.', [], 422);
        }

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return $this->sendResponse([], 'Password updated successfully.');
    }
}