<x-layouts::app :title="__('system_logs')">
    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">{{ __('dashboard') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.settings.index') }}" class="hover:text-white">{{ __('settings') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('logs') }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('system_logs') }}</h1>
        <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('view_logs_description') }}</p>
    </div>

    <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('log_files') }}</h2>
            <a href="{{ route('admin.settings.logs') }}" class="text-sm text-blue-600 hover:underline">{{ __('refresh') }}</a>
        </div>

        @if(count($logFiles) > 0)
            <ul class="divide-y divide-stone-200 dark:divide-stone-700">
                @foreach($logFiles as $file)
                    <li class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <p class="font-medium text-stone-900 dark:text-stone-100">{{ $file['name'] }}</p>
                                <p class="text-sm text-stone-500">{{ $file['size'] }} • {{ $file['modified'] }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="py-8 text-center text-stone-500">
                <p>{{ __('no_log_files') }}</p>
            </div>
        @endif
    </div>
</x-layouts::app>
