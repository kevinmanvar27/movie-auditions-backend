@extends('layouts.admin')

@section('title', 'View Page')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-theme-text">{{ $page->title }}</h1>
                <p class="text-theme-text-secondary">
                    <code class="bg-theme-secondary px-2 py-1 rounded text-xs">/{{ $page->slug }}</code>
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-primary py-2 px-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Page
                </a>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary py-2 px-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                    <div class="p-4 sm:p-6">
                        <h2 class="text-lg font-medium text-theme-text mb-4">Page Content</h2>
                        <div class="prose max-w-none text-theme-text">
                            {!! $page->content ?: '<p class="text-theme-text-secondary italic">No content</p>' !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                    <div class="p-4">
                        <h3 class="font-medium text-theme-text mb-4">Page Status</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-theme-text-secondary">Status</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $page->status_badge_class }}">
                                    {{ ucfirst($page->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-theme-text-secondary">Display Order</span>
                                <span class="text-sm text-theme-text">{{ $page->order }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-theme-text-secondary">Show in Menu</span>
                                @if($page->show_in_menu)
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- SEO Card -->
                <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                    <div class="p-4">
                        <h3 class="font-medium text-theme-text mb-4">SEO Information</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs text-theme-text-secondary uppercase tracking-wide">Meta Title</span>
                                <p class="text-sm text-theme-text mt-1">{{ $page->meta_title ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-theme-text-secondary uppercase tracking-wide">Meta Description</span>
                                <p class="text-sm text-theme-text mt-1">{{ $page->meta_description ?: '-' }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-theme-text-secondary uppercase tracking-wide">Meta Keywords</span>
                                <p class="text-sm text-theme-text mt-1">{{ $page->meta_keywords ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Info Card -->
                <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                    <div class="p-4">
                        <h3 class="font-medium text-theme-text mb-4">Page Information</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-theme-text-secondary">Created</span>
                                <span class="text-theme-text">{{ $page->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            @if($page->creator)
                            <div class="flex justify-between">
                                <span class="text-theme-text-secondary">Created by</span>
                                <span class="text-theme-text">{{ $page->creator->name }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-theme-text-secondary">Last updated</span>
                                <span class="text-theme-text">{{ $page->updated_at->format('M d, Y H:i') }}</span>
                            </div>
                            @if($page->updater)
                            <div class="flex justify-between">
                                <span class="text-theme-text-secondary">Updated by</span>
                                <span class="text-theme-text">{{ $page->updater->name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Actions Card -->
                <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                    <div class="p-4">
                        <h3 class="font-medium text-theme-text mb-4">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-secondary w-full justify-center py-2">
                                Edit Page
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-full justify-center py-2">
                                    Delete Page
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
