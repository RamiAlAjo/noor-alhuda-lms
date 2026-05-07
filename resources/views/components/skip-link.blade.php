@props([
    'target' => 'main-content',
    'text' => null,
    'position' => 'top-left', // top-left, top-center, top-right
])

@php
    $positionClasses = [
        'top-left' => 'focus:top-4 focus:left-4',
        'top-center' => 'focus:top-4 focus:left-1/2 focus:-translate-x-1/2',
        'top-right' => 'focus:top-4 focus:right-4',
    ];
@endphp

<a
    href="#{{ $target }}"
    class="sr-only focus:not-sr-only focus:absolute {{ $positionClasses[$position] ?? $positionClasses['top-left'] }} focus:z-[9999] focus:px-4 focus:py-2 focus:bg-[var(--color-accent)] focus:text-white focus:rounded-lg focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--color-accent)] focus:transition-all focus:duration-200"
    role="link"
    tabindex="0"
>
    {{ $text ?? __('Skip to main content') }}
</a>
