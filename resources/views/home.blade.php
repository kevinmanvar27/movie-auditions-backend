@extends('layouts.app')

@section('content')
<div class="flex-grow flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <div class="bg-theme-surface rounded-lg shadow-lg p-6 sm:p-8">
            <div class="text-center mb-6 sm:mb-8">
                <h1 class="text-2xl font-bold text-theme-text">{{ __('Dashboard') }}</h1>
                <p class="text-theme-text-secondary mt-2">Welcome to your dashboard</p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-theme-success bg-opacity-20 border border-theme-success rounded-lg text-theme-success">
                    {{ session('status') }}
                </div>
            @endif

            <div class="text-center">
                <p class="text-theme-text mb-6">{{ __('You are logged in!') }}</p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('admin.dashboard') }}">
                        <x-button variant="primary" size="md" class="w-full sm:w-auto">
                            Go to Admin Dashboard
                        </x-button>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button type="submit" variant="secondary" size="md" class="w-full sm:w-auto">
                            {{ __('Logout') }}
                        </x-button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Theme Toggle -->
        <div class="mt-6 flex justify-center">
            @include('components.theme-toggle', ['class' => 'p-2 rounded-full hover:bg-theme-secondary focus:outline-none focus:ring-2 focus:ring-theme-primary'])
        </div>
    </div>
</div>
@endsection