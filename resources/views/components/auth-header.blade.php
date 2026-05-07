@props([
    'title',
    'description',
    'align' => 'center', // center, left, right
])

@php
    $alignClasses = [
        'center' => 'text-center items-center',
        'left' => 'text-left items-start',
        'right' => 'text-right items-end',
    ];
@endphp

<div class="flex w-full flex-col {{ $alignClasses[$align] ?? $alignClasses['center'] }} gap-2" role="heading" aria-level="1">
    <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100">{{ $title }}</flux:heading>
    @if($description)
        <flux:subheading class="text-zinc-600 dark:text-zinc-400">{{ $description }}</flux:subheading>
    @endif
</div>
