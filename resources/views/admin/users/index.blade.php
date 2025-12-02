@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-theme-text">Users</h1>
                <p class="text-theme-text-secondary">Manage all users in the system</p>
            </div>
            <div class="mt-4 sm:mt-0 ml-auto">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary py-2 px-4" data-loading>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New User
                </a>
            </div>
        </div>

        <!-- Users Table -->
        <table class="min-w-full divide-y divide-theme-border datatable" id="usersTable">
            <thead class="bg-theme-secondary">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Registration Date</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-theme-background divide-y divide-theme-border">
                @forelse($users as $key => $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $key + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-theme-text">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">
                        @if($user->role && is_object($user->role))
                            {{ $user->role->name }}
                        @elseif($user->role_id)
                            {{ \App\Models\Role::find($user->role_id)?->name ?? 'Unknown Role' }}
                        @elseif(is_string($user->role))
                            {{ ucfirst($user->role) }}
                        @else
                            {{ ucfirst($user->attributes['role'] ?? 'User') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($user->status === 'active')
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $user->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium table-actions">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn-view mr-2">View</a>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit mr-2">Edit</a>
                        <button type="button" class="btn-delete" onclick="confirmDelete({{ $user->id }})">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-theme-text-secondary">
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-theme-surface rounded-lg shadow-xl border border-theme-border max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-lg font-medium text-theme-text">Confirm Deletion</h3>
                <p class="mt-2 text-theme-text-secondary">Are you sure you want to delete this user? This action cannot be undone.</p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = '/admin/users/' + userId;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('flex');
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Handle form submission
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForm = document.getElementById('deleteForm');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    // Form will submit normally, which will cause page refresh
                    // The server will redirect back to the index page with success message
                });
            }
        });

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection