<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'string'
        ]);

        // Define available permissions
        $availablePermissions = [
            'manage_users',
            'manage_roles',
            'manage_movies',
            'manage_auditions',
            'view_reports',
            'manage_settings',
            'view_movies',
            'apply_for_auditions'
        ];

        // Filter permissions to only include valid ones
        $permissions = array_intersect($request->permissions ?? [], $availablePermissions);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'permissions' => $permissions
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
            'permissions.*' => 'string'
        ]);

        // Define available permissions
        $availablePermissions = [
            'manage_users',
            'manage_roles',
            'manage_movies',
            'manage_auditions',
            'view_reports',
            'manage_settings',
            'view_movies',
            'apply_for_auditions'
        ];

        // Filter permissions to only include valid ones
        $permissions = array_intersect($request->permissions ?? [], $availablePermissions);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'permissions' => $permissions
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of roles that are assigned to users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete role because it is assigned to users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully!');
    }
}