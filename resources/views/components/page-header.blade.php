{{--
    Page Header Component - Consistent header for all pages
    Usage:
    <x-page-header
        title="Enrollment Management"
        description="Manage student course enrollments"
        :actions="$slot"
    />
--}}
@props([
    'title',
    'description' => null,
    'actions' => null,
])

<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0 flex-1">
        <h1 class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white">
            {{ $title }}
        </h1>
        @if ($description)
            <p class="mt-1.5 text-[15px] text-neutral-600 dark:text-neutral-400">
                {{ $description }}
            </p>
        @endif
    </div>

    @if ($actions || $slot->isNotEmpty())
        <div class="flex flex-shrink-0 items-center gap-3">
            {{ $actions ?? $slot }}
        </div>
    @endif
</div>
