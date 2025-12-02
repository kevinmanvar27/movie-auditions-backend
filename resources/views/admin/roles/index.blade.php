@extends('layouts.admin')

@section('title', 'Roles Management')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-theme-text">Roles</h1>
                <p class="text-theme-text-secondary">Manage all roles in the system</p>
            </div>
            <div class="mt-4 sm:mt-0 ml-auto">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary py-2 px-4" data-loading>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Role
                </a>
            </div>
        </div>

        <!-- Roles Table -->
        <div class="bg-theme-surface rounded-lg shadow">
            <table class="min-w-full divide-y divide-theme-border datatable p-2" id="rolesTable">
                <thead class="bg-theme-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Permissions</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Users</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-theme-background divide-y divide-theme-border">
                    @forelse($roles as $role)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $role->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-theme-text">{{ $role->name }}</td>
                        <td class="px-6 py-4 text-sm text-theme-text-secondary max-w-xs truncate">{{ $role->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-theme-text">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-theme-primary bg-opacity-10" style="color: var(--tw-ring-offset-color); padding: 0.50rem 0.50rem;">
                                {{ count($role->permissions ?? []) }} permissions
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $role->users()->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium table-actions flex items-center space-x-2">
                            <a href="{{ route('admin.roles.show', $role) }}" class="btn-view mr-2">View</a>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn-edit mr-2">Edit</a>
                            @if($role->users()->count() == 0)
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-theme-text-secondary">
                            No roles found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection