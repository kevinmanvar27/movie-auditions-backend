<?php

namespace App\Http\Controllers\API\Unified;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;

/**
 * @OA\Tag(
 *     name="Unified Users",
 *     description="API Endpoints for Users Management - Unified for all roles"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      operationId="getUsersList",
     *      tags={"Unified Users"},
     *      summary="Get list of users based on user role",
     *      description="Returns list of users based on the authenticated user's role and permissions",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="role",
     *          in="query",
     *          description="Filter by role",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          description="Filter by status",
     *          required=false,
     *          @OA\Schema(type="string", enum={"active", "inactive"})
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(ref="#/components/schemas/User")
     *              ),
     *              @OA\Property(property="message", type="string", example="Users retrieved successfully.")
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
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only users with manage_users permission can list all users
        if (!$user->hasPermission('manage_users')) {
            return $this->sendError('You are not authorized to view users list.', [], 403);
        }
        
        // Get filter values
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status');
        
        // Build query with filters
        $query = User::with('role');
        
        // Apply role filter if provided
        if ($roleFilter) {
            $query->whereHas('role', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }
        
        // Apply status filter if provided
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        
        // Fetch users based on filters
        $users = $query->get();
        
        return $this->sendResponse($users, 'Users retrieved successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      operationId="storeUser",
     *      tags={"Unified Users"},
     *      summary="Store a new user",
     *      description="Creates a new user - only for users with manage_users permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","role_id"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *              @OA\Property(property="role_id", type="integer", example=1),
     *              @OA\Property(property="phone", type="string", example="+1234567890"),
     *              @OA\Property(property="address", type="string", example="123 Main St"),
     *              @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/User"),
     *              @OA\Property(property="message", type="string", example="User created successfully.")
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
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Only users with manage_users permission can create users
        if (!$user->hasPermission('manage_users')) {
            return $this->sendError('You are not authorized to create users.', [], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status ?? 'active',
        ]);

        return $this->sendResponse($newUser->fresh(), 'User created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      operationId="getUserById",
     *      tags={"Unified Users"},
     *      summary="Get user information",
     *      description="Returns user data based on the authenticated user's permissions",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/User"),
     *              @OA\Property(property="message", type="string", example="User retrieved successfully.")
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
     *          response=404,
     *          description="User not found"
     *      )
     *     )
     */
    public function show(User $user)
    {
        $authUser = Auth::user();
        
        // Users can view their own profile or users with manage_users permission can view any user
        if ($authUser->id !== $user->id && !$authUser->hasPermission('manage_users')) {
            return $this->sendError('You are not authorized to view this user.', [], 403);
        }
        
        // Load role relationship
        $user->load('role');
        
        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      operationId="updateUser",
     *      tags={"Unified Users"},
     *      summary="Update existing user",
     *      description="Updates a user - users can update their own profile, users with manage_users permission can update any user",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","role_id"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="role_id", type="integer", example=1),
     *              @OA\Property(property="phone", type="string", example="+1234567890"),
     *              @OA\Property(property="address", type="string", example="123 Main St"),
     *              @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/User"),
     *              @OA\Property(property="message", type="string", example="User updated successfully.")
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
     *          response=404,
     *          description="User not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     */
    public function update(Request $request, User $user)
    {
        $authUser = Auth::user();
        
        // Users can update their own profile or users with manage_users permission can update any user
        if ($authUser->id !== $user->id && !$authUser->hasPermission('manage_users')) {
            return $this->sendError('You are not authorized to update this user.', [], 403);
        }
        
        // If user is updating their own profile, they cannot change their role
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ];
        
        // Only users with manage_users permission can change role_id
        if ($authUser->hasPermission('manage_users')) {
            $validationRules['role_id'] = 'required|exists:roles,id';
        }
        
        $validator = Validator::make($request->all(), $validationRules);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => $request->status ?? 'active',
        ];
        
        // Only users with manage_users permission can change role_id
        if ($authUser->hasPermission('manage_users') && $request->has('role_id')) {
            $updateData['role_id'] = $request->role_id;
        }
        
        $user->update($updateData);

        return $this->sendResponse($user->fresh(), 'User updated successfully.');
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      operationId="deleteUser",
     *      tags={"Unified Users"},
     *      summary="Delete user",
     *      description="Deletes a user - only for users with manage_users permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="User deleted successfully.")
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
     *          response=404,
     *          description="User not found"
     *      )
     *     )
     */
    public function destroy(User $user)
    {
        $authUser = Auth::user();
        
        // Only users with manage_users permission can delete users
        if (!$authUser->hasPermission('manage_users')) {
            return $this->sendError('You are not authorized to delete users.', [], 403);
        }
        
        // Prevent deleting the current user
        if ($user->id === $authUser->id) {
            return $this->sendError('You cannot delete yourself.');
        }

        $user->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }
}