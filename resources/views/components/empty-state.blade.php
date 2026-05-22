{{--
    Empty State Component
--}}
@props([
    'icon' => 'document-text',
    'title' => 'Nothing here yet',
    'description' => null,
    'action' => null,
])

<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-300 bg-white px-6 py-16 text-center dark:border-neutral-700 dark:bg-neutral-800/50">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-700">
        <flux:icon name="{{ $icon }}" class="size-8 text-neutral-400 dark:text-neutral-500" />
    </div>

    <h3 class="mt-5 text-lg font-semibold text-neutral-900 dark:text-white">
        {{ $title }}
    </h3>

    @if ($description)
        <p class="mt-2 max-w-sm text-sm text-neutral-600 dark:text-neutral-400">
            {{ $description }}
        </p>
    @endif

    @if ($action)
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
