@props(['label' => null, 'id' => null, 'name' => null, 'value' => '', 'type' => 'text', 'required' => false])

<div class="mb-4">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-theme-text mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-theme-error">*</span>
            @endif
        </label>
    @endif

    <input 
        id="{{ $id }}" 
        name="{{ $name }}" 
        type="{{ $type }}" 
        value="{{ $value }}"
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-theme-border rounded-md focus:ring-2 focus:ring-theme-primary focus:border-theme-primary bg-theme-background text-theme-text']) }}
    >
    
    @error($name)
        <p class="mt-1 text-sm text-theme-error">{{ $message }}</p>
    @enderror
</div>