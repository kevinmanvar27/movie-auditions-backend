@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="bg-theme-surface rounded-lg shadow-lg p-6 sm:p-8">
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl font-bold text-theme-text">{{ __('Login') }}</h1>
                    <p class="text-theme-text-secondary mt-2">Sign in to your account</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4 sm:mb-6">
                        <label for="email" class="block text-sm font-medium text-theme-text mb-2">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="w-full px-4 py-3 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text @error('email') border-theme-error @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-6">
                        <label for="password" class="block text-sm font-medium text-theme-text mb-2">{{ __('Password') }}</label>
                        <input id="password" type="password" class="w-full px-4 py-3 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text @error('password') border-theme-error @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center mb-4 sm:mb-6">
                        <input class="rounded border-theme-border text-theme-primary focus:ring-theme-primary" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="ml-2 text-sm text-theme-text" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-theme-primary hover:text-[#e05e00]" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif

                        <x-button type="submit" variant="primary" size="md" class="w-full sm:w-auto">
                            {{ __('Login') }}
                        </x-button>
                    </div>
                </form>

                @if (Route::has('register'))
                    <div class="mt-6 sm:mt-8 text-center text-sm text-theme-text-secondary">
                        <p>Don't have an account? <a href="{{ route('register') }}" class="text-theme-primary hover:text-[#e05e00] font-medium">Register</a></p>
                    </div>
                @endif
            </div>
            
            <!-- Theme Toggle -->
            <div class="mt-6 flex justify-center">
                @include('components.theme-toggle', ['class' => 'p-2 rounded-full hover:bg-theme-secondary focus:outline-none focus:ring-2 focus:ring-theme-primary'])
            </div>
        </div>
    </div>
</div>
@endsection