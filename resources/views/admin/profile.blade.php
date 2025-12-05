@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
<div class="p-4 sm:p-6">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul class="list-disc pl-5 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-theme-text">Edit Profile</h1>
        <p class="text-theme-text-secondary">Update your personal information and password</p>
    </div>

    <div class="bg-theme-surface rounded-lg shadow border border-theme-border">
        <!-- Profile Update Form -->
        <div class="p-6 border-b border-theme-border">
            <h3 class="text-xl font-semibold text-theme-text mb-4">Profile Information</h3>
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-theme-text">Name</label>
                        <input type="text" name="name" id="name" class="input-field mt-1 block w-full" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-theme-text">Email Address</label>
                        <input type="email" name="email" id="email" class="input-field mt-1 block w-full" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-theme-text">Mobile Number</label>
                        <input type="text" name="mobile_number" id="mobile_number" class="input-field mt-1 block w-full" value="{{ old('mobile_number', $user->mobile_number) }}">
                        @error('mobile_number')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-theme-text">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="input-field mt-1 block w-full" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="gender" class="block text-sm font-medium text-theme-text">Gender</label>
                        <select name="gender" id="gender" class="input-field mt-1 block w-full">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-theme-text">Profile Photo</label>
                        <input type="file" name="profile_photo" id="profile_photo" class="input-field mt-1 block w-full">
                        @error('profile_photo')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                        @if($user->profile_photo)
                            <div class="mt-2">
                                <span class="text-sm text-theme-text-secondary">Current photo:</span>
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo" class="mt-1 w-16 h-16 rounded-full object-cover">
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary py-2 px-4">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Image Gallery Section -->
        <div class="p-6 border-b border-theme-border">
            <h3 class="text-xl font-semibold text-theme-text mb-4">Image Gallery</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-theme-text mb-2">Upload Images</label>
                <div class="flex items-center justify-center w-full">
                    <label for="gallery_images" class="flex flex-col items-center justify-center w-full h-32 border-2 border-theme-border border-dashed rounded-lg cursor-pointer bg-theme-background hover:bg-theme-surface">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-theme-text-secondary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-theme-text-secondary"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-theme-text-secondary">PNG, JPG, GIF up to 2MB</p>
                        </div>
                        <input id="gallery_images" name="gallery_images[]" type="file" class="hidden" accept="image/*" multiple />
                    </label>
                </div>
                <button type="button" id="upload-gallery-btn" class="mt-2 btn btn-primary py-2 px-4">Upload Images</button>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-theme-text mb-2">Gallery Preview</label>
                <div id="gallery-preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- Gallery images will be loaded here dynamically -->
                </div>
            </div>
        </div>
        
        <!-- Password Update Form -->
        <div class="p-6">
            <h3 class="text-xl font-semibold text-theme-text mb-4">Change Password</h3>
            <form action="{{ route('admin.profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-theme-text">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="input-field mt-1 block w-full">
                        @error('current_password')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-theme-text">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="input-field mt-1 block w-full">
                        @error('new_password')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-theme-text">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="input-field mt-1 block w-full">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary py-2 px-4">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load existing gallery images
        loadGalleryImages();
        
        // Handle gallery image upload
        document.getElementById('upload-gallery-btn').addEventListener('click', function() {
            const files = document.getElementById('gallery_images').files;
            if (files.length === 0) {
                alert('Please select at least one image to upload.');
                return;
            }
            
            uploadGalleryImages(files);
        });
        
        // Function to load gallery images
        function loadGalleryImages() {
            fetch(`/api/v1/users/{{ $user->id }}/gallery`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderGallery(data.data.gallery);
                    }
                })
                .catch(error => {
                    console.error('Error loading gallery:', error);
                });
        }
        
        // Function to upload gallery images
        function uploadGalleryImages(files) {
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }
            
            fetch(`/api/v1/users/{{ $user->id }}/gallery`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderGallery(data.data.gallery);
                    document.getElementById('gallery_images').value = '';
                    alert('Images uploaded successfully!');
                } else {
                    alert('Error uploading images: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error uploading images:', error);
                alert('Error uploading images. Please try again.');
            });
        }
        
        // Function to render gallery images
        function renderGallery(images) {
            const galleryPreview = document.getElementById('gallery-preview');
            galleryPreview.innerHTML = '';
            
            if (images.length === 0) {
                galleryPreview.innerHTML = '<p class="text-theme-text-secondary">No images in gallery.</p>';
                return;
            }
            
            images.forEach((imageUrl, index) => {
                const imgContainer = document.createElement('div');
                imgContainer.className = 'relative group';
                imgContainer.innerHTML = `
                    <img src="${imageUrl}" alt="Gallery Image" class="w-full h-32 object-cover rounded-lg cursor-pointer" onclick="openImageModal('${imageUrl}')">
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                        <button type="button" class="text-white mx-1" onclick="event.stopPropagation(); deleteImage('${imageUrl}')" title="Delete">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                        <button type="button" class="text-white mx-1" onclick="event.stopPropagation(); moveImage(${index}, 'up')" title="Move Up">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button type="button" class="text-white mx-1" onclick="event.stopPropagation(); moveImage(${index}, 'down')" title="Move Down">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                `;
                galleryPreview.appendChild(imgContainer);
            });
        }
        
        // Function to delete an image
        function deleteImage(imageUrl) {
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }
            
            // Extract the image path from the URL
            const imagePath = imageUrl.replace(window.location.origin + '/storage/', '');
            
            fetch(`/api/v1/users/{{ $user->id }}/gallery/${encodeURIComponent(imagePath)}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderGallery(data.data.gallery);
                    alert('Image deleted successfully!');
                } else {
                    alert('Error deleting image: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting image:', error);
                alert('Error deleting image. Please try again.');
            });
        }
        
        // Function to move an image
        function moveImage(index, direction) {
            // Get current gallery images
            fetch(`/api/v1/users/{{ $user->id }}/gallery`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const images = data.data.gallery.map(url => url.replace(window.location.origin + '/storage/', ''));
                        
                        // Move image in array
                        if (direction === 'up' && index > 0) {
                            [images[index], images[index - 1]] = [images[index - 1], images[index]];
                        } else if (direction === 'down' && index < images.length - 1) {
                            [images[index], images[index + 1]] = [images[index + 1], images[index]];
                        }
                        
                        // Update gallery order
                        fetch(`/api/v1/users/{{ $user->id }}/gallery`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ gallery: images })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                renderGallery(data.data.gallery);
                            } else {
                                alert('Error updating gallery: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error updating gallery:', error);
                            alert('Error updating gallery. Please try again.');
                        });
                    }
                })
                .catch(error => {
                    console.error('Error getting gallery:', error);
                });
        }
        
        // Function to open image in modal
        window.openImageModal = function(imageUrl) {
            // Create modal HTML
            const modalHtml = `
                <div id="image-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" onclick="closeImageModal()">
                    <div class="relative max-w-4xl max-h-full" onclick="event.stopPropagation()">
                        <img src="${imageUrl}" class="max-w-full max-h-full object-contain" alt="Full Size Image">
                        <button class="absolute top-4 right-4 text-white text-2xl" onclick="closeImageModal()">&times;</button>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
        };
        
        // Function to close image modal
        window.closeImageModal = function() {
            const modal = document.getElementById('image-modal');
            if (modal) {
                modal.remove();
                document.body.style.overflow = 'auto';
            }
        };
    });
</script>
@endsection