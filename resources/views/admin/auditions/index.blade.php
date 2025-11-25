@extends('layouts.admin')

@section('title', 'Auditions Management')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-theme-text">Auditions</h1>
                <p class="text-theme-text-secondary">Manage all auditions in the system</p>
            </div>
            <div class="mt-4 sm:mt-0 ml-auto">
                <a href="{{ route('admin.auditions.create') }}" class="btn btn-primary py-2 px-4" data-loading>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Audition
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-theme-surface rounded-lg shadow border border-theme-border p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="movie_filter" class="block text-sm font-medium text-theme-text">Filter by Movie</label>
                    <select id="movie_filter" class="input-field mt-1 block w-full">
                        <option value="">All Movies</option>
                        @foreach($movies as $movie)
                            <option value="{{ $movie->id }}" {{ $movieFilter == $movie->id ? 'selected' : '' }}>{{ $movie->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status_filter" class="block text-sm font-medium text-theme-text">Filter by Status</label>
                    <select id="status_filter" class="input-field mt-1 block w-full">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ $statusFilter == $key ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end space-x-2">
                    <button id="applyFilters" class="btn btn-primary w-full py-2 px-4" data-loading>
                        <span class="loading-text">Apply Filters</span>
                        <span class="loading-spinner hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                    
                    <button id="clearFilters" class="btn btn-secondary w-full py-2 px-4">
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Auditions Table -->
        <table class="min-w-full divide-y divide-theme-border datatable" id="auditionsTable">
            <thead class="bg-theme-secondary">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Applicant Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Movie</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Audition Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-theme-background divide-y divide-theme-border">
                @forelse($auditions as $key => $audition)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $key + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-theme-text">{{ $audition->applicant_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $audition->movie->title ?? 'Unknown Movie' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $audition->audition_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($audition->status === 'pending')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($audition->status === 'approved')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.auditions.show', $audition->id) }}" class="text-theme-primary hover:text-[#e05e00] mr-3">View</a>
                        <a href="{{ route('admin.auditions.edit', $audition->id) }}" class="text-theme-primary hover:text-[#e05e00] mr-3">Edit</a>
                        <button type="button" class="text-theme-error hover:text-red-700" onclick="confirmDelete({{ $audition->id }})">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-theme-text-secondary">
                        No auditions found.
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
                <p class="mt-2 text-theme-text-secondary">Are you sure you want to delete this audition? This action cannot be undone.</p>
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
@endsection

@section('scripts')
    <script>
        function confirmDelete(auditionId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/admin/auditions/${auditionId}`;
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
            
            // Handle filter application
            document.getElementById('applyFilters').addEventListener('click', function() {
                const movieFilter = document.getElementById('movie_filter').value;
                const statusFilter = document.getElementById('status_filter').value;
                
                // Build query string
                let queryParams = [];
                if (movieFilter) queryParams.push(`movie=${encodeURIComponent(movieFilter)}`);
                if (statusFilter) queryParams.push(`status=${encodeURIComponent(statusFilter)}`);
                
                // Construct the URL with query parameters
                let url = "{{ route('admin.auditions.index') }}";
                if (queryParams.length > 0) {
                    url += '?' + queryParams.join('&');
                }
                
                // Redirect to the filtered URL
                window.location.href = url;
            });
            
            // Handle clear filters
            document.getElementById('clearFilters').addEventListener('click', function() {
                // Redirect to the base URL without query parameters
                window.location.href = "{{ route('admin.auditions.index') }}";
            });
        });

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection