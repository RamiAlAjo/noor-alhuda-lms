@props([
    'status',
    'type' => 'success', // success, error, warning, info
])

@php
    $typeStyles = [
        'success' => [
            'bg' => 'bg-green-50 dark:bg-green-900/20',
            'border' => 'border-green-200 dark:border-green-800',
            'text' => 'text-green-700 dark:text-green-300',
            'icon' => 'check-circle',
            'iconColor' => 'text-green-600 dark:text-green-400',
        ],
        'error' => [
            'bg' => 'bg-red-50 dark:bg-red-900/20',
            'border' => 'border-red-200 dark:border-red-800',
            'text' => 'text-red-700 dark:text-red-300',
            'icon' => 'exclamation-circle',
            'iconColor' => 'text-red-600 dark:text-red-400',
        ],
        'warning' => [
            'bg' => 'bg-amber-50 dark:bg-amber-900/20',
            'border' => 'border-amber-200 dark:border-amber-800',
            'text' => 'text-amber-700 dark:text-amber-300',
            'icon' => 'exclamation-triangle',
            'iconColor' => 'text-amber-600 dark:text-amber-400',
        ],
        'info' => [
            'bg' => 'bg-blue-50 dark:bg-blue-900/20',
            'border' => 'border-blue-200 dark:border-blue-800',
            'text' => 'text-blue-700 dark:text-blue-300',
            'icon' => 'information-circle',
            'iconColor' => 'text-blue-600 dark:text-blue-400',
        ],
    ];
    $style = $typeStyles[$type] ?? $typeStyles['success'];
@endphp

@if ($status)
    <div
        {{
            $attributes->merge([
                'class' => "font-medium text-sm p-3 rounded-lg mb-4 flex items-center gap-2 {$style['bg']} {$style['border']} border",
                'x-data' => '{ shown: true }',
                'x-init' => 'setTimeout(() => { shown = false }, 5000)',
                'x-show' => 'shown',
                'x-transition' => 'transition ease-in-out duration-300'
            ])
        }}
        role="alert"
        aria-live="polite"
        x-cloak
    >
        <flux:icon name="{{ $style['icon'] }}" class="h-5 w-5 flex-shrink-0 {{ $style['iconColor'] }}" aria-hidden="true" />
        <span class="{{ $style['text'] }}">{{ $status }}</span>
    </div>
@endif
