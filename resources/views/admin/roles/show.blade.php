@extends('layouts.admin')

@section('title', 'Role Details')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Role Details</h1>
            <p class="text-theme-text-secondary">View detailed information about this role</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Name</label>
                        <p class="mt-1 text-theme-text">{{ $role->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Description</label>
                        <p class="mt-1 text-theme-text">{{ $role->description ?? 'No description provided.' }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-theme-text">Permissions</label>
                        <div class="mt-2">
                            @if(!empty($role->permissions))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($role->permissions as $permission)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-theme-primary bg-opacity-10 text-center" style="color: var(--tw-ring-offset-color); padding: 5px 5px; margin-right: 5px;">
                                            {{ ucfirst(str_replace('_', ' ', $permission)) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-theme-text-secondary">No permissions assigned to this role.</p>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Assigned Users</label>
                        <p class="mt-1 text-theme-text">{{ $role->users()->count() }} users</p>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Back to Roles</a>
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary py-2 px-4 mt-6">Edit Role</a>
                </div>
            </div>
        </div>
    </div>
@endsection