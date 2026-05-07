@props([
    'id' => uniqid(),
    'color' => 'zinc', // zinc, gray, slate
    'opacity' => 'medium', // low, medium, high
])

@php
    $colorStyles = [
        'zinc' => 'stroke-zinc-400 dark:stroke-zinc-500',
        'gray' => 'stroke-gray-400 dark:stroke-gray-500',
        'slate' => 'stroke-slate-400 dark:stroke-slate-500',
    ];

    $opacityStyles = [
        'low' => 'opacity-25',
        'medium' => 'opacity-40',
        'high' => 'opacity-60',
    ];
@endphp

<svg
    {{ $attributes->merge(['class' => (isset($colorStyles[$color]) ? $colorStyles[$color] : $colorStyles['zinc']) . ' ' . (isset($opacityStyles[$opacity]) ? $opacityStyles[$opacity] : $opacityStyles['medium'])]) }}
    fill="none"
    role="img"
    aria-label="{{ __('Placeholder pattern') }}"
>
    <defs>
        <pattern id="pattern-{{ $id }}" x="0" y="0" width="8" height="8" patternUnits="userSpaceOnUse">
            <path d="M-1 5L5 -1M3 9L8.5 3.5" stroke-width="0.5"></path>
        </pattern>
    </defs>
    <rect stroke="none" fill="url(#pattern-{{ $id }})" width="100%" height="100%"></rect>
</svg>
