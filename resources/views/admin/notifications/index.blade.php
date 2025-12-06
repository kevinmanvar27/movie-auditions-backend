@extends('layouts.admin')

@section('title', 'Notification Management')

@section('content')
<div class="p-4 sm:p-6">
    @include('components.error-message')
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between">    
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Notification Management</h1>
            <p class="text-theme-text-secondary">View and manage system notifications</p>
        </div>
        
        <div class="mb-6">
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary  py-2 px-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Send New Notification
            </a>
        </div>
    </div>
    
    <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
        <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
            <h2 class="text-lg font-medium text-theme-text">Sent Notifications</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-theme-border">
                <thead class="bg-theme-surface">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Recipients</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Sent At</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-theme-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-theme-border">
                    @if(isset($notifications) && count($notifications) > 0)
                        @foreach($notifications as $notification)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-theme-text">{{ $notification->title }}</div>
                                <div class="text-sm text-theme-text-secondary truncate max-w-xs">{{ Str::limit($notification->message, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-theme-text">{{ $notification->recipient_count }} users</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-theme-text">
                                {{ $notification->sent_at ? $notification->sent_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($notification->status === 'sent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Sent
                                    </span>
                                @elseif($notification->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.notifications.show', $notification->id) }}" class="text-theme-primary hover:text-orange-600">View Details</a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-theme-text-secondary">
                                No notifications sent yet.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection