@props(['title' => null, 'footer' => null])

<div class="bg-theme-surface rounded-lg shadow border border-theme-border overflow-hidden">
    @if ($title)
        <div class="px-6 py-4 border-b border-theme-border">
            <h3 class="text-lg font-medium text-theme-text">{{ $title }}</h3>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-theme-background border-t border-theme-border">
            {{ $footer }}
        </div>
    @endif
</div>