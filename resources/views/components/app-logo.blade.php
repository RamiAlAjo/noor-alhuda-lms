@props([
    'sidebar' => false,
    'size' => 'md', // xs, sm, md, lg, xl
    'name' => null,
    'logo' => null,
])

@php
    $sizes = [
        'xs' => 'h-6 w-6',
        'sm' => 'h-8 w-8',
        'md' => 'h-10 w-10',
        'lg' => 'h-12 w-12',
        'xl' => 'h-16 w-16',
    ];
    $imgSize = $sizes[$size] ?? $sizes['md'];
    $logoPath = $logo ?? asset('inu-logo.jpg');
    $displayName = $name ?? __('lms.app_name');
@endphp

@if($sidebar)
    <flux:sidebar.brand name="{{ $displayName }}" {{ $attributes }}>
        <x-slot name="logo">
            <img
                src="{{ $logoPath }}"
                alt="{{ $name }} Logo"
                class="{{ $imgSize }} rounded-lg object-cover bg-white dark:bg-zinc-800 p-0.5"
                loading="lazy"
                decoding="async"
            >
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="{{ $displayName }}" {{ $attributes }}>
        <x-slot name="logo">
            <img
                src="{{ $logoPath }}"
                alt="{{ $name }} Logo"
                class="{{ $imgSize }} rounded-lg object-cover bg-white dark:bg-zinc-800 p-0.5"
                loading="lazy"
                decoding="async"
            >
        </x-slot>
    </flux:brand>
@endif
