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
                        <div class="relative">
                            <input id="password" type="password" class="w-full px-4 py-3 pr-12 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text @error('password') border-theme-error @enderror" name="password" required autocomplete="current-password">
                            <button type="button" id="togglePassword" class="absolute top-0 right-0 h-full px-3 flex items-center text-gray-500">
                                <svg id="showIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="hideIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
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

                        <button type="submit" class="w-full sm:w-auto px-4 py-2 sm:px-6 sm:py-3 bg-theme-primary hover:bg-[#e05e00] text-theme-background font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-theme-primary">
                            {{ __('Login') }}
                        </button>
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

@section('scripts')
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const showIcon = document.querySelector('#showIcon');
    const hideIcon = document.querySelector('#hideIcon');

    togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the eye icons
        showIcon.classList.toggle('hidden');
        hideIcon.classList.toggle('hidden');
    });
</script>
@endsection
@endsection