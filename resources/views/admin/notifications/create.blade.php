@extends('layouts.admin')

@section('title', 'Send Notification')

@section('content')
<div class="p-4 sm:p-6">
    @include('components.error-message')
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-theme-text">Send Notification</h1>
        <p class="text-theme-text-secondary">Create and send a notification to targeted users</p>
    </div>
    
    <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
        <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
            <h2 class="text-lg font-medium text-theme-text">Notification Details</h2>
        </div>
        
        <form method="POST" action="{{ route('admin.notifications.send') }}" class="p-4 md:p-6">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-theme-text">Notification Title</label>
                    <input type="text" name="title" id="title" class="input-field mt-1 block w-full" required>
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-theme-text">Notification Message</label>
                    <textarea name="message" id="message" rows="4" class="input-field mt-1 block w-full" required></textarea>
                </div>
                
                <div class="border-t border-theme-border pt-6">
                    <h3 class="text-lg font-medium text-theme-text mb-4">Target Audience Filters</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Roles Filter -->
                        <div>
                            <label class="block text-sm font-medium text-theme-text mb-2">User Roles</label>
                            <div class="space-y-2">
                                @if(isset($roles) && count($roles) > 0)
                                    @foreach($roles as $role)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="target_roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <label for="role_{{ $role->id }}" class="ml-2 text-sm text-theme-text">{{ $role->name }}</label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-theme-text-secondary">No roles available</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Movies Filter -->
                        <div>
                            <label class="block text-sm font-medium text-theme-text mb-2">Specific Movies</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                                @if(isset($movies) && count($movies) > 0)
                                    @foreach($movies as $movie)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="target_movies[]" value="{{ $movie->id }}" id="movie_{{ $movie->id }}" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary">
                                            <label for="movie_{{ $movie->id }}" class="ml-2 text-sm text-theme-text">{{ $movie->title }}</label>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-theme-text-secondary">No movies available</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Gender Filter -->
                        <div>
                            <label class="block text-sm font-medium text-theme-text mb-2">Gender</label>
                            <select name="gender" class="input-field block w-full">
                                <option value="">Any Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <!-- Age Range Filter -->
                        <div>
                            <label class="block text-sm font-medium text-theme-text mb-2">Age Range</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="min_age" class="block text-sm text-theme-text-secondary">Minimum Age</label>
                                    <input type="number" name="min_age" id="min_age" class="input-field mt-1 block w-full" min="0" max="120">
                                </div>
                                <div>
                                    <label for="max_age" class="block text-sm text-theme-text-secondary">Maximum Age</label>
                                    <input type="number" name="max_age" id="max_age" class="input-field mt-1 block w-full" min="0" max="120">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary px-4 py-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                        <span class="loading-text">Send Notification</span>
                        <span class="loading-spinner hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection