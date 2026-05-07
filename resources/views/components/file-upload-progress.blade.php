@props([
    'progress' => 0,
    'fileName' => null,
    'fileSize' => null,
    'status' => 'uploading', // uploading, success, error
    'showPercent' => true,
    'cancelable' => false,
    'id' => 'file-upload-' . uniqid(),
])

@php
    $statusColors = [
        'uploading' => 'bg-[var(--color-accent)]',
        'success' => 'bg-green-500',
        'error' => 'bg-red-500',
    ];

    $statusIcons = [
        'uploading' => 'cloud-arrow-up',
        'success' => 'check-circle',
        'error' => 'exclamation-circle',
    ];

    $statusLabels = [
        'uploading' => __('Uploading'),
        'success' => __('Completed'),
        'error' => __('Failed'),
    ];
@endphp

<div
    id="{{ $id }}"
    class="w-full p-4 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-sm transition-all duration-200"
    role="progressbar"
    aria-valuenow="{{ $progress }}"
    aria-valuemin="0"
    aria-valuemax="100"
    aria-label="{{ __('File upload progress') }}"
    aria-busy="{{ $status === 'uploading' ? 'true' : 'false' }}"
    aria-describedby="{{ $id }}-status"
>
    <div class="flex items-center gap-3">
        <!-- File Icon -->
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-zinc-100 dark:bg-zinc-700 flex items-center justify-center transition-colors duration-200">
            @if($status === 'uploading')
                <flux:icon name="document-text" class="size-5 text-zinc-500 dark:text-zinc-400" aria-hidden="true" />
            @elseif($status === 'success')
                <flux:icon name="check-circle" class="size-5 text-green-500 dark:text-green-400" aria-hidden="true" />
            @else
                <flux:icon name="exclamation-circle" class="size-5 text-red-500 dark:text-red-400" aria-hidden="true" />
            @endif
        </div>

        <!-- File Info -->
        <div class="flex-1 min-w-0">
            @if($fileName)
                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate" id="{{ $id }}-filename">
                    {{ $fileName }}
                </p>
            @endif
            @if($fileSize)
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $fileSize }}
                </p>
            @endif

            <!-- Progress Bar -->
            <div class="mt-2 h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden" role="presentation">
                <div
                    class="h-full {{ $statusColors[$status] }} transition-all duration-300 ease-out rounded-full"
                    style="width: {{ min(100, max(0, $progress)) }}%"
                    aria-hidden="true"
                ></div>
            </div>
        </div>

        <!-- Percentage / Status -->
        <div class="flex-shrink-0 text-right flex items-center gap-2">
            @if($status === 'uploading' && $showPercent)
                <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100 tabular-nums" id="{{ $id }}-status">
                    {{ $progress }}%
                </span>
                @if($cancelable)
                    <button
                        type="button"
                        class="p-1 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] transition-colors"
                        aria-label="{{ __('Cancel upload') }}"
                        {{ $attributes->only(['wire:click', 'onclick', 'x-on:click']) }}
                    >
                        <flux:icon name="x-mark" class="size-4 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" aria-hidden="true" />
                    </button>
                @endif
            @elseif($status === 'success')
                <span class="text-sm font-medium text-green-600 dark:text-green-400" id="{{ $id }}-status">
                    {{ __('Done') }}
                </span>
            @elseif($status === 'error')
                <span class="text-sm font-medium text-red-600 dark:text-red-400" id="{{ $id }}-status">
                    {{ __('Failed') }}
                </span>
            @endif
        </div>
    </div>

    <span class="sr-only" aria-live="polite">
        @if($status === 'uploading')
            {{ __('Uploading file, :progress percent complete', ['progress' => $progress]) }}
        @elseif($status === 'success')
            {{ __('File uploaded successfully') }}
        @else
            {{ __('File upload failed') }}
        @endif
    </span>
</div>
