@extends('layouts.admin')

@section('title', 'Movie Details')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Movie Details</h1>
            <p class="text-theme-text-secondary">View detailed information about the movie</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Title</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->title }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Genre</label>
                        <p class="mt-1 text-theme-text-secondary">{{ is_array($movie->genre) ? implode(', ', $movie->genre) : $movie->genre }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">End Date</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->end_date->format('M d, Y') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Director</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->director }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Budget ( In CR )</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->budget ? '₹' . number_format($movie->budget * 10000000, 2) . ' (' . number_format($movie->budget, 2) . ' CR)' : 'Not specified' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Status</label>
                        <p class="mt-1 text-theme-text-secondary">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($movie->status === 'active') bg-green-100 text-green-800
                                @elseif($movie->status === 'inactive') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($movie->status) }}
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-theme-text">Description</label>
                    <p class="mt-1 text-theme-text-secondary">{{ $movie->description ?? 'No description provided.' }}</p>
                </div>
                
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Back to Movies</a>
                    <a href="{{ route('admin.movies.edit', $movie) }}" class="btn btn-primary py-2 px-4 mt-6">Edit Movie</a>
                </div>


                <!-- Auditions Section -->
                <div class="mt-8" style="margin-top: 20px;">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-theme-text">Related Auditions</h2>
                        
                        <!-- Filter Dropdowns -->
                        @if($auditions->total() > 0 || isset($roleFilter) || isset($statusFilter))
                            <form method="GET" id="filter-form" class="flex space-x-2">
                                <!-- Role Filter Dropdown -->
                                <div class="relative">
                                    <select id="role-filter" name="role" class="input-field" onchange="this.form.submit()">
                                        <option value="">All Roles</option>
                                        @foreach($uniqueRoles as $role)
                                            <option value="{{ $role }}" {{ isset($roleFilter) && $roleFilter == $role ? 'selected' : '' }}>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Status Filter Dropdown -->
                                <div class="relative">
                                    <select id="status-filter" name="status" class="input-field" onchange="this.form.submit()">
                                        <option value="">All Statuses</option>
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ isset($statusFilter) && $statusFilter == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Reset Filters Button (only show when filters are active) -->
                                @if(isset($roleFilter) || isset($statusFilter))
                                    <div class="relative">
                                        <a href="{{ route('admin.movies.show', $movie) }}" class="btn btn-primary py-2 px-4">
                                            Reset
                                        </a>
                                    </div>
                                @endif
                            </form>
                        @endif
                    </div>
                    
                    @if($auditions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6" id="auditions-container">
                            @foreach($auditions as $audition)
                                <div class="audition-card">
                                    <div class="audition-header">
                                        <div>
                                            <h3 class="audition-title">{{ $audition->role }}</h3>   
                                            <p class="audition-movie">
                                                For <span class="audition-movie-title">{{ $movie->title }}</span>
                                            </p>
                                            <p class="audition-date">
                                                Submitted by <strong>{{ $audition->user->name }}</strong> on {{ $audition->created_at->format('M j, Y') }}
                                            </p>
                                        </div>
                                        
                                        <div>
                                            @if(auth()->user()->hasPermission('manage_movies') || auth()->user()->hasRole('Super Admin'))
                                                <select class="audition-status-dropdown" data-audition-id="{{ $audition->id }}" onchange="updateAuditionStatus(this)">
                                                    <option value="pending" {{ $audition->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="viewed" {{ $audition->status == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                                    <option value="shortlisted" {{ $audition->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                                    <option value="rejected" {{ $audition->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                </select>
                                            @else
                                                <span class="audition-status 
                                                    @if($audition->status == 'pending') status-pending
                                                    @elseif($audition->status == 'shortlisted') status-approved
                                                    @elseif($audition->status == 'viewed') status-viewed
                                                    @else status-rejected @endif">
                                                    {{ ucfirst($audition->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($audition->notes)
                                        <div class="audition-notes-container">
                                            <p class="audition-notes">{{ $audition->notes }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($audition->uploaded_videos && count(json_decode($audition->uploaded_videos)) > 0)
                                        <div class="audition-videos-section">
                                            <p class="audition-videos-title">Videos ({{ count(json_decode($audition->uploaded_videos)) }}):</p>
                                            <div class="video-grid">
                                                @foreach(array_slice(json_decode($audition->uploaded_videos), 0, 3) as $videoUrl)
                                                    <div class="video-container">
                                                        <video controls class="video-element">
                                                            <source src="{{ $videoUrl }}" type="video/mp4">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    </div>
                                                @endforeach
                                                @if(count(json_decode($audition->uploaded_videos)) > 3)
                                                    <div class="video-placeholder">
                                                        <span class="video-placeholder-text">+{{ count(json_decode($audition->uploaded_videos)) - 3 }} more videos</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination Links -->
                        <div class="mt-6">
                            {{ $auditions->appends(request()->except('page'))->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-theme-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-theme-text">No auditions found</h3>
                            <p class="mt-1 text-theme-text-secondary">
                                @if(isset($roleFilter) || isset($statusFilter))
                                    No auditions match your filter criteria.
                                    @if(isset($roleFilter))
                                        <br>Role filter: <strong>{{ $roleFilter }}</strong>
                                    @endif
                                    @if(isset($statusFilter))
                                        <br>Status filter: <strong>{{ $statusOptions[$statusFilter] ?? ucfirst($statusFilter) }}</strong>
                                    @endif
                                @else
                                    There are no auditions submitted for this movie yet.
                                @endif
                            </p>
                            
                            @if(isset($roleFilter) || isset($statusFilter))
                                <div class="mt-6">
                                    <a href="{{ route('admin.movies.show', $movie) }}" class="btn btn-primary py-2 px-4">
                                        Reset Filters
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function updateAuditionStatus(selectElement) {
        const auditionId = selectElement.dataset.auditionId;
        const newStatus = selectElement.value;
        
        // Disable the select while processing
        selectElement.disabled = true;
        
        // Send AJAX request to update status
        const baseUrl = "{{ route('admin.auditions.update-status', ['audition' => 'AUDITION_ID_PLACEHOLDER']) }}";
        const url = baseUrl.replace('AUDITION_ID_PLACEHOLDER', auditionId);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the select element's class based on the new status
                selectElement.className = 'audition-status-dropdown';
                
                // Add appropriate class based on status
                if (newStatus === 'pending') {
                    selectElement.classList.add('status-pending');
                } else if (newStatus === 'viewed') {
                    selectElement.classList.add('status-viewed');
                } else if (newStatus === 'shortlisted') {
                    selectElement.classList.add('status-approved');
                } else {
                    selectElement.classList.add('status-rejected');
                }
                
                // Show success message
                alert('Audition status updated successfully!');
            } else {
                // Revert to previous selection on error
                selectElement.value = selectElement.dataset.previousValue || selectElement.dataset.initialValue;
                alert('Failed to update audition status: ' + data.message);
            }
        })
        .catch(error => {
            // Revert to previous selection on error
            selectElement.value = selectElement.dataset.previousValue || selectElement.dataset.initialValue;
            alert('An error occurred while updating audition status.');
            console.error('Error:', error);
        })
        .finally(() => {
            // Re-enable the select
            selectElement.disabled = false;
            // Store current value for potential revert
            selectElement.dataset.previousValue = newStatus;
        });
    }
    
    // Store initial values when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.audition-status-dropdown');
        selects.forEach(select => {
            select.dataset.initialValue = select.value;
        });
    });
</script>
@endsection