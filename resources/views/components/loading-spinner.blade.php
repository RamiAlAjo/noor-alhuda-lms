@props([
    'size' => 'md',
    'color' => 'primary',
    'text' => null,
    'overlay' => false,
])

@php
    $sizeClasses = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-12 h-12',
        '2xl' => 'w-16 h-16',
    ];

    $colorClasses = [
        'primary' => 'text-[var(--color-accent)]',
        'white' => 'text-white',
        'gray' => 'text-zinc-500 dark:text-zinc-400',
        'success' => 'text-green-500 dark:text-green-400',
        'warning' => 'text-amber-500 dark:text-amber-400',
        'danger' => 'text-red-500 dark:text-red-400',
    ];
@endphp

@if($overlay)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm transition-opacity duration-200"
        role="status"
        aria-live="polite"
        aria-label="{{ $text ?? __('Loading') }}"
        aria-busy="true"
    >
        <div class="flex flex-col items-center justify-center gap-3 p-6 rounded-xl bg-white dark:bg-zinc-800 shadow-xl border border-zinc-200 dark:border-zinc-700">
            <svg
                class="animate-spin {{ $sizeClasses[$size] ?? $sizeClasses['md'] }} {{ $colorClasses[$color] ?? $colorClasses['primary'] }}"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>

            @if($text)
                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $text }}</p>
            @endif
        </div>

        <span class="sr-only">{{ $text ?? __('Loading...') }}</span>
    </div>
@else
    <div
        class="flex flex-col items-center justify-center gap-3"
        role="status"
        aria-live="polite"
        aria-label="{{ $text ?? __('Loading') }}"
        aria-busy="true"
    >
        <svg
            class="animate-spin {{ $sizeClasses[$size] ?? $sizeClasses['md'] }} {{ $colorClasses[$color] ?? $colorClasses['primary'] }}"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            ></circle>
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
        </svg>

        @if($text)
            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $text }}</p>
        @endif

        <span class="sr-only">{{ $text ?? __('Loading...') }}</span>
    </div>
@endif
