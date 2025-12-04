@extends('layouts.admin')

@section('title', 'Edit Movie')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Edit Movie</h1>
            <p class="text-theme-text-secondary">Update the details below to modify the movie</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-theme-text">Title</label>
                        <input type="text" name="title" id="title" class="input-field mt-1 block w-full" value="{{ old('title', $movie->title) }}" required>
                    </div>
                    
                    <div>
                        <label for="genre" class="block text-sm font-medium text-theme-text">Genre</label>
                        <select name="genre[]" id="genre" class="input-field mt-1 block w-full" multiple required>
                            <option value="">Select Genre</option>
                            @php
                                $movieGenres = is_array($movie->genre) ? $movie->genre : (is_string($movie->genre) ? json_decode($movie->genre, true) : []);
                                $oldGenres = old('genre', $movieGenres);
                                if (!is_array($oldGenres)) {
                                    $oldGenres = [];
                                }
                            @endphp
                            <option value="Action" {{ in_array('Action', $oldGenres) ? 'selected' : '' }}>Action</option>
                            <option value="Adventure" {{ in_array('Adventure', $oldGenres) ? 'selected' : '' }}>Adventure</option>
                            <option value="Animation" {{ in_array('Animation', $oldGenres) ? 'selected' : '' }}>Animation</option>
                            <option value="Biography" {{ in_array('Biography', $oldGenres) ? 'selected' : '' }}>Biography</option>
                            <option value="Comedy" {{ in_array('Comedy', $oldGenres) ? 'selected' : '' }}>Comedy</option>
                            <option value="Crime" {{ in_array('Crime', $oldGenres) ? 'selected' : '' }}>Crime</option>
                            <option value="Documentary" {{ in_array('Documentary', $oldGenres) ? 'selected' : '' }}>Documentary</option>
                            <option value="Drama" {{ in_array('Drama', $oldGenres) ? 'selected' : '' }}>Drama</option>
                            <option value="Family" {{ in_array('Family', $oldGenres) ? 'selected' : '' }}>Family</option>
                            <option value="Fantasy" {{ in_array('Fantasy', $oldGenres) ? 'selected' : '' }}>Fantasy</option>
                            <option value="History" {{ in_array('History', $oldGenres) ? 'selected' : '' }}>History</option>
                            <option value="Horror" {{ in_array('Horror', $oldGenres) ? 'selected' : '' }}>Horror</option>
                            <option value="Music" {{ in_array('Music', $oldGenres) ? 'selected' : '' }}>Music</option>
                            <option value="Mystery" {{ in_array('Mystery', $oldGenres) ? 'selected' : '' }}>Mystery</option>
                            <option value="Romance" {{ in_array('Romance', $oldGenres) ? 'selected' : '' }}>Romance</option>
                            <option value="Sci-Fi" {{ in_array('Sci-Fi', $oldGenres) ? 'selected' : '' }}>Sci-Fi</option>
                            <option value="Sport" {{ in_array('Sport', $oldGenres) ? 'selected' : '' }}>Sport</option>
                            <option value="Thriller" {{ in_array('Thriller', $oldGenres) ? 'selected' : '' }}>Thriller</option>
                            <option value="War" {{ in_array('War', $oldGenres) ? 'selected' : '' }}>War</option>
                            <option value="Western" {{ in_array('Western', $oldGenres) ? 'selected' : '' }}>Western</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-theme-text">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="input-field mt-1 block w-full" value="{{ old('end_date', $movie->end_date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div>
                        <label for="director" class="block text-sm font-medium text-theme-text">Director</label>
                        <input type="text" name="director" id="director" class="input-field mt-1 block w-full" value="{{ old('director', $movie->director) }}" required>
                    </div>
                    
                    <div>
                        <label for="budget" class="block text-sm font-medium text-theme-text">Budget</label>
                        <input type="number" name="budget" id="budget" class="input-field mt-1 block w-full" value="{{ old('budget', $movie->budget) }}" step="0.01" min="0">
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-theme-text">Status</label>
                        <select name="status" id="status" class="input-field mt-1 block w-full" required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $movie->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $movie->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="upcoming" {{ old('status', $movie->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-theme-text">Description</label>
                    <textarea name="description" id="description" rows="4" class="input-field mt-1 block w-full">{{ old('description', $movie->description) }}</textarea>
                </div>
                
                <!-- Character Roles Section -->
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-theme-text">Character Roles</h2>
                    </div>
                    
                    <div id="roles-container">
                        <!-- Existing role fields -->
                        @foreach($movie->roles as $index => $role)
                        <div class="role-fields mb-4 p-4 bg-theme-background rounded-lg border border-theme-border">
                            <input type="hidden" name="roles[{{ $index }}][id]" value="{{ $role->id }}">
                            <input type="hidden" name="roles[{{ $index }}][deleted]" class="role-deleted" value="0">
                            <div class="space-y-4">
                                <!-- First row with Role Type, Gender, Age Range -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-theme-text">Role Type</label>
                                        <input type="text" name="roles[{{ $index }}][role_type]" class="input-field mt-1 block w-full" placeholder="e.g., Lead, Supporting" value="{{ old('roles.' . $index . '.role_type', $role->role_type) }}">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-theme-text">Gender</label>
                                        <select name="roles[{{ $index }}][gender]" class="input-field mt-1 block w-full">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('roles.' . $index . '.gender', $role->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('roles.' . $index . '.gender', $role->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ old('roles.' . $index . '.gender', $role->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                                            <option value="None" {{ old('roles.' . $index . '.gender', $role->gender) === 'None' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-theme-text">Age Range</label>
                                        <input type="text" name="roles[{{ $index }}][age_range]" placeholder="e.g., 23-99" pattern="[0-9]{2}-[0-9]{2}" class="input-field mt-1 block w-full" value="{{ old('roles.' . $index . '.age_range', $role->age_range) }}">
                                    </div>
                                </div>
                                
                                <!-- Second row with Dialogue Sample (Full Width) -->
                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Dialogue Sample</label>
                                    <textarea name="roles[{{ $index }}][dialogue_sample]" rows="2" class="input-field mt-1 block w-full" placeholder="Enter sample dialogue...">{{ old('roles.' . $index . '.dialogue_sample', $role->dialogue_sample) }}</textarea>
                                </div>
                                
                                <!-- Remove button -->
                                <div class="flex justify-end table-actions">
                                    <button type="button" class="remove-role text-sm text-theme-error hover:text-red-700 btn-delete">
                                        Remove Role
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Add Role button -->
                        <div class="mt-4" id="add-role-container">
                            <button type="button" id="add-role" class="btn btn-secondary py-2 px-4 text-sm">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Role
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Cancel</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 mt-6" data-loading>
                        <span class="loading-text">Update Movie</span>
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

    <!-- JavaScript for dynamic role fields -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for genre selection
            $('#genre').select2({
                placeholder: 'Select Genre',
                allowClear: true,
                width: '100%'
            });
            
            const rolesContainer = document.getElementById('roles-container');
            let roleIndex = {{ $movie->roles->count() }};
            
            // Function to add event listeners to remove buttons
            function addRemoveEventListeners() {
                document.querySelectorAll('.remove-role').forEach(button => {
                    button.addEventListener('click', function() {
                        const roleFields = this.closest('.role-fields');
                        if (document.querySelectorAll('.role-fields').length > 1) {
                            // Mark as deleted instead of removing
                            const deletedInput = roleFields.querySelector('.role-deleted');
                            if (deletedInput) {
                                deletedInput.value = '1';
                                roleFields.style.display = 'none';
                            } else {
                                roleFields.remove();
                            }
                        } else {
                            // Clear the fields instead of removing if it's the last one
                            const inputs = roleFields.querySelectorAll('input:not([type="hidden"]), select, textarea');
                            inputs.forEach(input => {
                                if (input.type === 'checkbox' || input.type === 'radio') {
                                    input.checked = false;
                                } else {
                                    input.value = '';
                                }
                            });
                            
                            // Mark as deleted if it has an ID
                            const deletedInput = roleFields.querySelector('.role-deleted');
                            if (deletedInput) {
                                deletedInput.value = '1';
                            }
                        }
                    });
                });
            }
            
            // Add event listener to the initial remove buttons
            addRemoveEventListeners();
            
            // Add new role fields
            document.addEventListener('click', function(e) {
                if (e.target.id === 'add-role' || (e.target.closest('#add-role') && e.target.closest('#add-role').id === 'add-role')) {
                    // Create new role fields with the same structure as the initial one
                    const roleFields = document.createElement('div');
                    roleFields.className = 'role-fields mb-4 p-4 bg-theme-background rounded-lg border border-theme-border';
                    roleFields.innerHTML = `
                        <input type="hidden" name="roles[${roleIndex}][deleted]" class="role-deleted" value="0">
                        <div class="space-y-4">
                            <!-- First row with Role Type, Gender, Age Range -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Role Type</label>
                                    <input type="text" name="roles[${roleIndex}][role_type]" class="input-field mt-1 block w-full" placeholder="e.g., Lead, Supporting">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Gender</label>
                                    <select name="roles[${roleIndex}][gender]" class="input-field mt-1 block w-full">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                        <option value="None">None</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Age Range</label>
                                    <input type="text" name="roles[${roleIndex}][age_range]" placeholder="e.g., 23-99" pattern="[0-9]{2}-[0-9]{2}" class="input-field mt-1 block w-full">
                                </div>
                            </div>
                            
                            <!-- Second row with Dialogue Sample (Full Width) -->
                            <div>
                                <label class="block text-sm font-medium text-theme-text">Dialogue Sample</label>
                                <textarea name="roles[${roleIndex}][dialogue_sample]" rows="2" class="input-field mt-1 block w-full" placeholder="Enter sample dialogue..."></textarea>
                            </div>
                            
                            <!-- Remove button -->
                            <div class="flex justify-end table-actions">
                                <button type="button" class="remove-role text-sm text-theme-error hover:text-red-700 btn-delete">
                                    Remove Role
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Insert the new role fields before the add button container
                    const addButtonContainer = document.getElementById('add-role-container');
                    rolesContainer.insertBefore(roleFields, addButtonContainer);
                    
                    roleIndex++;
                    
                    // Re-add event listeners to all remove buttons
                    addRemoveEventListeners();
                }
            });
        });
    </script>
@endsection