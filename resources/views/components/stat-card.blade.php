{{--
    Stat Card Component
    Usage:
    <x-stat-card
        icon="academic-cap"
        label="Total Courses"
        value="142"
        change="+12%"
        color="blue"
        :href="route('admin.courses.index')"
    />
--}}
@props([
    'icon' => null,
    'label',
    'value',
    'change' => null,
    'color' => 'indigo',
    'href' => null,
    'description' => null,
])

@php
    $colorClasses = [
        'indigo'   => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
        'blue'     => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        'green'    => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        'emerald'  => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
        'amber'    => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
        'orange'   => 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
        'red'      => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        'purple'   => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        'pink'     => 'bg-pink-100 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400',
        'teal'     => 'bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400',
        'cyan'     => 'bg-cyan-100 text-cyan-600 dark:bg-cyan-900/30 dark:text-cyan-400',
    ];
    $iconClass = $colorClasses[$color] ?? $colorClasses['indigo'];
@endphp

@php
    $cardClasses = 'group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 dark:border-neutral-700 dark:bg-neutral-800';
    if ($href) {
        $cardClasses .= ' cursor-pointer';
    }
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $cardClasses }}">
@else
    <div class="{{ $cardClasses }}">
@endif
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $label }}</p>

            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-3xl font-semibold tracking-tight text-neutral-900 dark:text-white">
                    {{ $value }}
                </p>

                @if ($change)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                        {{ str_starts_with($change, '+') ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ $change }}
                    </span>
                @endif
            </div>

            @if ($description)
                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ $description }}</p>
            @endif
        </div>

        @if ($icon)
            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl {{ $iconClass }} transition-transform group-hover:scale-105">
                <flux:icon name="{{ $icon }}" class="size-5" />
            </div>
        @endif
    </div>
@if ($href)
    </a>
@else
    </div>
@endif
