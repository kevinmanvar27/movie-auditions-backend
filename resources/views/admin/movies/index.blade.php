@extends('layouts.admin')

@section('title', 'Movies Management')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-theme-text">Movies</h1>
                <p class="text-theme-text-secondary">Manage all movies in the system</p>
            </div>
            <div class="mt-4 sm:mt-0 ml-auto">
                <a href="{{ route('admin.movies.create') }}" class="btn btn-primary py-2 px-4" data-loading>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Movie
                </a>
            </div>
        </div>

        <!-- Movies Table -->
        <table class="min-w-full divide-y divide-theme-border datatable" id="moviesTable">
            <thead class="bg-theme-secondary">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Genre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Release Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-theme-background divide-y divide-theme-border">
                @forelse($movies as $movie)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $movie->id }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-theme-text max-w-xs truncate">{{ $movie->title }}</td>
                    <td class="px-6 py-4 text-sm text-theme-text max-w-xs truncate">{{ $movie->genre }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $movie->release_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($movie->status === 'active')
                            <span class="status-badge status-active">Active</span>
                        @elseif($movie->status === 'inactive')
                            <span class="status-badge status-inactive">Inactive</span>
                        @else
                            <span class="status-badge status-pending">Upcoming</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium table-actions">
                        <a href="{{ route('admin.movies.edit', $movie->id) }}" class="btn-edit mr-2">Edit</a>
                        <button type="button" class="btn-delete" onclick="confirmDelete({{ $movie->id }})">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-theme-text-secondary">
                        No movies found.
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
                <p class="mt-2 text-theme-text-secondary">Are you sure you want to delete this movie? This action cannot be undone.</p>
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
        function confirmDelete(movieId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = '/admin/movies/' + movieId;
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