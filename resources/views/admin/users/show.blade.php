@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">User Details</h1>
            <p class="text-theme-text-secondary">View detailed information about this user</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Name</label>
                        <p class="mt-1 text-theme-text">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Email</label>
                        <p class="mt-1 text-theme-text">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Role</label>
                        <p class="mt-1 text-theme-text">
                            @if($user->role && is_object($user->role))
                                {{ $user->role->name }}
                            @elseif($user->role_id)
                                {{ \App\Models\Role::find($user->role_id)?->name ?? 'Unknown Role' }}
                            @elseif(is_string($user->role))
                                {{ ucfirst($user->role) }}
                            @else
                                {{ ucfirst($user->attributes['role'] ?? 'User') }}
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Status</label>
                        <p class="mt-1">
                            @if($user->status === 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Registration Date</label>
                        <p class="mt-1 text-theme-text">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Back to Users</a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary py-2 px-4 mt-6">Edit User</a>
                </div>
            </div>
        </div>
    </div>
@endsection