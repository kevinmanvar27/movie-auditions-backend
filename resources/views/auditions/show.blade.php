@extends('layouts.admin')

@section('title', 'Audition Details')

@section('content')
<div class="p-4">
    <div class="max-w-2xl mx-auto">
        <div class="bg-theme-surface rounded-lg shadow-lg p-6 sm:p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-theme-text">{{ $audition->role }}</h1>
                    <p class="text-theme-text-secondary">Audition for {{ $audition->movie->title }}</p>
                </div>
                
                <span class="px-3 py-1 rounded-full text-xs font-medium 
                    @if($audition->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($audition->status == 'shortlisted') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($audition->status) }}
                </span>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-theme-text-secondary">Movie</h3>
                        <p class="text-theme-text">{{ $audition->movie->title }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-theme-text-secondary">Submitted On</h3>
                        <p class="text-theme-text">{{ $audition->created_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-theme-text-secondary">Applicant Name</h3>
                        <p class="text-theme-text">{{ $audition->applicant_name }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-theme-text-secondary">Status Updated</h3>
                        <p class="text-theme-text">{{ $audition->updated_at->format('F j, Y') }}</p>
                    </div>
                </div>
                
                @if($audition->notes)
                    <div>
                        <h3 class="text-sm font-medium text-theme-text-secondary mb-2">Notes</h3>
                        <div class="p-4 bg-theme-background rounded-md">
                            <p class="text-theme-text">{{ $audition->notes }}</p>
                        </div>
                    </div>
                @endif
                
                @if($audition->uploaded_videos)
                    <div>
                        <h3 class="text-sm font-medium text-theme-text-secondary mb-2">Uploaded Videos</h3>
                        <div class="space-y-3">
                            @foreach(json_decode($audition->uploaded_videos) as $videoUrl)
                                <div class="p-3 bg-theme-background rounded-md border border-theme-border">
                                    <video controls autoplay muted loop class="w-full max-h-64">
                                        <source src="{{ $videoUrl }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                    <div class="mt-2">
                                        <a href="{{ $videoUrl }}" target="_blank" class="text-theme-primary hover:underline text-sm">
                                            {{ basename($videoUrl) }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mt-8 flex flex-col sm:flex-row justify-between gap-4">
                <a href="{{ route('auditions.index') }}">
                    <x-button variant="secondary" size="md" class="w-full sm:w-auto">
                        Back to Auditions
                    </x-button>
                </a>
                
                @if($audition->status == 'pending')
                    <a href="#">
                        <x-button variant="primary" size="md" class="w-full sm:w-auto" disabled>
                            Edit Audition (Coming Soon)
                        </x-button>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection