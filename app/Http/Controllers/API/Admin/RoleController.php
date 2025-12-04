<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

/**
 * @OA\Tag(
 *     name="Admin Roles",
 *     description="API Endpoints for Admin Roles Management"
 * )
 */
class RoleController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/admin/roles",
     *      operationId="getAdminRolesList",
     *      tags={"Admin Roles"},
     *      summary="Get list of roles (Admin)",
     *      description="Returns list of all roles for admin",
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
        $roles = Role::all();
        
        return $this->sendResponse($roles, 'Roles retrieved successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/roles",
     *      operationId="storeAdminRole",
     *      tags={"Admin Roles"},
     *      summary="Store a new role (Admin)",
     *      description="Creates a new role",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","permissions"},
     *              @OA\Property(property="name", type="string", example="Casting Director"),
     *              @OA\Property(property="permissions", type="array",
     *                  @OA\Items(type="string", example="manage_movies")
     *              ),
     *              @OA\Property(property="description", type="string", example="Can manage movies and auditions")
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'required|array',
            'permissions.*' => 'string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'permissions' => json_encode($request->permissions),
            'description' => $request->description,
        ]);

        return $this->sendResponse($role->fresh(), 'Role created successfully.');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/roles/{id}",
     *      operationId="getAdminRoleById",
     *      tags={"Admin Roles"},
     *      summary="Get role information (Admin)",
     *      description="Returns role data",
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
        return $this->sendResponse($role, 'Role retrieved successfully.');
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/roles/{id}",
     *      operationId="updateAdminRole",
     *      tags={"Admin Roles"},
     *      summary="Update existing role (Admin)",
     *      description="Updates a role",
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
     *              required={"name","permissions"},
     *              @OA\Property(property="name", type="string", example="Casting Director"),
     *              @OA\Property(property="permissions", type="array",
     *                  @OA\Items(type="string", example="manage_movies")
     *              ),
     *              @OA\Property(property="description", type="string", example="Can manage movies and auditions")
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $role->update([
            'name' => $request->name,
            'permissions' => json_encode($request->permissions),
            'description' => $request->description,
        ]);

        return $this->sendResponse($role->fresh(), 'Role updated successfully.');
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/roles/{id}",
     *      operationId="deleteAdminRole",
     *      tags={"Admin Roles"},
     *      summary="Delete role (Admin)",
     *      description="Deletes a role",
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
        // Prevent deleting system roles
        if (in_array($role->name, ['Super Admin', 'Admin', 'Casting Director', 'Audition User'])) {
            return $this->sendError('Cannot delete system roles.');
        }

        $role->delete();

        return $this->sendResponse([], 'Role deleted successfully.');
    }
}