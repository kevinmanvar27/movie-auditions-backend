<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Movie Auditions') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @yield('head')
</head>
<body class="bg-theme-background text-theme-text min-h-screen">
    <div class="min-h-screen flex flex-col">
        <!-- Topbar -->
        <header class="bg-theme-surface border-b border-theme-border shadow-sm sticky top-0 z-10">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="mr-4 text-theme-text lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-xl font-bold text-theme-primary">Movie Auditions Admin</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    @include('components.theme-toggle', ['class' => 'p-2 rounded-full hover:bg-theme-secondary focus:outline-none focus:ring-2 focus:ring-theme-primary'])
                    
                    <!-- User Profile -->
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center space-x-2 focus:outline-none">
                            <div class="h-8 w-8 rounded-full bg-theme-primary flex items-center justify-center text-theme-background font-medium">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:inline text-sm font-medium text-theme-text">{{ Auth::user()->name }}</span>
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-theme-surface rounded-md shadow-lg py-1 border border-theme-border hidden z-50">
                            <a href="#" class="block px-4 py-2 text-sm text-theme-text hover:bg-theme-secondary">Your Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-theme-text hover:bg-theme-secondary">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-theme-text hover:bg-theme-secondary">
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside id="sidebar" class="bg-theme-surface border-r border-theme-border w-64 fixed lg:static h-full lg:h-auto z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
                <div class="flex flex-col h-full">
                    <div class="p-4 border-b border-theme-border">
                        <h2 class="text-lg font-semibold text-theme-text">Navigation</h2>
                    </div>
                    <nav class="flex-1 overflow-y-auto py-4">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-theme-text hover:bg-theme-secondary border-l-4 border-transparent hover:border-theme-primary">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Dashboard
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-theme-text hover:bg-theme-secondary border-l-4 border-transparent hover:border-theme-primary">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Movies
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-theme-text hover:bg-theme-secondary border-l-4 border-transparent hover:border-theme-primary">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Auditions
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-theme-text hover:bg-theme-secondary border-l-4 border-transparent hover:border-theme-primary">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Users
                        </a>
                        <a href="#" class="flex items-center px-4 py-3 text-theme-text hover:bg-theme-secondary border-l-4 border-transparent hover:border-theme-primary">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Settings
                        </a>
                    </nav>
                    <div class="p-4 border-t border-theme-border">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-3 text-theme-text hover:bg-theme-secondary rounded-md">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Overlay for mobile -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden"></div>

            <!-- Main Content -->
            <main class="flex-1 pt-16 lg:pt-0">
                @yield('content')
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-theme-surface border-t border-theme-border py-4 px-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-theme-text-secondary">
                    &copy; {{ date('Y') }} Movie Auditions Platform. All rights reserved.
                </p>
                <div class="mt-2 md:mt-0">
                    <p class="text-sm text-theme-text-secondary">
                        Version 1.0.0
                    </p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                });
            }
            
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                });
            }
            
            // User menu dropdown
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>