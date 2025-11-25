@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Create Role</h1>
            <p class="text-theme-text-secondary">Create a new role with specific permissions</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-theme-text">Role Name</label>
                            <input type="text" name="name" id="name" class="input-field mt-1 block w-full" value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-theme-text">Description</label>
                            <textarea name="description" id="description" rows="3" class="input-field mt-1 block w-full">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-theme-text mb-2">Permissions</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border border-theme-border rounded-lg p-4">
                                    <h3 class="font-medium text-theme-text mb-3">User Management</h3>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="manage_users" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">Manage Users</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="manage_roles" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">Manage Roles</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="border border-theme-border rounded-lg p-4">
                                    <h3 class="font-medium text-theme-text mb-3">Content Management</h3>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="manage_movies" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">Manage Movies</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="manage_auditions" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">Manage Auditions</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="border border-theme-border rounded-lg p-4">
                                    <h3 class="font-medium text-theme-text mb-3">System</h3>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="view_reports" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">View Reports</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="manage_settings" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">Manage Settings</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="border border-theme-border rounded-lg p-4">
                                    <h3 class="font-medium text-theme-text mb-3">General</h3>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="view_movies" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">View Movies</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="permissions[]" value="apply_for_auditions" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <span class="ml-2 text-sm text-theme-text">Apply for Auditions</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('permissions')
                                <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-theme-background border-t border-theme-border flex justify-end space-x-3">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary px-4 py-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                        <span class="loading-text">Create Role</span>
                        <span class="loading-spinner hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection