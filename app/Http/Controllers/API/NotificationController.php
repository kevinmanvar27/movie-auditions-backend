<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseAPIController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Notifications",
 *     description="API Endpoints for User Notifications"
 * )
 */
class NotificationController extends BaseAPIController
{
    protected $firebaseService;
    
    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    /**
     * @OA\Post(
     *      path="/api/v1/notifications/device-token",
     *      operationId="registerDeviceToken",
     *      tags={"Notifications"},
     *      summary="Register device token",
     *      description="Register device token for push notifications",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"device_token"},
     *              @OA\Property(property="device_token", type="string", example="fcm_device_token_here")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Device token registered successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object"),
     *              @OA\Property(property="message", type="string", example="Device token registered successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation Error."),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     *     )
     */
    public function registerDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }
        
        $user = $request->user();
        $user->device_token = $request->device_token;
        $user->save();
        
        return $this->sendResponse([], 'Device token registered successfully.');
    }
    
    /**
     * @OA\Get(
     *      path="/api/v1/notifications",
     *      operationId="getUserNotifications",
     *      tags={"Notifications"},
     *      summary="Get user notifications",
     *      description="Retrieve notifications for the authenticated user",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Notifications retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="data", type="array",
     *                      @OA\Items(ref="#/components/schemas/Notification")
     *                  ),
     *                  @OA\Property(property="first_page_url", type="string"),
     *                  @OA\Property(property="from", type="integer", example=1),
     *                  @OA\Property(property="last_page", type="integer", example=1),
     *                  @OA\Property(property="last_page_url", type="string"),
     *                  @OA\Property(property="links", type="array",
     *                      @OA\Items(type="object",
     *                          @OA\Property(property="url", type="string"),
     *                          @OA\Property(property="label", type="string"),
     *                          @OA\Property(property="active", type="boolean")
     *                      )
     *                  ),
     *                  @OA\Property(property="next_page_url", type="string"),
     *                  @OA\Property(property="path", type="string"),
     *                  @OA\Property(property="per_page", type="integer", example=20),
     *                  @OA\Property(property="prev_page_url", type="string"),
     *                  @OA\Property(property="to", type="integer", example=5),
     *                  @OA\Property(property="total", type="integer", example=5)
     *              ),
     *              @OA\Property(property="message", type="string", example="Notifications retrieved successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      )
     *     )
     */
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();
        
        // In a real implementation, you would filter notifications based on user criteria
        // For now, we'll return all notifications ordered by created date
        $notifications = Notification::where('status', 'sent')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return $this->sendResponse($notifications, 'Notifications retrieved successfully.');
    }
    
    /**
     * @OA\Post(
     *      path="/api/v1/notifications/{id}/read",
     *      operationId="markNotificationAsRead",
     *      tags={"Notifications"},
     *      summary="Mark notification as read",
     *      description="Mark a notification as read for the authenticated user",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Notification ID",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Notification marked as read",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object"),
     *              @OA\Property(property="message", type="string", example="Notification marked as read.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      )
     *     )
     */
    public function markAsRead($id)
    {
        // In a real implementation, you would have a user_notification pivot table
        // to track which notifications have been read by which users
        // For now, we'll just return success
        
        return $this->sendResponse([], 'Notification marked as read.');
    }
}