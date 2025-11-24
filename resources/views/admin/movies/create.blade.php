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
            <form method="POST" action="{{ route('admin.movies.store') }}" class="p-6">
                @csrf
                
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
                        <input type="text" name="genre" id="genre" class="input-field mt-1 block w-full" value="{{ old('genre') }}" required>
                        @error('genre')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="release_date" class="block text-sm font-medium text-theme-text">Release Date</label>
                        <input type="date" name="release_date" id="release_date" class="input-field mt-1 block w-full" value="{{ old('release_date') }}" required>
                        @error('release_date')
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
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Cancel</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 mt-6" data-loading>
                        <span class="loading-text">Create Movie</span>
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