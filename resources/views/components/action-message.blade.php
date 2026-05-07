@props([
    'on',
    'timeout' => 3000,
])

<div
    x-data="{
        shown: false,
        timeout: null,
        show() {
            clearTimeout(this.timeout);
            this.shown = true;
            this.timeout = setTimeout(() => { this.shown = false }, {{ $timeout }});
        }
    }"
    x-on:{{ $on }}.window="show()"
    x-show="shown"
    x-transition:leave="transition ease-in duration-{{ $timeout }}"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none"
    role="status"
    aria-live="polite"
    aria-atomic="true"
    {{ $attributes->merge(['class' => 'text-sm font-medium text-green-600 dark:text-green-400']) }}
>
    <div class="flex items-center gap-2">
        <flux:icon name="check-circle" class="h-4 w-4 flex-shrink-0" aria-hidden="true" />
        <span>{{ $slot->isEmpty() ? __('Saved successfully.') : $slot }}</span>
    </div>
</div>
