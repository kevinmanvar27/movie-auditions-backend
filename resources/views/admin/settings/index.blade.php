@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Settings</h1>
            <p class="text-theme-text-secondary">Manage system settings and preferences</p>
        </div>

        <div class="grid grid-cols-1">
            <!-- Site Configuration -->
            <div>
                <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                    <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                        <h2 class="text-lg font-medium text-theme-text">Site Configuration</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.update') }}" class="p-4 md:p-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-theme-text">Site Name</label>
                                <input type="text" name="site_name" id="site_name" class="input-field mt-1 block w-full" value="Movie Auditions Platform" required>
                            </div>
                            
                            <div>
                                <label for="site_description" class="block text-sm font-medium text-theme-text">Site Description</label>
                                <textarea name="site_description" id="site_description" rows="3" class="input-field mt-1 block w-full">A platform for managing movie auditions and casting.</textarea>
                            </div>
                            
                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-theme-text">Admin Email</label>
                                <input type="email" name="admin_email" id="admin_email" class="input-field mt-1 block w-full" value="admin@example.com" required>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                                <span class="loading-text ">Save Settings</span>
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
        </div>
    </div>
@endsection