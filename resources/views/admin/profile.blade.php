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
@endsection