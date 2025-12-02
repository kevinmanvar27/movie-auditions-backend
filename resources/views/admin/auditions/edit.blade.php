@extends('layouts.admin')

@section('title', 'Edit Audition')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Edit Audition</h1>
            <p class="text-theme-text-secondary">Update the audition details below</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <form method="POST" action="{{ route('admin.auditions.update', $audition->id) }}" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Hidden fields for user_id -->
                <input type="hidden" name="user_id" value="{{ $audition->user_id }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="applicant_name" class="block text-sm font-medium text-theme-text">Applicant Name</label>
                        <input type="text" name="applicant_name" id="applicant_name" class="input-field mt-1 block w-full" value="{{ old('applicant_name', $audition->applicant_name) }}" required>
                    </div>
                    
                    <div>
                        <label for="applicant_email" class="block text-sm font-medium text-theme-text">Applicant Email</label>
                        <input type="email" name="applicant_email" id="applicant_email" class="input-field mt-1 block w-full" value="{{ old('applicant_email', $audition->applicant_email) }}" required>
                    </div>
                    
                    <div>
                        <label for="movie_id" class="block text-sm font-medium text-theme-text">Movie</label>
                        <select name="movie_id" id="movie_id" class="input-field mt-1 block w-full" required>
                            <option value="">Select Movie</option>
                            @foreach(App\Models\Movie::all() as $movie)
                                <option value="{{ $movie->id }}" {{ old('movie_id', $audition->movie_id) == $movie->id ? 'selected' : '' }}>
                                    {{ $movie->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-medium text-theme-text">Character Role</label>
                        <input type="text" name="role" id="role" class="input-field mt-1 block w-full" value="{{ old('role', $audition->role) }}" required>
                    </div>
                    
                    <div>
                        <label for="audition_date" class="block text-sm font-medium text-theme-text">Audition Date</label>
                        <input type="date" name="audition_date" id="audition_date" class="input-field mt-1 block w-full" value="{{ old('audition_date', $audition->audition_date->format('Y-m-d')) }}" required>
                    </div>
                    
                    <div>
                        <label for="audition_time" class="block text-sm font-medium text-theme-text">Audition Time</label>
                        <input type="time" name="audition_time" id="audition_time" class="input-field mt-1 block w-full" value="{{ old('audition_time', $audition->audition_time) }}">
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-theme-text">Status</label>
                        <select name="status" id="status" class="input-field mt-1 block w-full" required>
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status', $audition->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $audition->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status', $audition->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-theme-text">Notes</label>
                    <textarea name="notes" id="notes" rows="4" class="input-field mt-1 block w-full">{{ old('notes', $audition->notes) }}</textarea>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.auditions.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Cancel</a>
                    <button type="submit" class="btn btn-primary py-2 px-4 mt-6" data-loading>
                        <span class="loading-text">Update Audition</span>
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