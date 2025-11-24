@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'disabled' => false])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed';

$variantClasses = [
    'primary' => 'bg-theme-primary hover:bg-[#e05e00] text-theme-background focus:ring-theme-primary border border-transparent',
    'secondary' => 'bg-theme-secondary hover:bg-gray-300 text-theme-text border border-theme-border focus:ring-theme-border',
    'outline' => 'bg-transparent border border-theme-primary text-theme-primary hover:bg-theme-primary hover:text-theme-background focus:ring-theme-primary',
    'danger' => 'bg-theme-error hover:bg-[#d32f2f] text-theme-background focus:ring-theme-error border border-transparent',
];

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<button 
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge([
        'class' => $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . $disabledClasses,
        'aria-disabled' => $disabled ? 'true' : null
    ]) }}
>
    {{ $slot }}
</button>