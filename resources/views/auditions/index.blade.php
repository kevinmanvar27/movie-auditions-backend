@extends('layouts.admin')

@section('title', 'My Auditions')

@section('content')
<div class="p-4">
    <div class="max-w-6xl mx-auto">
        <div class="bg-theme-surface rounded-lg shadow-lg p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 sm:mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-theme-text">My Auditions</h1>
                    <p class="text-theme-text-secondary mt-2">View and manage your audition submissions</p>
                </div>
                
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('auditions.create') }}">
                        <x-button variant="primary" size="md">
                            Submit New Audition
                        </x-button>
                    </a>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-theme-success bg-opacity-20 border border-theme-success rounded-lg text-theme-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-theme-secondary rounded-lg p-4 mb-6">
                <form method="GET" action="{{ route('auditions.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="movie_title" class="block text-sm font-medium text-theme-text mb-1">
                            Movie Title
                        </label>
                        <select 
                            id="movie_title" 
                            name="movie_title"
                            class="input-field mt-1 block w-full px-3 py-2 border border-theme-border rounded-md focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text w-full"
                        >
                            <option value="">All Movies</option>
                            @foreach(App\Models\Movie::pluck('title') as $title)
                                <option value="{{ $title }}" {{ request('movie_title') == $title ? 'selected' : '' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-theme-text mb-1">
                            Role
                        </label>
                        <select 
                            id="role" 
                            name="role"
                            class="input-field mt-1 block w-full px-3 py-2 border border-theme-border rounded-md focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text"
                        >
                            <option value="">All Roles</option>
                            @foreach(App\Models\Audition::select('role')->distinct()->pluck('role') as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-theme-text mb-1">
                            Status
                        </label>
                        <select 
                            id="status" 
                            name="status"
                            class="input-field mt-1 block w-full px-3 py-2 border border-theme-border rounded-md focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text"
                        >
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                            <option value="shortlisted" {{ request('status') == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="btn btn-primary w-full py-2 px-4">
                            Filter
                        </button>
                        <a href="{{ route('auditions.index') }}" class="btn btn-secondary w-full py-2 px-4">
                            Clear
                        </a>
                    </div>
                    
                    <!-- Preserve sort parameters in form -->
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                </form>
            </div>

            <!-- Audition Reel -->
            @if($auditions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6">
                    @foreach($auditions as $audition)
                        <div class="audition-card">
                            <div class="audition-header">
                                <div>
                                    <h3 class="audition-title">{{ $audition->role }}</h3>   
                                    <p class="audition-movie">
                                        For <span class="audition-movie-title">{{ $audition->movie->title }}</span>
                                    </p>
                                    <p class="audition-date">
                                        Submitted on {{ $audition->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                                
                                <div>
                                    <span class="audition-status 
                                        @if($audition->status == 'pending') status-pending
                                        @elseif($audition->status == 'shortlisted') status-approved
                                        @else status-rejected @endif">
                                        {{ ucfirst($audition->status) }}
                                    </span>
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
                            
                            <div class="audition-actions">
                                <button type="button" class="view-details-btn" data-audition-id="{{ $audition->id }}">
                                    View Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($auditions->hasPages())
                    <div class="mt-8">
                        {{ $auditions->appends(request()->except('page'))->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-theme-text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-theme-text">No auditions found</h3>
                    <p class="mt-1 text-theme-text-secondary">Get started by submitting your first audition.</p>
                    <div class="mt-6">
                        <a href="{{ route('auditions.create') }}">
                            <x-button variant="primary" size="md">
                                Submit New Audition
                            </x-button>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Audition Detail Modal -->
<div id="auditionModal" class="audition-modal backdrop-blur">
    <!-- <div class="modal-overlay">
        <div class="modal-overlay-inner" onclick="closeAuditionModal()"></div>
    </div> -->

    <div class="modal-container">
        <div class="modal-content-wrapper">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">
                    Audition Details
                </h3>
                <button type="button" onclick="closeAuditionModal()" class="modal-close-btn">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Modal content will be loaded here -->
                <div class="loading-spinner">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add event listeners to all "View Details" buttons
    document.addEventListener('DOMContentLoaded', function() {
        const viewButtons = document.querySelectorAll('.view-details-btn');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const auditionId = this.getAttribute('data-audition-id');
                openAuditionModal(auditionId);
            });
        });
    });

    function openAuditionModal(auditionId) {
        // Show the modal
        document.getElementById('auditionModal').style.display = 'block';
        
        // Prevent background scrolling
        document.body.style.overflow = 'hidden';
        
        // Show loading indicator
        document.getElementById('modalContent').innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
        
        // Fetch audition details
        fetch(`/auditions/${auditionId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const audition = data.data;
                document.getElementById('modalTitle').textContent = audition.role;
                
                let statusClass = '';
                if (audition.status === 'pending') {
                    statusClass = 'status-pending';
                } else if (audition.status === 'shortlisted') {
                    statusClass = 'status-approved';
                } else {
                    statusClass = 'status-rejected';
                }
                
                let videosHtml = '';
                // Ensure audition.uploaded_videos is an array
                let uploadedVideos = audition.uploaded_videos || [];
                // If it's an object, convert to array
                if (!Array.isArray(uploadedVideos) && typeof uploadedVideos === 'object') {
                    uploadedVideos = Object.values(uploadedVideos);
                }
                // If it's a string, parse it as JSON
                if (typeof uploadedVideos === 'string') {
                    try {
                        uploadedVideos = JSON.parse(uploadedVideos);
                    } catch (e) {
                        uploadedVideos = [];
                    }
                }
                // Ensure it's an array
                if (!Array.isArray(uploadedVideos)) {
                    uploadedVideos = [];
                }
                
                // Parse old video backups
                let oldVideoBackups = audition.old_video_backups || [];
                if (typeof oldVideoBackups === 'string') {
                    try {
                        oldVideoBackups = JSON.parse(oldVideoBackups);
                    } catch (e) {
                        oldVideoBackups = [];
                    }
                }
                if (!Array.isArray(oldVideoBackups)) {
                    oldVideoBackups = [];
                }
                
                if (uploadedVideos.length > 0 || oldVideoBackups.length > 0) {
                    videosHtml = `
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="detail-label">Uploaded Videos</h3>
                                <div class="flex space-x-2">
                                    <button type="button" class="upload-new-video-btn px-4 py-2 bg-theme-primary text-theme-background rounded-md hover:underline" data-audition-id="${audition.id}">
                                        Upload New Video
                                    </button>
                                    ${oldVideoBackups.length > 0 ? `
                                    <button type="button" class="history-btn px-4 py-2 bg-theme-secondary text-theme-text rounded-md hover:bg-theme-border" data-audition-id="${audition.id}">
                                        History (${oldVideoBackups.length})
                                    </button>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="space-y-3">
                    `;
                    
                    if (uploadedVideos.length > 0) {
                        uploadedVideos.forEach((videoUrl, index) => {
                            videosHtml += `
                                <div class="video-container relative table-actions" id="video-container-${index}">
                                    <button type="button" class="btn btn-delete text-theme-error hover:text-red-700 text-sm" data-video-url="${videoUrl}" data-audition-id="${audition.id}" style="position: absolute; top: 5px; right: 5px; z-index: 1;">
                                        Remove
                                    </button>
                                    <video controls autoplay muted loop class="video-element">
                                        <source src="${videoUrl}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            `;
                        });
                    } else {
                        videosHtml += `
                            <div class="border-2 border-dashed border-theme-border rounded-lg p-6 text-center">
                                <p class="text-theme-text-secondary">No current video uploaded</p>
                            </div>
                        `;
                    }
                    
                    videosHtml += `
                            </div>
                        </div>
                    `;
                } else {
                    // If no videos, show upload button
                    videosHtml = `
                        <div>
                            <h3 class="detail-label">Uploaded Videos</h3>
                            <div class="border-2 border-dashed border-theme-border rounded-lg p-6 text-center">
                                <p class="text-theme-text-secondary mb-4">No videos uploaded yet</p>
                                <button type="button" class="upload-new-video-btn px-4 py-2 bg-theme-primary text-theme-background rounded-md hover:underline" data-audition-id="${audition.id}">
                                    Upload New Video
                                </button>
                            </div>
                        </div>
                    `;
                }
                
                // History section (initially hidden)
                // let historyHtml = '';
                // if (oldVideoBackups.length > 0) {
                //     historyHtml = `
                //         <div id="history-section-${audition.id}" class="hidden">
                //             <h3 class="detail-label mt-6">Video History</h3>
                //             <div class="space-y-3">
                //     `;
                    
                //     oldVideoBackups.forEach((videoUrl, index) => {
                //         historyHtml += `
                //             <div class="video-container relative" id="history-video-container-${index}">
                //                 <video controls autoplay muted loop class="video-element">
                //                     <source src="${videoUrl}" type="video/mp4">
                //                     Your browser does not support the video tag.
                //                 </video>
                //                 <div class="mt-2 text-sm text-theme-text-secondary">
                //                     ${getFileNameFromUrl(videoUrl)}
                //                 </div>
                //             </div>
                //         `;
                //     });
                    
                //     historyHtml += `
                //             </div>
                //         </div>
                //     `;
                // }
                
                let notesHtml = '';
                if (audition.notes) {
                    notesHtml = `
                        <div>
                            <h3 class="detail-label">Notes</h3>
                            <div class="audition-notes-container">
                                <p class="audition-notes">${audition.notes}</p>
                            </div>
                        </div>
                    `;
                }
                
                document.getElementById('modalContent').innerHTML = `
                    <div class="space-y-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-2xl font-bold text-theme-text">${audition.role}</h1>
                                <p class="text-theme-text-secondary">Audition for ${audition.movie.title}</p>
                            </div>
                            
                            <span class="audition-status ${statusClass}">
                                ${audition.status.charAt(0).toUpperCase() + audition.status.slice(1)}
                            </span>
                        </div>
                        <div class="modal-details-grid">
                            <div>
                                <h3 class="detail-label">Movie</h3>
                                <p class="detail-value">${audition.movie.title}</p>
                            </div>
                            
                            <div>
                                <h3 class="detail-label">Submitted On</h3>
                                <p class="detail-value">${new Date(audition.created_at).toLocaleDateString()}</p>
                            </div>
                            
                            <div>
                                <h3 class="detail-label">Applicant Name</h3>
                                <p class="detail-value">${audition.applicant_name}</p>
                            </div>
                            
                            <div>
                                <h3 class="detail-label">Status Updated</h3>
                                <p class="detail-value">${new Date(audition.updated_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        
                        ${notesHtml}
                        
                        ${videosHtml}
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="closeAuditionModal()" class="close-modal-btn px-4 py-2 border border-theme-border rounded-md text-theme-text hover:bg-theme-secondary">
                                Close
                            </button>
                            <button type="button" class="save-audition-btn px-4 py-2 bg-theme-primary text-theme-background rounded-md hover:bg-blue-600" data-audition-id="${audition.id}">
                                Save Changes
                            </button>
                        </div>
                    </div>
                `;
                
                // Add hidden file input to the document body
                let fileInput = document.getElementById(`new-video-file-${audition.id}`);
                if (!fileInput) {
                    fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.id = `new-video-file-${audition.id}`;
                    fileInput.className = 'hidden new-video-file-input';
                    fileInput.accept = 'video/*';
                    fileInput.multiple = false;
                    fileInput.setAttribute('data-audition-id', audition.id);
                    document.body.appendChild(fileInput);
                    
                    // Add event listener for file selection
                    fileInput.addEventListener('change', function() {
                        const auditionId = this.getAttribute('data-audition-id');
                        const files = this.files;
                        if (files.length > 0) {
                            uploadNewVideos(auditionId, files);
                        }
                        // Reset the input
                        this.value = '';
                    });
                }
                
                // Attach event listeners
                attachRemoveVideoListeners();
                attachUploadVideoListener();
                attachSaveButtonListener();
                attachHistoryButtonListener();

            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalContent').innerHTML = '<p class="text-red-500">Error loading audition details.</p>';
        });
    }

    function closeAuditionModal() {
        document.getElementById('auditionModal').style.display = 'none';
        
        // Restore background scrolling
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('auditionModal');
        if (event.target === modal) {
            closeAuditionModal();
        }
    });

    function getFileNameFromUrl(url) {
        return url.substring(url.lastIndexOf('/') + 1);
    }

    // Add event listener for remove video buttons
    function attachRemoveVideoListeners() {
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const videoUrl = this.getAttribute('data-video-url');
                const auditionId = this.getAttribute('data-audition-id');
                const videoContainer = this.closest('.video-container');
                
                if (confirm('Are you sure you want to remove this video?')) {
                    removeVideo(auditionId, videoUrl, videoContainer);
                }
            });
        });
    }

    function removeVideo(auditionId, videoUrl, videoContainer) {
        fetch(`/auditions/${auditionId}/remove-video`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                video_url: videoUrl
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the video container from the DOM
                videoContainer.remove();
                // Show success message
                alert('Video removed successfully');
            } else {
                alert('Error removing video: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing video');
        });
    }

    // Add event listener for upload video button
    function attachUploadVideoListener() {
        document.querySelectorAll('.upload-new-video-btn').forEach(button => {
            button.addEventListener('click', function() {
                const auditionId = this.getAttribute('data-audition-id');
                const fileInput = document.getElementById(`new-video-file-${auditionId}`);
                fileInput.click();
            });
        });
    }

    // Add event listener for save button
    function attachSaveButtonListener() {
        document.querySelectorAll('.save-audition-btn').forEach(button => {
            button.addEventListener('click', function() {
                const auditionId = this.getAttribute('data-audition-id');
                saveAuditionChanges(auditionId);
            });
        });
    }

    // Add event listener for history button
    function attachHistoryButtonListener() {
        document.querySelectorAll('.history-btn').forEach(button => {
            button.addEventListener('click', function() {
                const auditionId = this.getAttribute('data-audition-id');
                toggleHistorySection(auditionId);
            });
        });
    }

    // Toggle history section visibility
    function toggleHistorySection(auditionId) {
        const historySection = document.getElementById(`history-section-${auditionId}`);
        if (historySection) {
            if (historySection.classList.contains('hidden')) {
                historySection.classList.remove('hidden');
            } else {
                historySection.classList.add('hidden');
            }
        }
    }

    // Function to upload new videos
    function uploadNewVideos(auditionId, files) {
        // Only take the first file since we now only allow one video
        const file = files[0];
        if (!file) {
            alert('No file selected');
            return;
        }
        
        const formData = new FormData();
        formData.append('new_videos', file);
        
        // Show uploading message
        alert('Uploading video...');
        
        fetch(`/auditions/${auditionId}/upload-videos`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Video uploaded successfully');
                // Refresh the modal to show new videos
                openAuditionModal(auditionId);
            } else {
                alert('Error uploading video: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading video');
        });
    }

    // Function to save audition changes
    function saveAuditionChanges(auditionId) {
        // In a real implementation, this would save any changes to the audition
        // alert('Changes saved successfully!');
        closeAuditionModal();
        location.reload();
    }

</script>
@endsection