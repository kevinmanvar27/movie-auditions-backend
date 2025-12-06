@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<style>
/* Toggle background */
.toggle-switch {
    background-color: #d1d5db; /* gray */
    width: 46px;
    height: 24px;
    border-radius: 9999px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s ease;
    border: none;
}

/* When toggle is enabled */
.toggle-switch.enabled {
    background-color: #22c55e; /* green */
}

/* Toggle circle (slider) */
.toggle-switch .toggle-slider {
    position: absolute;
    height: 18px;
    width: 18px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    transition: transform 0.3s ease;
    top: 3px;
    left: 3px;
}

/* When enabled, move the slider */
.toggle-switch.enabled .toggle-slider {
    transform: translateX(22px);
}

</style>
    <div class="p-4 sm:p-6">
        @include('components.error-message')
        
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-theme-text">Settings</h1>
            <p class="text-theme-text-secondary">Manage system settings and preferences</p>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-theme-border">
            <nav class="flex space-x-6">
                <button data-tab="general" class="tab-button active text-theme-primary border-theme-primary whitespace-nowrap py-3 px-4 border-b-2 font-semibold text-base rounded-t-lg transition-colors duration-200">
                    General
                </button>
                <button data-tab="payment" class="tab-button text-theme-text-secondary hover:text-theme-text whitespace-nowrap py-3 px-4 border-b-2 font-medium text-base rounded-t-lg transition-colors duration-200">
                    Payment
                </button>
                <button data-tab="notification" class="tab-button text-theme-text-secondary hover:text-theme-text whitespace-nowrap py-3 px-4 border-b-2 font-medium text-base rounded-t-lg transition-colors duration-200">
                    Notification
                </button>
            </nav>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- General Settings Tab -->
            <div id="tab-general" class="tab-content active">
                <!-- Site Configuration -->
                <div>
                    <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                        <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                            <h2 class="text-lg font-medium text-theme-text">Site Configuration</h2>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-4 md:p-6" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-6">
                                <!-- Logo Upload Section -->
                                <div class="space-y-4">
                                    <label class="block text-sm font-medium text-theme-text mb-2">Site Logo</label>
                                    <div class="flex flex-col md:flex-row items-start md:items-center gap-6 p-4 bg-theme-surface rounded-lg border border-theme-border">
                                        <!-- Current Logo Preview -->
                                        <div class="flex-shrink-0">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs text-theme-text-secondary mb-2">Current Logo</span>
                                                @if(!empty($settings['logo_path']))
                                                    <img src="{{ Storage::url($settings['logo_path']) }}" alt="Current Logo" class="object-contain rounded-lg border border-theme-border" height="128" width="128">
                                                @else
                                                    <div class="bg-gray-100 border-2 border-dashed border-theme-border rounded-lg w-16 h-16 flex items-center justify-center text-gray-400">
                                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- File Upload -->
                                        <div class="flex-1 w-full">
                                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-theme-border border-dashed rounded-lg transition-colors duration-200 hover:border-theme-primary">
                                                <div class="space-y-3 text-center">
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <div class="flex flex-col sm:flex-row text-sm text-gray-600 items-center justify-center gap-2">
                                                        <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-theme-primary hover:text-orange-600 focus-within:outline-none">
                                                            <span>Upload a file</span>
                                                            <input id="logo" name="logo" type="file" class="sr-only" accept="image/*">
                                                        </label>
                                                        <p class="hidden sm:block pl-1">or drag and drop</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        PNG, JPG, GIF up to 2MB
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="mt-2 text-sm text-theme-text-secondary">
                                                Recommended size: 160x160 pixels
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="site_name" class="block text-sm font-medium text-theme-text">Site Name</label>
                                    <input type="text" name="site_name" id="site_name" class="input-field mt-1 block w-full" value="{{ $settings['site_name'] ?? 'Movie Auditions Platform' }}" required>
                                </div>
                                
                                <div>
                                    <label for="site_description" class="block text-sm font-medium text-theme-text">Site Description</label>
                                    <textarea name="site_description" id="site_description" rows="3" class="input-field mt-1 block w-full">{{ $settings['site_description'] ?? 'A platform for managing movies and casting.' }}</textarea>
                                </div>
                                
                                <div>
                                    <label for="admin_email" class="block text-sm font-medium text-theme-text">Admin Email</label>
                                    <input type="email" name="admin_email" id="admin_email" class="input-field mt-1 block w-full" value="{{ $settings['admin_email'] ?? 'admin@example.com' }}" required>
                                </div>
                                
                                <!-- Razorpay Keys Section -->
                                <div>
                                    <label for="razorpay_key_id" class="block text-sm font-medium text-theme-text">Razorpay Key ID</label>
                                    <input type="text" name="razorpay_key_id" id="razorpay_key_id" class="input-field mt-1 block w-full" value="{{ $settings['razorpay_key_id'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Razorpay Key ID for payment processing</p>
                                </div>
                                
                                <div>
                                    <label for="razorpay_key_secret" class="block text-sm font-medium text-theme-text">Razorpay Key Secret</label>
                                    <input type="password" name="razorpay_key_secret" id="razorpay_key_secret" class="input-field mt-1 block w-full" value="{{ $settings['razorpay_key_secret'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Razorpay Key Secret for payment processing</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                                    <span class="loading-text ">Save Settings</span>
                                    <span class="loading-spinner hidden">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payment Settings Tab -->
            <div id="tab-payment" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Casting Director Section -->
                    <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                        <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                            <h2 class="text-lg font-medium text-theme-text">Casting Director</h2>
                            <p class="text-sm text-theme-text-secondary mt-1">Configure payment settings for casting directors</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-4 md:p-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-6">
                                <!-- Payment Requirement Toggle -->
                                <div>
                                    <label class="block text-sm font-medium text-theme-text mb-2">Payment Required</label>
                                    <div class="flex items-center">
                                        <button type="button" 
                                                class="toggle-switch {{ ($settings['casting_director_payment_required'] ?? '0') == '1' ? 'enabled' : '' }} relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                                data-setting="casting_director_payment_required">
                                            <span class="toggle-slider inline-block h-4 w-4 rounded-full bg-white duration-200 ease-in-out translate-x-1 {{ ($settings['casting_director_payment_required'] ?? '0') == '1' ? 'translate-x-6' : '' }}"></span>
                                        </button>
                                        <input type="hidden" name="casting_director_payment_required" value="{{ $settings['casting_director_payment_required'] ?? '0' }}">
                                        <span class="ml-3 text-sm text-theme-text-secondary">
                                            {{ ($settings['casting_director_payment_required'] ?? '0') == '1' ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-theme-text-secondary">Enable to require payment from casting directors when adding movies</p>
                                </div>
                                
                                <div>
                                    <label for="casting_director_amount" class="block text-sm font-medium text-theme-text">Fixed Amount ($)</label>
                                    <input type="number" name="casting_director_amount" id="casting_director_amount" class="input-field mt-1 block w-full" value="{{ $settings['casting_director_amount'] ?? '' }}" min="0" step="0.01">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Set a fixed payment amount for casting directors</p>
                                </div>
                                
                                <div>
                                    <label for="casting_director_percentage" class="block text-sm font-medium text-theme-text">Percentage (%)</label>
                                    <input type="number" name="casting_director_percentage" id="casting_director_percentage" class="input-field mt-1 block w-full" value="{{ $settings['casting_director_percentage'] ?? '' }}" min="0" max="100" step="0.01">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Set a percentage of earnings for casting directors</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                                    <span class="loading-text ">Save Payment Settings</span>
                                    <span class="loading-spinner hidden">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Audition User Section -->
                    <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                        <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                            <h2 class="text-lg font-medium text-theme-text">Audition User</h2>
                            <p class="text-sm text-theme-text-secondary mt-1">Configure payment settings for audition users</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-4 md:p-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-6">
                                <!-- Payment Requirement Toggle -->
                                <div>
                                    <label class="block text-sm font-medium text-theme-text mb-2">Payment Required</label>
                                    <div class="flex items-center">
                                        <button type="button" 
                                                class="toggle-switch {{ ($settings['audition_user_payment_required'] ?? '0') == '1' ? 'enabled' : '' }} relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none"
                                                data-setting="audition_user_payment_required">
                                            <span class="toggle-slider inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out translate-x-1 {{ ($settings['audition_user_payment_required'] ?? '0') == '1' ? 'translate-x-6' : '' }}"></span>
                                        </button>
                                        <input type="hidden" name="audition_user_payment_required" value="{{ $settings['audition_user_payment_required'] ?? '0' }}">
                                        <span class="ml-3 text-sm text-theme-text-secondary">
                                            {{ ($settings['audition_user_payment_required'] ?? '0') == '1' ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-theme-text-secondary">Enable to require payment from audition users when submitting auditions</p>
                                </div>
                                
                                <div>
                                    <label for="audition_user_amount" class="block text-sm font-medium text-theme-text">Fixed Amount ($)</label>
                                    <input type="number" name="audition_user_amount" id="audition_user_amount" class="input-field mt-1 block w-full" value="{{ $settings['audition_user_amount'] ?? '' }}" min="0" step="0.01">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Set a fixed payment amount for audition users</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                                    <span class="loading-text ">Save Payment Settings</span>
                                    <span class="loading-spinner hidden">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Notification Settings Tab -->
            <div id="tab-notification" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Firebase Configuration Section -->
                    <div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
                        <div class="px-4 py-3 md:px-6 md:py-4 border-b border-theme-border">
                            <h2 class="text-lg font-medium text-theme-text">Firebase Configuration</h2>
                            <p class="text-sm text-theme-text-secondary mt-1">Configure Firebase API keys for push notifications</p>
                        </div>
                        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-4 md:p-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="firebase_api_key" class="block text-sm font-medium text-theme-text">Firebase API Key</label>
                                    <input type="password" name="firebase_api_key" id="firebase_api_key" class="input-field mt-1 block w-full" value="{{ $settings['firebase_api_key'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Firebase API key for push notifications</p>
                                </div>
                                
                                <div>
                                    <label for="firebase_auth_domain" class="block text-sm font-medium text-theme-text">Firebase Auth Domain</label>
                                    <input type="text" name="firebase_auth_domain" id="firebase_auth_domain" class="input-field mt-1 block w-full" value="{{ $settings['firebase_auth_domain'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Firebase Auth Domain</p>
                                </div>
                                
                                <div>
                                    <label for="firebase_project_id" class="block text-sm font-medium text-theme-text">Firebase Project ID</label>
                                    <input type="text" name="firebase_project_id" id="firebase_project_id" class="input-field mt-1 block w-full" value="{{ $settings['firebase_project_id'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Firebase Project ID</p>
                                </div>
                                
                                <div>
                                    <label for="firebase_storage_bucket" class="block text-sm font-medium text-theme-text">Firebase Storage Bucket</label>
                                    <input type="text" name="firebase_storage_bucket" id="firebase_storage_bucket" class="input-field mt-1 block w-full" value="{{ $settings['firebase_storage_bucket'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Firebase Storage Bucket</p>
                                </div>
                                
                                <div>
                                    <label for="firebase_messaging_sender_id" class="block text-sm font-medium text-theme-text">Firebase Messaging Sender ID</label>
                                    <input type="text" name="firebase_messaging_sender_id" id="firebase_messaging_sender_id" class="input-field mt-1 block w-full" value="{{ $settings['firebase_messaging_sender_id'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Firebase Messaging Sender ID</p>
                                </div>
                                
                                <div>
                                    <label for="firebase_app_id" class="block text-sm font-medium text-theme-text">Firebase App ID</label>
                                    <input type="text" name="firebase_app_id" id="firebase_app_id" class="input-field mt-1 block w-full" value="{{ $settings['firebase_app_id'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Firebase App ID</p>
                                </div>
                                
                                <div>
                                    <label for="firebase_measurement_id" class="block text-sm font-medium text-theme-text">Firebase Measurement ID</label>
                                    <input type="text" name="firebase_measurement_id" id="firebase_measurement_id" class="input-field mt-1 block w-full" value="{{ $settings['firebase_measurement_id'] ?? '' }}">
                                    <p class="mt-1 text-sm text-theme-text-secondary">Your Firebase Measurement ID</p>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <button type="submit" class="btn btn-primary px-4 py-2" data-loading>
                                    <span class="loading-text ">Save Notification Settings</span>
                                    <span class="loading-spinner hidden">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active classes
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'text-theme-primary', 'border-theme-primary');
                        btn.classList.add('text-theme-text-secondary', 'hover:text-theme-text');
                    });
                    
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        content.classList.remove('active');
                    });
                    
                    // Add active classes to clicked tab
                    button.classList.add('active', 'text-theme-primary', 'border-theme-primary');
                    button.classList.remove('text-theme-text-secondary', 'hover:text-theme-text');
                    
                    // Show corresponding content
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(`tab-${tabId}`).classList.remove('hidden');
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });
            
            // Toggle switch functionality
            const toggleSwitches = document.querySelectorAll('.toggle-switch');
            toggleSwitches.forEach(switchEl => {
                switchEl.addEventListener('click', function() {
                    const isEnabled = this.classList.contains('enabled');
                    const input = this.nextElementSibling;
                    const statusText = this.parentElement.querySelector('span.text-sm');
                    
                    if (isEnabled) {
                        // Disable
                        this.classList.remove('enabled');
                        this.querySelector('.toggle-slider').classList.remove('translate-x-6');
                        this.querySelector('.toggle-slider').classList.add('translate-x-1');
                        input.value = '0';
                        statusText.textContent = 'Disabled';
                    } else {
                        // Enable
                        this.classList.add('enabled');
                        this.querySelector('.toggle-slider').classList.remove('translate-x-1');
                        this.querySelector('.toggle-slider').classList.add('translate-x-6');
                        input.value = '1';
                        statusText.textContent = 'Enabled';
                    }
                });
            });
        });
    </script>
@endsection