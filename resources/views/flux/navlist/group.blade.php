@props([
    'expandable' => false,
    'expanded' => true,
    'heading' => null,
    'id' => 'navlist-group-' . uniqid(),
])

<?php if ($expandable && $heading): ?>

<ui-disclosure
    {{ $attributes->class('group/disclosure') }}
    @if ($expanded === true) open @endif
    data-flux-navlist-group
    role="navigation"
    aria-label="{{ $heading }}"
>
    <button
        type="button"
        id="{{ $id }}-button"
        class="group/disclosure-button mb-[2px] flex h-10 w-full items-center rounded-lg text-zinc-500 hover:bg-zinc-800/5 hover:text-zinc-800 lg:h-8 dark:text-zinc-400 dark:hover:bg-white/[7%] dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:ring-offset-1 dark:focus:ring-offset-zinc-900 transition-colors duration-150"
        aria-expanded="{{ $expanded ? 'true' : 'false' }}"
        aria-controls="{{ $id }}-content"
    >
        <div class="ps-3 pe-4" aria-hidden="true">
            <flux:icon.chevron-down class="hidden size-3! group-data-open/disclosure-button:block transition-transform duration-200" />
            <flux:icon.chevron-right class="block size-3! group-data-open/disclosure-button:hidden transition-transform duration-200" />
        </div>

        <span class="text-sm font-medium leading-none">{{ $heading }}</span>
    </button>

    <div
        id="{{ $id }}-content"
        class="relative hidden space-y-[2px] ps-7 data-open:block"
        @if ($expanded === true) data-open @endif
        role="group"
        aria-labelledby="{{ $id }}-button"
    >
        <div class="absolute inset-y-[3px] start-0 ms-4 w-px bg-zinc-200 dark:bg-zinc-700" aria-hidden="true"></div>

        {{ $slot }}
    </div>
</ui-disclosure>

<?php elseif ($heading): ?>

<div {{ $attributes->class('block space-y-[2px]') }} role="group" aria-label="{{ $heading }}">
    <div class="px-1 py-2">
        <div class="text-xs font-medium leading-none text-zinc-400 dark:text-zinc-500 uppercase tracking-wide" id="{{ $id }}-heading">{{ $heading }}</div>
    </div>

    <div role="group" aria-labelledby="{{ $id }}-heading">
        {{ $slot }}
    </div>
</div>

<?php else: ?>

<div {{ $attributes->class('block space-y-[2px]') }} role="group">
    {{ $slot }}
</div>

<?php endif; ?>
