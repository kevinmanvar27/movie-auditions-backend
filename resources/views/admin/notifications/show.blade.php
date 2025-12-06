@extends('layouts.admin')

@section('title', 'Notification Details')

@section('content')
<div class="p-4 sm:p-6">
    @include('components.error-message')
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-theme-text">Notification Details</h1>
        <p class="text-theme-text-secondary">View detailed information about this notification</p>
    </div>
    
    <div class="mb-6">
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Notifications
        </a>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                    <h2 class="text-lg font-medium text-theme-text">Notification Content</h2>
                </div>
                
                <div class="p-4 md:p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-theme-text-secondary">Title</label>
                            <p class="mt-1 text-lg font-medium text-theme-text">{{ $notification->title }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-theme-text-secondary">Message</label>
                            <p class="mt-1 text-theme-text">{{ $notification->message }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-theme-text-secondary">Sent At</label>
                            <p class="mt-1 text-theme-text">{{ $notification->sent_at ? $notification->sent_at->format('F j, Y \a\t g:i A') : 'Not sent yet' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-theme-text-secondary">Status</label>
                            @if($notification->status === 'sent')
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Sent Successfully
                                </span>
                            @elseif($notification->status === 'pending')
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @else
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Failed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                    <h2 class="text-lg font-medium text-theme-text">Recipient Filters</h2>
                </div>
                
                <div class="p-4 md:p-6">
                    <div class="space-y-4">
                        @if(!empty($notification->filters_applied['target_roles']))
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Target Roles</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($notification->filters_applied['target_roles'] as $roleId)
                                        @php
                                            $role = DB::table('roles')->find($roleId);
                                        @endphp
                                        @if($role)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $role->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($notification->filters_applied['target_movies']))
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Target Movies</label>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @foreach($notification->filters_applied['target_movies'] as $movieId)
                                        @php
                                            $movie = App\Models\Movie::find($movieId);
                                        @endphp
                                        @if($movie)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $movie->title }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($notification->filters_applied['gender']))
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Gender Criteria</label>
                                <p class="mt-1 text-theme-text">{{ ucfirst($notification->filters_applied['gender']) }}</p>
                            </div>
                        @endif
                        
                        @if(!empty($notification->filters_applied['min_age']) || !empty($notification->filters_applied['max_age']))
                            <div>
                                <label class="block text-sm font-medium text-theme-text-secondary">Age Range</label>
                                <p class="mt-1 text-theme-text">
                                    @if(!empty($notification->filters_applied['min_age']) && !empty($notification->filters_applied['max_age']))
                                        {{ $notification->filters_applied['min_age'] }} - {{ $notification->filters_applied['max_age'] }} years
                                    @elseif(!empty($notification->filters_applied['min_age']))
                                        {{ $notification->filters_applied['min_age'] }}+ years
                                    @elseif(!empty($notification->filters_applied['max_age']))
                                        Up to {{ $notification->filters_applied['max_age'] }} years
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-6 bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                    <h2 class="text-lg font-medium text-theme-text">Delivery Statistics</h2>
                </div>
                
                <div class="p-4 md:p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-theme-text">Total Recipients</span>
                            <span class="font-medium text-theme-text">{{ $notification->recipient_count }} users</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-theme-text">Successfully Delivered</span>
                            <span class="font-medium text-green-600">{{ $notification->recipient_count }} users</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-theme-text">Failed Deliveries</span>
                            <span class="font-medium text-red-600">0 users</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection