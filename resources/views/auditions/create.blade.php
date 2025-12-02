@extends('layouts.admin')

@section('title', 'Submit Audition')

@section('content')
<div class="p-4">
    <div class="mx-auto">
        <div class="bg-theme-surface rounded-lg shadow-lg p-6 sm:p-8">
            <div class="text-left mb-6 sm:mb-8">
                <h1 class="text-2xl font-bold text-theme-text">Submit Audition</h1>
                <p class="text-theme-text-secondary mt-2">Fill in the details below to submit your audition</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-theme-success bg-opacity-20 border border-stheme-success rounded-lg text-theme-success">
                    {{ session('status') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="mb-6 p-4 bg-theme-error bg-opacity-20 border border-theme-error rounded-lg text-theme-error">
                    {{ session('error') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-theme-error bg-opacity-20 border border-theme-error rounded-lg text-theme-error">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('auditions.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    <!-- Movie Selection -->
                    <x-input 
                        label="Select Movie" 
                        id="movie_id" 
                        name="movie_id" 
                        type="select"
                        required>
                        <option value="">Choose a movie</option>
                        @foreach($movies as $movie)
                            <option value="{{ $movie->id }}" {{ old('movie_id') == $movie->id ? 'selected' : '' }}>
                                {{ $movie->title }}
                            </option>
                        @endforeach
                    </x-input>

                    <!-- Role Selection (Dependent Dropdown) -->
                    <x-input 
                        label="Select Role" 
                        id="role" 
                        name="role" 
                        type="select"
                        required
                    >
                        <option value="">First select a movie</option>
                    </x-input>

                    <!-- Applicant Name -->
                    <x-input 
                        label="Applicant Name" 
                        id="applicant_name" 
                        name="applicant_name" 
                        value="{{ old('applicant_name', Auth::user()->name) }}" 
                        required
                        placeholder="Enter your name"
                    ></x-input>

                    <!-- File Upload -->
                    <x-input 
                        label="Upload Video File" 
                        id="uploaded_videos" 
                        name="uploaded_videos" 
                        type="file"
                        accept="video/*"
                    >
                        <p class="mt-1 text-sm text-theme-text-secondary">You can select only one video file</p>
                        @error('uploaded_videos.*')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </x-input>

                    <!-- Notes -->
                    <x-input 
                        label="Notes" 
                        id="notes" 
                        name="notes" 
                        type="textarea"
                        rows="4"
                        placeholder="Any additional notes or information about your audition"
                    >{{ old('notes') }}</x-input>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row justify-end gap-4">
                    <a href="{{ route('auditions.index') }}">
                        <x-button variant="secondary" size="md" class="w-full sm:w-auto">
                            Cancel
                        </x-button>
                    </a>
                    
                    <x-button type="submit" variant="primary" size="md" class="w-full sm:w-auto">
                        Submit Audition
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Handle movie selection change
    $('#movie_id').on('change', function() {
        var movieId = $(this).val();
        var roleSelect = $('#role');
        
        // Clear existing options
        roleSelect.empty().append('<option value="">Loading roles...</option>');
        
        if (movieId) {
            // Fetch roles via AJAX
            $.ajax({
                url: '{{ route("movies.roles", ["movie" => ":movie"]) }}'.replace(':movie', movieId),
                method: 'GET',
                success: function(data) {
                    // Clear and populate roles
                    roleSelect.empty().append('<option value="">Select a role</option>');
                    
                    if (data.length > 0) {
                        $.each(data, function(index, role) {
                            var optionText = 'Role ' + (index + 1);
                            if (role.role_type) {
                                optionText += ' (' + role.role_type + ')';
                            }
                            if (role.gender) {
                                optionText += ' - ' + role.gender;
                            }
                            if (role.age_range) {
                                optionText += ' (' + role.age_range + ')';
                            }
                            
                            var selected = '';
                            // Check if this role matches the old input value
                            var oldRole = $('input[name="role"]').val();
                            if (oldRole && oldRole === ('Role ' + (index + 1))) {
                                selected = 'selected';
                            }
                            
                            roleSelect.append('<option value="' + optionText + '" ' + selected + '>' + optionText + '</option>');
                        });
                    } else {
                        roleSelect.append('<option value="">No roles available for this movie</option>');
                    }
                },
                error: function() {
                    roleSelect.empty().append('<option value="">Error loading roles</option>');
                }
            });
        } else {
            // No movie selected
            roleSelect.empty().append('<option value="">First select a movie</option>');
        }
    });
    
    // Trigger change event on page load if a movie is already selected
    var selectedMovie = $('#movie_id').val();
    if (selectedMovie) {
        $('#movie_id').trigger('change');
    }
});
</script>
@endsection