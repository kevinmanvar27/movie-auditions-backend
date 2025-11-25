@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Edit User</h1>
            <p class="text-theme-text-secondary">Update the user details below</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-theme-text">Full Name</label>
                        <input type="text" name="name" id="name" class="input-field mt-1 block w-full" value="{{ $user->name }}" required>
                        @error('name')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-theme-text">Email Address</label>
                        <input type="email" name="email" id="email" class="input-field mt-1 block w-full" value="{{ $user->email }}" required>
                        @error('email')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="role_id" class="block text-sm font-medium text-theme-text">Role</label>
                        <select name="role_id" id="role_id" class="input-field mt-1 block w-full" required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-theme-text">Status</label>
                        <select name="status" id="status" class="input-field mt-1 block w-full" required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Cancel</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 mt-6" data-loading>
                        <span class="loading-text">Update User</span>
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
@endsection