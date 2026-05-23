@props([
    'loadingText' => 'Saving...',
    'variant' => 'primary',
    'size' => 'md', // sm, md, lg
    'full' => false,
    'confirm' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-lg font-medium transition-all disabled:cursor-not-allowed disabled:opacity-75 shadow-sm hover:shadow-md active:scale-[0.985]';

    $sizes = [
        'sm'  => 'px-3 py-1.5 text-xs',
        'md'  => 'px-5 py-2.5 text-sm',
        'lg'  => 'px-6 py-3 text-base',
    ];

    $variants = [
        'primary'   => 'bg-[var(--color-accent)] text-white hover:bg-[var(--color-accent)]/90',
        'secondary' => 'bg-neutral-200 text-neutral-800 hover:bg-neutral-300 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700',
        'ghost'     => 'bg-transparent text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800',
    ];

    $buttonClass = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);

    if ($full) {
        $buttonClass .= ' w-full';
    }
@endphp

<button
    type="submit"
    x-data="{ loading: false }"
    @click="@if($confirm)if(confirm('{{ addslashes($confirm) }}')){ setTimeout(() => loading = true, 10); }else{return false;}@else setTimeout(() => loading = true, 10); @endif"
    :disabled="loading"
    class="{{ $buttonClass }} {{ $attributes->get('class') }}"
    {{ $attributes->except(['class', 'onclick']) }}
>
    <!-- Spinner -->
    <div x-show="loading" x-cloak class="size-4 animate-spin rounded-full border-2 border-current border-t-transparent flex-shrink-0"></div>

    <!-- Content (icon + text) -->
    <span x-show="!loading" class="flex items-center gap-2">
        {{ $slot }}
    </span>

    <!-- Loading text -->
    <span x-show="loading" x-cloak>{{ $loadingText }}</span>
</button>
