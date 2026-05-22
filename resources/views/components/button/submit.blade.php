@props([
    'loadingText' => 'Saving...',
    'variant' => 'primary',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium transition-all disabled:cursor-not-allowed disabled:opacity-75 shadow-sm hover:shadow-md active:scale-[0.985]';

    $variants = [
        'primary'   => 'bg-[var(--color-accent)] text-white hover:bg-[var(--color-accent)]/90',
        'secondary' => 'bg-neutral-200 text-neutral-800 hover:bg-neutral-300 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700',
    ];

    $buttonClass = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button
    type="submit"
    x-data="{ loading: false }"
    @click="loading = true"
    :disabled="loading"
    class="{{ $buttonClass }} {{ $attributes->get('class') }}"
    {{ $attributes->except('class') }}
>
    <!-- Spinner -->
    <svg x-show="loading" x-cloak class="size-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    <!-- Icon + Text (hidden while loading) -->
    <span x-show="!loading" class="flex items-center gap-2">
        {{ $slot }}
    </span>

    <!-- Loading text -->
    <span x-show="loading" x-cloak>{{ $loadingText }}</span>
</button>
