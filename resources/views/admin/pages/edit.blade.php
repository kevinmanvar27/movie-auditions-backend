@extends('layouts.admin')

@section('title', 'Edit Page')

@section('head')
    <!-- TinyMCE Editor (Self-hosted via jsDelivr - no API key required) -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
@endsection

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Edit Page</h1>
            <p class="text-theme-text-secondary">Edit "{{ $page->title }}"</p>
        </div>

        <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
            <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Main Content Column -->
                        <div class="lg:col-span-2 space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-theme-text">Page Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" class="input-field mt-1 block w-full" value="{{ old('title', $page->title) }}" required>
                                @error('title')
                                    <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="slug" class="block text-sm font-medium text-theme-text">URL Slug</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-theme-border bg-theme-secondary text-theme-text-secondary text-sm">/</span>
                                    <input type="text" name="slug" id="slug" class="input-field flex-1 block w-full rounded-none rounded-r-md" value="{{ old('slug', $page->slug) }}">
                                </div>
                                @error('slug')
                                    <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="content" class="block text-sm font-medium text-theme-text">Page Content</label>
                                <textarea name="content" id="content" rows="15" class="input-field mt-1 block w-full">{{ old('content', $page->content) }}</textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Sidebar Column -->
                        <div class="space-y-6">
                            <!-- Status & Visibility -->
                            <div class="border border-theme-border rounded-lg p-4">
                                <h3 class="font-medium text-theme-text mb-4">Publish Settings</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-theme-text">Status <span class="text-red-500">*</span></label>
                                        <select name="status" id="status" class="input-field mt-1 block w-full">
                                            <option value="draft" {{ old('status', $page->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ old('status', $page->status) == 'published' ? 'selected' : '' }}>Published</option>
                                            <option value="archived" {{ old('status', $page->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="order" class="block text-sm font-medium text-theme-text">Display Order</label>
                                        <input type="number" name="order" id="order" class="input-field mt-1 block w-full" value="{{ old('order', $page->order) }}" min="0">
                                        <p class="mt-1 text-xs text-theme-text-secondary">Lower numbers appear first</p>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="show_in_menu" id="show_in_menu" value="1" class="rounded border-theme-border text-theme-primary focus:ring-theme-primary" {{ old('show_in_menu', $page->show_in_menu) ? 'checked' : '' }}>
                                        <label for="show_in_menu" class="ml-2 text-sm text-theme-text">Show in navigation menu</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SEO Settings -->
                            <div class="border border-theme-border rounded-lg p-4">
                                <h3 class="font-medium text-theme-text mb-4">SEO Settings</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="meta_title" class="block text-sm font-medium text-theme-text">Meta Title</label>
                                        <input type="text" name="meta_title" id="meta_title" class="input-field mt-1 block w-full" value="{{ old('meta_title', $page->meta_title) }}" maxlength="60">
                                        <p class="mt-1 text-xs text-theme-text-secondary">Recommended: 50-60 characters</p>
                                    </div>
                                    
                                    <div>
                                        <label for="meta_description" class="block text-sm font-medium text-theme-text">Meta Description</label>
                                        <textarea name="meta_description" id="meta_description" rows="3" class="input-field mt-1 block w-full" maxlength="160">{{ old('meta_description', $page->meta_description) }}</textarea>
                                        <p class="mt-1 text-xs text-theme-text-secondary">Recommended: 150-160 characters</p>
                                    </div>
                                    
                                    <div>
                                        <label for="meta_keywords" class="block text-sm font-medium text-theme-text">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" id="meta_keywords" class="input-field mt-1 block w-full" value="{{ old('meta_keywords', $page->meta_keywords) }}">
                                        <p class="mt-1 text-xs text-theme-text-secondary">Comma-separated keywords</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Page Info -->
                            <div class="border border-theme-border rounded-lg p-4">
                                <h3 class="font-medium text-theme-text mb-4">Page Information</h3>
                                <div class="space-y-2 text-sm text-theme-text-secondary">
                                    <p><strong>Created:</strong> {{ $page->created_at->format('M d, Y H:i') }}</p>
                                    @if($page->creator)
                                        <p><strong>Created by:</strong> {{ $page->creator->name }}</p>
                                    @endif
                                    <p><strong>Last updated:</strong> {{ $page->updated_at->format('M d, Y H:i') }}</p>
                                    @if($page->updater)
                                        <p><strong>Updated by:</strong> {{ $page->updater->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-theme-background border-t border-theme-border flex justify-end space-x-3">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary px-4 py-2">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                        <span class="loading-text">Update Page</span>
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

@section('scripts')
<script>
    // Initialize TinyMCE if available
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#content',
            height: 400,
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
            content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        });
    }
</script>
@endsection
