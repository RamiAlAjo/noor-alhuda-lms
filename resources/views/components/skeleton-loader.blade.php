@props([
    'type' => 'text',
    'lines' => 3,
    'class' => '',
    'animate' => true,
])

@php
    $baseClasses = ($animate ? 'animate-pulse ' : '') . 'bg-zinc-200 dark:bg-zinc-700 rounded';
@endphp

@if($type === 'text')
    <div class="space-y-2 {{ $class }}" role="status" aria-label="{{ __('Loading content') }}" aria-busy="true">
        @for($i = 0; $i < $lines; $i++)
            <div
                class="{{ $baseClasses }} h-4 {{ $i === $lines - 1 ? 'w-3/4' : 'w-full' }}"
                aria-hidden="true"
            ></div>
        @endfor
        <span class="sr-only">{{ __('Loading...') }}</span>
    </div>

@elseif($type === 'card')
    <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 {{ $class }}" role="status" aria-label="{{ __('Loading card') }}" aria-busy="true">
        <div class="{{ $baseClasses }} h-48 w-full mb-4 rounded-lg" aria-hidden="true"></div>
        <div class="space-y-3">
            <div class="{{ $baseClasses }} h-4 w-3/4" aria-hidden="true"></div>
            <div class="{{ $baseClasses }} h-4 w-1/2" aria-hidden="true"></div>
            <div class="{{ $baseClasses }} h-4 w-5/6" aria-hidden="true"></div>
        </div>
        <span class="sr-only">{{ __('Loading card...') }}</span>
    </div>

@elseif($type === 'table')
    <div class="bg-white dark:bg-zinc-800 rounded-lg {{ $class }}" role="status" aria-label="{{ __('Loading table') }}" aria-busy="true">
        <div class="{{ $baseClasses }} h-10 w-full mb-4 rounded" aria-hidden="true"></div>
        @for($i = 0; $i < 5; $i++)
            <div class="flex items-center space-x-4 mb-3 p-2" aria-hidden="true">
                <div class="{{ $baseClasses }} h-4 w-1/4" aria-hidden="true"></div>
                <div class="{{ $baseClasses }} h-4 w-1/3" aria-hidden="true"></div>
                <div class="{{ $baseClasses }} h-4 w-1/4" aria-hidden="true"></div>
                <div class="{{ $baseClasses }} h-4 w-1/6" aria-hidden="true"></div>
            </div>
        @endfor
        <span class="sr-only">{{ __('Loading table...') }}</span>
    </div>

@elseif($type === 'avatar')
    <div class="flex items-center space-x-3 {{ $class }}" role="status" aria-label="{{ __('Loading profile') }}" aria-busy="true">
        <div class="{{ $baseClasses }} w-10 h-10 rounded-full" aria-hidden="true"></div>
        <div class="space-y-2 flex-1">
            <div class="{{ $baseClasses }} h-4 w-1/2" aria-hidden="true"></div>
            <div class="{{ $baseClasses }} h-3 w-1/3" aria-hidden="true"></div>
        </div>
        <span class="sr-only">{{ __('Loading profile...') }}</span>
    </div>

@elseif($type === 'list')
    <div class="space-y-3 {{ $class }}" role="status" aria-label="{{ __('Loading list') }}" aria-busy="true">
        @for($i = 0; $i < $lines; $i++)
            <div class="flex items-center space-x-3 p-2" aria-hidden="true">
                <div class="{{ $baseClasses }} w-8 h-8 rounded" aria-hidden="true"></div>
                <div class="flex-1 space-y-2">
                    <div class="{{ $baseClasses }} h-4 w-3/4" aria-hidden="true"></div>
                    <div class="{{ $baseClasses }} h-3 w-1/2" aria-hidden="true"></div>
                </div>
            </div>
        @endfor
        <span class="sr-only">{{ __('Loading list...') }}</span>
    </div>

@elseif($type === 'form')
    <div class="space-y-4 {{ $class }}" role="status" aria-label="{{ __('Loading form') }}" aria-busy="true">
        @for($i = 0; $i < $lines; $i++)
            <div class="space-y-2" aria-hidden="true">
                <div class="{{ $baseClasses }} h-4 w-1/4" aria-hidden="true"></div>
                <div class="{{ $baseClasses }} h-10 w-full rounded-lg" aria-hidden="true"></div>
            </div>
        @endfor
        <div class="{{ $baseClasses }} h-10 w-32 rounded-lg mt-4" aria-hidden="true"></div>
        <span class="sr-only">{{ __('Loading form...') }}</span>
    </div>

@elseif($type === 'stat')
    <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-white dark:bg-zinc-800 {{ $class }}" role="status" aria-label="{{ __('Loading statistics') }}" aria-busy="true">
        <div class="{{ $baseClasses }} h-4 w-1/2 mb-2" aria-hidden="true"></div>
        <div class="{{ $baseClasses }} h-8 w-3/4 mb-1" aria-hidden="true"></div>
        <div class="{{ $baseClasses }} h-3 w-1/3" aria-hidden="true"></div>
        <span class="sr-only">{{ __('Loading statistics...') }}</span>
    </div>

@elseif($type === 'chart')
    <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 {{ $class }}" role="status" aria-label="{{ __('Loading chart') }}" aria-busy="true">
        <div class="{{ $baseClasses }} h-64 w-full rounded-lg" aria-hidden="true"></div>
        <span class="sr-only">{{ __('Loading chart...') }}</span>
    </div>

@elseif($type === 'image')
    <div class="{{ $baseClasses }} {{ $class }} w-full h-48 rounded-lg" role="img" aria-label="{{ __('Loading image') }}" aria-busy="true">
        <span class="sr-only">{{ __('Loading image...') }}</span>
    </div>

@elseif($type === 'notification')
    <div class="space-y-3 {{ $class }}" role="status" aria-label="{{ __('Loading notifications') }}" aria-busy="true">
        @for($i = 0; $i < $lines; $i++)
            <div class="flex items-start space-x-3 p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg" aria-hidden="true">
                <div class="{{ $baseClasses }} w-10 h-10 rounded-full flex-shrink-0" aria-hidden="true"></div>
                <div class="flex-1 space-y-2">
                    <div class="{{ $baseClasses }} h-4 w-3/4" aria-hidden="true"></div>
                    <div class="{{ $baseClasses }} h-3 w-full" aria-hidden="true"></div>
                    <div class="{{ $baseClasses }} h-2 w-1/4" aria-hidden="true"></div>
                </div>
            </div>
        @endfor
        <span class="sr-only">{{ __('Loading notifications...') }}</span>
    </div>

@elseif($type === 'sidebar')
    <div class="w-64 h-full {{ $class }}" role="status" aria-label="{{ __('Loading sidebar') }}" aria-busy="true">
        <div class="p-4 space-y-4" aria-hidden="true">
            <div class="{{ $baseClasses }} h-8 w-8 rounded-lg mx-auto" aria-hidden="true"></div>
            <div class="space-y-2">
                @for($i = 0; $i < 5; $i++)
                    <div class="{{ $baseClasses }} h-8 w-full rounded-lg" aria-hidden="true"></div>
                @endfor
            </div>
        </div>
        <span class="sr-only">{{ __('Loading sidebar...') }}</span>
    </div>
@else
    <div class="{{ $baseClasses }} {{ $class }} h-4 w-full" role="status" aria-label="{{ __('Loading') }}" aria-busy="true">
        <span class="sr-only">{{ __('Loading...') }}</span>
    </div>
@endif
