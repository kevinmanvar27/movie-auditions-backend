<?php

namespace App\Http\Controllers\API\Unified;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

/**
 * @OA\Tag(
 *     name="Unified Roles",
 *     description="API Endpoints for Roles Management - Unified for all roles"
 * )
 */
class RoleController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/roles",
     *      operationId="getRolesList",
     *      tags={"Unified Roles"},
     *      summary="Get list of roles",
     *      description="Returns list of all roles - only for users with manage_roles permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(ref="#/components/schemas/Role")
     *              ),
     *              @OA\Property(property="message", type="string", example="Roles retrieved successfully.")
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
        $user = Auth::user();
        
        // Only users with manage_roles permission can list roles
        if (!$user->hasPermission('manage_roles')) {
            return $this->sendError('You are not authorized to view roles list.', [], 403);
        }
        
        $roles = Role::all();
        
        return $this->sendResponse($roles, 'Roles retrieved successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/v1/roles",
     *      operationId="storeRole",
     *      tags={"Unified Roles"},
     *      summary="Store a new role",
     *      description="Creates a new role - only for users with manage_roles permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","description","permissions"},
     *              @OA\Property(property="name", type="string", example="Casting Director"),
     *              @OA\Property(property="description", type="string", example="Can create movies and roles"),
     *              @OA\Property(property="permissions", type="array", 
     *                  @OA\Items(type="string", example="manage_movies")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Role"),
     *              @OA\Property(property="message", type="string", example="Role created successfully.")
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
        
        // Only users with manage_roles permission can create roles
        if (!$user->hasPermission('manage_roles')) {
            return $this->sendError('You are not authorized to create roles.', [], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        // Define available permissions
        $availablePermissions = [
            'manage_users',
            'manage_roles',
            'manage_movies',
            'manage_auditions',
            'view_reports',
            'manage_settings',
            'view_movies',
            'view_dashboard'
        ];
        
        // Filter permissions to only include valid ones
        $permissions = array_intersect($request->permissions, $availablePermissions);
        
        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => $permissions
        ]);
        
        return $this->sendResponse($role, 'Role created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/roles/{id}",
     *      operationId="getRoleById",
     *      tags={"Unified Roles"},
     *      summary="Get role information",
     *      description="Returns role data - only for users with manage_roles permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Role"),
     *              @OA\Property(property="message", type="string", example="Role retrieved successfully.")
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
     *          description="Role not found"
     *      )
     *     )
     */
    public function show(Role $role)
    {
        $user = Auth::user();
        
        // Only users with manage_roles permission can view roles
        if (!$user->hasPermission('manage_roles')) {
            return $this->sendError('You are not authorized to view roles.', [], 403);
        }
        
        return $this->sendResponse($role, 'Role retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/roles/{id}",
     *      operationId="updateRole",
     *      tags={"Unified Roles"},
     *      summary="Update existing role",
     *      description="Updates a role - only for users with manage_roles permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","description","permissions"},
     *              @OA\Property(property="name", type="string", example="Casting Director"),
     *              @OA\Property(property="description", type="string", example="Can create movies and roles"),
     *              @OA\Property(property="permissions", type="array", 
     *                  @OA\Items(type="string", example="manage_movies")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/Role"),
     *              @OA\Property(property="message", type="string", example="Role updated successfully.")
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
     *          description="Role not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     */
    public function update(Request $request, Role $role)
    {
        $user = Auth::user();
        
        // Only users with manage_roles permission can update roles
        if (!$user->hasPermission('manage_roles')) {
            return $this->sendError('You are not authorized to update roles.', [], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        // Define available permissions
        $availablePermissions = [
            'manage_users',
            'manage_roles',
            'manage_movies',
            'manage_auditions',
            'view_reports',
            'manage_settings',
            'view_movies',
            'view_dashboard'
        ];
        
        // Filter permissions to only include valid ones
        $permissions = array_intersect($request->permissions, $availablePermissions);
        
        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'permissions' => $permissions
        ]);
        
        return $this->sendResponse($role->fresh(), 'Role updated successfully.');
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/roles/{id}",
     *      operationId="deleteRole",
     *      tags={"Unified Roles"},
     *      summary="Delete role",
     *      description="Deletes a role - only for users with manage_roles permission",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Role deleted successfully.")
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
     *          description="Role not found"
     *      )
     *     )
     */
    public function destroy(Role $role)
    {
        $user = Auth::user();
        
        // Only users with manage_roles permission can delete roles
        if (!$user->hasPermission('manage_roles')) {
            return $this->sendError('You are not authorized to delete roles.', [], 403);
        }
        
        // Prevent deletion of roles that are assigned to users
        if ($role->users()->count() > 0) {
            return $this->sendError('Cannot delete role because it is assigned to users.');
        }
        
        $role->delete();
        
        return $this->sendResponse([], 'Role deleted successfully.');
    }
}