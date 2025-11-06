@props(['variant' => 'primary', 'size' => 'md'])

@php
$baseClasses = 'inline-flex items-center justify-center rounded-lg font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';
$variantClasses = [
    'primary' => 'bg-theme-primary hover:bg-[#e05e00] text-theme-background focus:ring-theme-primary',
    'secondary' => 'bg-theme-secondary hover:bg-gray-300 text-theme-text border border-theme-border focus:ring-theme-border',
    'outline' => 'bg-transparent border border-theme-primary text-theme-primary hover:bg-theme-primary hover:text-theme-background focus:ring-theme-primary',
    'danger' => 'bg-theme-error hover:bg-[#d32f2f] text-theme-background focus:ring-theme-error',
];
$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];
@endphp

<button {{ $attributes->merge(['class' => $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size]]) }}>
    {{ $slot }}
</button>