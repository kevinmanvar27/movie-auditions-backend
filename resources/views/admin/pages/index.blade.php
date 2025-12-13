@extends('layouts.admin')

@section('title', 'Pages Management')

@section('content')
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-theme-text">Pages</h1>
                <p class="text-theme-text-secondary">Manage dynamic pages for your website</p>
            </div>
            <div class="mt-4 sm:mt-0 ml-auto">
                <a href="{{ route('admin.pages.create') }}" class="btn btn-primary py-2 px-4" data-loading>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add New Page
                </a>
            </div>
        </div>

        <!-- Pages Table -->
        <div class="bg-theme-surface rounded-lg shadow">
            <table class="min-w-full divide-y divide-theme-border datatable p-2" id="pagesTable">
                <thead class="bg-theme-secondary">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Order</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Menu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Updated</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-theme-background divide-y divide-theme-border">
                    @forelse($pages as $page)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">{{ $page->order }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-theme-text">{{ $page->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-secondary">
                            <code class="bg-theme-secondary px-2 py-1 rounded text-xs">/{{ $page->slug }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $page->status_badge_class }}">
                                {{ ucfirst($page->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">
                            @if($page->show_in_menu)
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text-secondary">
                            {{ $page->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium table-actions flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.pages.show', $page) }}" class="btn-view">View</a>
                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn-edit">Edit</a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this page? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-theme-text-secondary">
                            No pages found. <a href="{{ route('admin.pages.create') }}" class="text-theme-primary hover:underline">Create your first page</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
