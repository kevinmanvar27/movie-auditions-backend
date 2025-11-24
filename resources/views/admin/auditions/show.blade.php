@extends('layouts.admin')

@section('title', 'Audition Details')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Audition Details</h1>
            <p class="text-theme-text-secondary">View detailed information about this audition</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-theme-text">Applicant Information</h3>
                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Applicant Name</label>
                                <p class="mt-1 text-theme-text">John Doe</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Applicant Email</label>
                                <p class="mt-1 text-theme-text">john.doe@example.com</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-theme-text">Audition Details</h3>
                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Movie</label>
                                <p class="mt-1 text-theme-text">The Adventure Begins</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Character Role</label>
                                <p class="mt-1 text-theme-text">Lead Hero</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Audition Date</label>
                                <p class="mt-1 text-theme-text">2023-06-20</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Audition Time</label>
                                <p class="mt-1 text-theme-text">14:30</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Status</label>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-theme-text">Notes</h3>
                    <div class="mt-2 p-4 bg-theme-background rounded-lg border border-theme-border">
                        <p class="text-theme-text">Applicant has previous acting experience and is available for callbacks.</p>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('admin.auditions.index') }}" class="btn btn-secondary py-2 px-4 mt-6 mr-4">Back to List</a>
                    <a href="{{ route('admin.auditions.edit', 1) }}" class="btn btn-primary py-2 px-4 mt-6">Edit Audition</a>
                </div>
            </div>
        </div>
    </div>
@endsection