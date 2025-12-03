@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Settings</h1>
            <p class="text-theme-text-secondary">Manage system settings and preferences</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Site Configuration -->
            <div>
                <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                    <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                        <h2 class="text-lg font-medium text-theme-text">Site Configuration</h2>
                    </div>
                    <form method="POST" action="{{ route('admin.settings.update') }}" class="p-4 md:p-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-6">
                            <!-- Logo Upload Section -->
                            <div class="space-y-4">
                                <label class="block text-sm font-medium text-theme-text mb-2">Site Logo</label>
                                <div class="flex flex-col md:flex-row items-start md:items-center gap-6 p-4 bg-theme-surface rounded-lg border border-theme-border">
                                    <!-- Current Logo Preview -->
                                    <div class="flex-shrink-0">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs text-theme-text-secondary mb-2">Current Logo</span>
                                            @if(!empty($settings['logo_path']))
                                                <img src="{{ Storage::url($settings['logo_path']) }}" alt="Current Logo" class="object-contain rounded-lg border border-theme-border" height="128" width="128">
                                            @else
                                                <div class="bg-gray-100 border-2 border-dashed border-theme-border rounded-lg w-16 h-16 flex items-center justify-center text-gray-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- File Upload -->
                                    <div class="flex-1 w-full">
                                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-theme-border border-dashed rounded-lg transition-colors duration-200 hover:border-theme-primary">
                                            <div class="space-y-3 text-center">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="flex flex-col sm:flex-row text-sm text-gray-600 items-center justify-center gap-2">
                                                    <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-theme-primary hover:text-orange-600 focus-within:outline-none">
                                                        <span>Upload a file</span>
                                                        <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                                    </label>
                                                    <p class="hidden sm:block pl-1">or drag and drop</p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    PNG, JPG, GIF up to 2MB
                                                </p>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-theme-text-secondary">
                                            Recommended size: 160x160 pixels
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-theme-text">Site Name</label>
                                <input type="text" name="site_name" id="site_name" class="input-field mt-1 block w-full" value="{{ $settings['site_name'] ?? 'Movie Auditions Platform' }}" required>
                            </div>
                            
                            <div>
                                <label for="site_description" class="block text-sm font-medium text-theme-text">Site Description</label>
                                <textarea name="site_description" id="site_description" rows="3" class="input-field mt-1 block w-full">{{ $settings['site_description'] ?? 'A platform for managing movies and casting.' }}</textarea>
                            </div>
                            
                            <div>
                                <label for="admin_email" class="block text-sm font-medium text-theme-text">Admin Email</label>
                                <input type="email" name="admin_email" id="admin_email" class="input-field mt-1 block w-full" value="{{ $settings['admin_email'] ?? 'admin@example.com' }}" required>
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