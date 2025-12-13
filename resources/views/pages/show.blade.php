@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title)

@section('head')
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @if($page->meta_keywords)
        <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif
@endsection

@section('content')
    <div class="min-h-screen bg-theme-background">
        <!-- Header -->
        <header class="bg-theme-surface border-b border-theme-border shadow-sm">
            <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
                @php
                    $logoUrl = function_exists('get_site_logo') ? get_site_logo() : null;
                @endphp
                <a href="{{ url('/') }}" class="flex items-center">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-10 mr-3">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-10 mr-3">
                    @endif
                    <span class="text-lg font-semibold text-theme-text">{{ config('app.name', 'Movie Auditions') }}</span>
                </a>
                @include('components.theme-toggle', ['class' => 'p-2 rounded-full hover:bg-theme-secondary focus:outline-none focus:ring-2 focus:ring-theme-primary'])
            </div>
        </header>

        <!-- Page Content -->
        <main class="max-w-4xl mx-auto px-4 py-8">
            <article class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                <div class="p-6 sm:p-8">
                    <h1 class="text-3xl font-bold text-theme-text mb-6">{{ $page->title }}</h1>
                    
                    <div class="prose prose-lg max-w-none text-theme-text">
                        {!! $page->content !!}
                    </div>
                </div>
            </article>
            
            <!-- Last Updated -->
            <p class="mt-4 text-sm text-theme-text-secondary text-center">
                Last updated: {{ $page->updated_at->format('F j, Y') }}
            </p>
        </main>

        <!-- Footer -->
        <footer class="bg-theme-surface border-t border-theme-border mt-auto">
            <div class="max-w-4xl mx-auto px-4 py-4 text-center text-sm text-theme-text-secondary">
                &copy; {{ date('Y') }} {{ config('app.name', 'Movie Auditions') }}. All rights reserved.
            </div>
        </footer>
    </div>
@endsection
