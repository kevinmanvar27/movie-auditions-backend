@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="p-4 sm:p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Dashboard</h1>
            <p class="text-theme-text-secondary">Welcome to your admin dashboard</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-6">
            <!-- Stats Cards -->
            <div class="bg-theme-surface rounded-lg shadow border border-theme-border p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-md bg-theme-primary flex items-center justify-center p-2">
                            <svg class="h-6 w-6 text-theme-background" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-6 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-theme-text-secondary truncate">Total Movies</dt>
                            <dd class="text-2xl font-bold text-theme-text">12</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-theme-surface rounded-lg shadow border border-theme-border p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-md bg-theme-primary flex items-center justify-center p-2">
                            <svg class="h-6 w-6 text-theme-background" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-6 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-theme-text-secondary truncate">Total Auditions</dt>
                            <dd class="text-2xl font-bold text-theme-text">142</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-theme-surface rounded-lg shadow border border-theme-border p-4 md:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-md bg-theme-primary flex items-center justify-center p-2">
                            <svg class="h-6 w-6 text-theme-background" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-6 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-theme-text-secondary truncate">Successful Auditions</dt>
                            <dd class="text-2xl font-bold text-theme-text">24</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
            <!-- Recent Activity -->
            <div class="bg-theme-surface rounded-lg shadow border border-theme-border">
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                    <h2 class="text-lg font-medium text-theme-text">Recent Activity</h2>
                </div>
                <div class="p-4 md:p-6">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-theme-secondary flex items-center justify-center">
                                    <svg class="h-4 w-4 text-theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-theme-text">New movie "The Adventure" was added</p>
                                <p class="text-xs text-theme-text-secondary">2 hours ago</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-theme-secondary flex items-center justify-center">
                                    <svg class="h-4 w-4 text-theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-theme-text">John Doe submitted an audition</p>
                                <p class="text-xs text-theme-text-secondary">4 hours ago</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-theme-secondary flex items-center justify-center">
                                    <svg class="h-4 w-4 text-theme-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-theme-text">Audition status updated for "The Mystery"</p>
                                <p class="text-xs text-theme-text-secondary">1 day ago</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-theme-surface rounded-lg shadow border border-theme-border">
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                    <h2 class="text-lg font-medium text-theme-text">Quick Actions</h2>
                </div>
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <button class="flex flex-col items-center justify-center p-4 bg-theme-background rounded-lg border border-theme-border hover:bg-theme-secondary transition-colors">
                            <div class="h-10 w-10 rounded-md bg-theme-primary flex items-center justify-center mb-2">
                                <svg class="h-5 w-5 text-theme-background" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-theme-text">Add Movie</span>
                        </button>
                        <button class="flex flex-col items-center justify-center p-4 bg-theme-background rounded-lg border border-theme-border hover:bg-theme-secondary transition-colors">
                            <div class="h-10 w-10 rounded-md bg-theme-primary flex items-center justify-center mb-2">
                                <svg class="h-5 w-5 text-theme-background" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-theme-text">View Auditions</span>
                        </button>
                        <button class="flex flex-col items-center justify-center p-4 bg-theme-background rounded-lg border border-theme-border hover:bg-theme-secondary transition-colors">
                            <div class="h-10 w-10 rounded-md bg-theme-primary flex items-center justify-center mb-2">
                                <svg class="h-5 w-5 text-theme-background" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-theme-text">Manage Users</span>
                        </button>
                        <button class="flex flex-col items-center justify-center p-4 bg-theme-background rounded-lg border border-theme-border hover:bg-theme-secondary transition-colors">
                            <div class="h-10 w-10 rounded-md bg-theme-primary flex items-center justify-center mb-2">
                                <svg class="h-5 w-5 text-theme-background" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-theme-text">Settings</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection