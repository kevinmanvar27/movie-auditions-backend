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

            <form method="POST" action="{{ route('auditions.store') }}" enctype="multipart/form-data" id="auditionForm">
                @csrf
                
                <!-- Hidden fields for payment verification -->
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                
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
                    
                    @if(is_audition_user_payment_required())
                        <x-button type="button" variant="primary" size="md" class="w-full sm:w-auto" id="payAndSubmitBtn">
                            Pay & Submit Audition
                        </x-button>
                    @else
                        <x-button type="submit" variant="primary" size="md" class="w-full sm:w-auto">
                            Submit Audition
                        </x-button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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
    
    // Handle payment and submission
    @if(is_audition_user_payment_required())
    $('#payAndSubmitBtn').on('click', function(e) {
        e.preventDefault();
        
        // Validate form fields
        var movieId = $('#movie_id').val();
        var role = $('#role').val();
        var applicantName = $('#applicant_name').val();
        
        if (!movieId || !role || !applicantName) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // Create payment order
        $.ajax({
            url: '{{ route("payment.audition.order") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Open Razorpay checkout
                    var options = {
                        "key": response.razorpay_key_id || "{{ env('RAZORPAY_KEY_ID') }}",
                        "amount": response.amount * 100, // Amount in paise
                        "currency": response.currency,
                        "name": "Movie Auditions Platform",
                        "description": "Audition Submission Fee",
                        "order_id": response.order_id,
                        "handler": function (response) {
                            // Set payment details in hidden fields
                            $('#razorpay_payment_id').val(response.razorpay_payment_id);
                            $('#razorpay_order_id').val(response.razorpay_order_id);
                            $('#razorpay_signature').val(response.razorpay_signature);
                            
                            // Submit the form
                            $('#auditionForm').submit();
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
            },
            error: function(xhr) {
                alert('Failed to create payment order. Please try again.');
            }
        });
    });
    @endif
});
</script>
@endsection