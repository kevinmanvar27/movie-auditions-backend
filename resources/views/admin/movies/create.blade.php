@extends('layouts.admin')

@section('title', 'Add New Movie')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Add New Movie</h1>
            <p class="text-theme-text-secondary">Fill in the details below to add a new movie</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border">
            <form method="POST" action="{{ route('admin.movies.store') }}" class="p-6" id="movieForm">
                @csrf
                
                <!-- Hidden fields for payment verification -->
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-theme-text">Title</label>
                        <input type="text" name="title" id="title" class="input-field mt-1 block w-full" value="{{ old('title') }}" required>
                        @error('title')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="genre" class="block text-sm font-medium text-theme-text">Genre</label>
                        <select name="genre[]" id="genre" class="input-field mt-1 block w-full" multiple required>
                            <option value="">Select Genre</option>
                            <option value="Action" {{ is_array(old('genre')) && in_array('Action', old('genre')) ? 'selected' : '' }}>Action</option>
                            <option value="Adventure" {{ is_array(old('genre')) && in_array('Adventure', old('genre')) ? 'selected' : '' }}>Adventure</option>
                            <option value="Animation" {{ is_array(old('genre')) && in_array('Animation', old('genre')) ? 'selected' : '' }}>Animation</option>
                            <option value="Biography" {{ is_array(old('genre')) && in_array('Biography', old('genre')) ? 'selected' : '' }}>Biography</option>
                            <option value="Comedy" {{ is_array(old('genre')) && in_array('Comedy', old('genre')) ? 'selected' : '' }}>Comedy</option>
                            <option value="Crime" {{ is_array(old('genre')) && in_array('Crime', old('genre')) ? 'selected' : '' }}>Crime</option>
                            <option value="Documentary" {{ is_array(old('genre')) && in_array('Documentary', old('genre')) ? 'selected' : '' }}>Documentary</option>
                            <option value="Drama" {{ is_array(old('genre')) && in_array('Drama', old('genre')) ? 'selected' : '' }}>Drama</option>
                            <option value="Family" {{ is_array(old('genre')) && in_array('Family', old('genre')) ? 'selected' : '' }}>Family</option>
                            <option value="Fantasy" {{ is_array(old('genre')) && in_array('Fantasy', old('genre')) ? 'selected' : '' }}>Fantasy</option>
                            <option value="History" {{ is_array(old('genre')) && in_array('History', old('genre')) ? 'selected' : '' }}>History</option>
                            <option value="Horror" {{ is_array(old('genre')) && in_array('Horror', old('genre')) ? 'selected' : '' }}>Horror</option>
                            <option value="Music" {{ is_array(old('genre')) && in_array('Music', old('genre')) ? 'selected' : '' }}>Music</option>
                            <option value="Mystery" {{ is_array(old('genre')) && in_array('Mystery', old('genre')) ? 'selected' : '' }}>Mystery</option>
                            <option value="Romance" {{ is_array(old('genre')) && in_array('Romance', old('genre')) ? 'selected' : '' }}>Romance</option>
                            <option value="Sci-Fi" {{ is_array(old('genre')) && in_array('Sci-Fi', old('genre')) ? 'selected' : '' }}>Sci-Fi</option>
                            <option value="Sport" {{ is_array(old('genre')) && in_array('Sport', old('genre')) ? 'selected' : '' }}>Sport</option>
                            <option value="Thriller" {{ is_array(old('genre')) && in_array('Thriller', old('genre')) ? 'selected' : '' }}>Thriller</option>
                            <option value="War" {{ is_array(old('genre')) && in_array('War', old('genre')) ? 'selected' : '' }}>War</option>
                            <option value="Western" {{ is_array(old('genre')) && in_array('Western', old('genre')) ? 'selected' : '' }}>Western</option>
                        </select>
                        @error('genre')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-theme-text">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="input-field mt-1 block w-full" value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}" required>
                        @error('end_date')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="director" class="block text-sm font-medium text-theme-text">Director</label>
                        <input type="text" name="director" id="director" class="input-field mt-1 block w-full" value="{{ old('director') }}" required>
                        @error('director')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-theme-text">Status</label>
                        <select name="status" id="status" class="input-field mt-1 block w-full" required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-theme-text">Description</label>
                    <textarea name="description" id="description" rows="4" class="input-field mt-1 block w-full" aria-label="Movie description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Character Roles Section -->
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-theme-text">Character Roles</h2>
                    </div>
                    
                    <div id="roles-container">
                        <!-- Initial role fields -->
                        <div class="role-fields mb-4 p-4 bg-theme-background rounded-lg border border-theme-border">
                            <input type="hidden" name="roles[0][deleted]" class="role-deleted" value="0">
                            <div class="space-y-4">
                                <!-- First row with Role Type, Gender, Age Range -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-theme-text">Role Type</label>
                                        <input type="text" name="roles[0][role_type]" class="input-field mt-1 block w-full" placeholder="e.g., Lead, Supporting">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-theme-text">Gender</label>
                                        <select name="roles[0][gender]" class="input-field mt-1 block w-full">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                            <option value="None">None</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-theme-text">Age Range</label>
                                        <input type="text" name="roles[0][age_range]" placeholder="e.g., 23-99" pattern="[0-9]{2}-[0-9]{2}" class="input-field mt-1 block w-full">
                                    </div>
                                </div>
                                
                                <!-- Second row with Dialogue Sample (Full Width) -->
                                <div>
                                    <label class="block text-sm font-medium text-theme-text">Dialogue Sample</label>
                                    <textarea name="roles[0][dialogue_sample]" rows="2" class="input-field mt-1 block w-full" placeholder="Enter sample dialogue..."></textarea>
                                </div>
                                
                                <!-- Remove button -->
                                <div class="flex justify-end table-actions">
                                    <button type="button" class="remove-role text-sm text-theme-error hover:text-red-700 btn-delete">
                                        Remove Role
                                    </button>
                                </div>
                            </div>
                        </div>
                        
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
                    <button type="button" class="btn btn-primary py-2 px-4 mt-6" id="payAndCreateBtn" data-loading>
                        <span class="loading-text">Pay & Create Movie</span>
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
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for genre selection
            $('#genre').select2({
                placeholder: 'Select Genre',
                allowClear: true,
                width: '100%'
            });
            
            const rolesContainer = document.getElementById('roles-container');
            let roleIndex = 1;
            
            // Function to add event listeners to remove buttons
            function addRemoveEventListeners() {
                document.querySelectorAll('.remove-role').forEach(button => {
                    button.addEventListener('click', function() {
                        const roleFields = this.closest('.role-fields');
                        if (document.querySelectorAll('.role-fields').length > 1) {
                            roleFields.remove();
                        } else {
                            // Clear the fields instead of removing if it's the last one
                            const inputs = roleFields.querySelectorAll('input, select, textarea');
                            inputs.forEach(input => {
                                if (input.type === 'checkbox' || input.type === 'radio') {
                                    input.checked = false;
                                } else {
                                    input.value = '';
                                }
                            });
                        }
                    });
                });
            }
            
            // Add event listener to the initial remove button
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
            
            // Handle payment and creation
            document.getElementById('payAndCreateBtn').addEventListener('click', function(e) {
                e.preventDefault();
                
                // Validate form fields
                var title = document.getElementById('title').value;
                var genre = document.getElementById('genre').value;
                var endDate = document.getElementById('end_date').value;
                var director = document.getElementById('director').value;
                var status = document.getElementById('status').value;
                
                if (!title || !genre || !endDate || !director || !status) {
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Create payment order
                fetch('{{ route("payment.movie.order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        // Open Razorpay checkout
                        var options = {
                            "key": response.razorpay_key_id || "{{ env('RAZORPAY_KEY_ID') }}",
                            "amount": response.amount * 100, // Amount in paise
                            "currency": response.currency,
                            "name": "Movie Auditions Platform",
                            "description": "Movie Creation Fee",
                            "order_id": response.order_id,
                            "handler": function (rzpResponse) {
                                // Set payment details in hidden fields
                                document.getElementById('razorpay_payment_id').value = rzpResponse.razorpay_payment_id;
                                document.getElementById('razorpay_order_id').value = rzpResponse.razorpay_order_id;
                                document.getElementById('razorpay_signature').value = rzpResponse.razorpay_signature;
                                
                                // Submit the form
                                document.getElementById('movieForm').submit();
                            },
                            "prefill": {
                                "name": "{{ Auth::user()->name }}",
                                "email": "{{ Auth::user()->email }}"
                            },
                            "theme": {
                                "color": "#F37254"
                            }
                        };
                        
                        var rzp = new Razorpay(options);
                        rzp.open();
                    } else {
                        alert(response.message || 'Failed to create payment order.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to create payment order. Please try again.');
                });
            });
        });
    </script>
@endsection