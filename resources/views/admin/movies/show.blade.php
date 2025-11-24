@extends('layouts.admin')

@section('title', 'Movie Details')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Movie Details</h1>
            <p class="text-theme-text-secondary">View detailed information about the movie</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Title</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->title }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Genre</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->genre }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Release Date</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->release_date->format('M d, Y') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Director</label>
                        <p class="mt-1 text-theme-text-secondary">{{ $movie->director }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-theme-text">Status</label>
                        <p class="mt-1 text-theme-text-secondary">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($movie->status === 'active') bg-green-100 text-green-800
                                @elseif($movie->status === 'inactive') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($movie->status) }}
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="block text-sm font-medium text-theme-text">Description</label>
                    <p class="mt-1 text-theme-text-secondary">{{ $movie->description ?? 'No description provided.' }}</p>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Back to Movies</a>
                    <a href="{{ route('admin.movies.edit', $movie) }}" class="btn btn-primary py-2 px-4 mt-6">Edit Movie</a>
                </div>
            </div>
        </div>
    </div>
@endsection