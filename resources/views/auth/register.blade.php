@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex-grow flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="bg-theme-surface rounded-lg shadow-lg p-6 sm:p-8">
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl font-bold text-theme-text">{{ __('Register') }}</h1>
                    <p class="text-theme-text-secondary mt-2">Create a new account</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4 sm:mb-6">
                        <label for="name" class="block text-sm font-medium text-theme-text mb-2">{{ __('Name') }}</label>
                        <input id="name" type="text" class="w-full px-4 py-3 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text @error('name') border-theme-error @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-6">
                        <label for="email" class="block text-sm font-medium text-theme-text mb-2">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="w-full px-4 py-3 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text @error('email') border-theme-error @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-6">
                        <label for="password" class="block text-sm font-medium text-theme-text mb-2">{{ __('Password') }}</label>
                        <input id="password" type="password" class="w-full px-4 py-3 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text @error('password') border-theme-error @enderror" name="password" required autocomplete="new-password">
                        @error('password')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4 sm:mb-6">
                        <label for="password-confirm" class="block text-sm font-medium text-theme-text mb-2">{{ __('Confirm Password') }}</label>
                        <input id="password-confirm" type="password" class="w-full px-4 py-3 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <!-- Role Selection -->
                    <div class="mb-4 sm:mb-6">
                        <label for="role_id" class="block text-sm font-medium text-theme-text mb-2">User Role</label>
                        <select id="role_id" class="w-full px-4 py-3 border border-theme-border rounded-lg focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text" name="role_id" required>
                            <option value="">Select Role</option>
                            @foreach(App\Models\Role::all() as $role)
                                @if($role->name !== 'Super Admin') {{-- Don't allow users to register as Super Admin --}}
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <a class="text-sm text-theme-primary hover:text-[#e05e00]" href="{{ route('login') }}">
                            {{ __('Already have an account?') }}
                        </a>

                        <x-button type="submit" variant="primary" size="md" class="w-full sm:w-auto">
                            {{ __('Register') }}
                        </x-button>
                    </div>
                </form>
            </div>
            
            <!-- Theme Toggle -->
            <div class="mt-6 flex justify-center">
                @include('components.theme-toggle', ['class' => 'p-2 rounded-full hover:bg-theme-secondary focus:outline-none focus:ring-2 focus:ring-theme-primary'])
            </div>
        </div>
    </div>
</div>
@endsection