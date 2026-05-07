<x-layouts::app :title="__('System Information')">
    <div class="mb-8">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.settings.index') }}" class="hover:text-white">{{ __('Settings') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('System Info') }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('System Information') }}</h1>
        <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('lms.system_info_description') }}</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Application Info -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <flux:icon.cube class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <flux:heading level="3">{{ __('Application') }}</flux:heading>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-stone-100 dark:border-stone-700">
                    <flux:text variant="subtle">{{ __('Application Name') }}</flux:text>
                    <flux:heading level="4" size="sm">{{ __('lms.app_name') }}</flux:heading>
                </div>
                <div class="flex justify-between py-2 border-b border-stone-100 dark:border-stone-700">
                    <flux:text variant="subtle">{{ __('Version') }}</flux:text>
                    <flux:heading level="4" size="sm">1.0.0</flux:heading>
                </div>
                <div class="flex justify-between py-2 border-b border-stone-100 dark:border-stone-700">
                    <flux:text variant="subtle">{{ __('Laravel Version') }}</flux:text>
                    <flux:heading level="4" size="sm">{{ $laravelVersion }}</flux:heading>
                </div>
                <div class="flex justify-between py-2">
                    <flux:text variant="subtle">{{ __('Environment') }}</flux:text>
                    <flux:badge color="green">{{ app()->environment() }}</flux:badge>
                </div>
            </div>
        </div>

        <!-- Server Info -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                    <flux:icon.server class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <flux:heading level="3">{{ __('Server') }}</flux:heading>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b border-stone-100 dark:border-stone-700">
                    <flux:text variant="subtle">{{ __('PHP Version') }}</flux:text>
                    <flux:heading level="4" size="sm">{{ $phpVersion }}</flux:heading>
                </div>
                <div class="flex justify-between py-2 border-b border-stone-100 dark:border-stone-700">
                    <flux:text variant="subtle">{{ __('Database Driver') }}</flux:text>
                    <flux:heading level="4" size="sm">{{ ucfirst($databaseDriver) }}</flux:heading>
                </div>
                <div class="flex justify-between py-2 border-b border-stone-100 dark:border-stone-700">
                    <flux:text variant="subtle">{{ __('Server Software') }}</flux:text>
                    <flux:heading level="4" size="sm" class="truncate max-w-[150px]">{{ $serverSoftware }}</flux:heading>
                </div>
                <div class="flex justify-between py-2">
                    <flux:text variant="subtle">{{ __('Timezone') }}</flux:text>
                    <flux:heading level="4" size="sm">{{ config('app.timezone') }}</flux:heading>
                </div>
            </div>
        </div>

        <!-- PHP Extensions -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800 md:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                    <flux:icon.code-bracket class="size-5 text-green-600 dark:text-green-400" />
                </div>
                <flux:heading level="3">{{ __('PHP Extensions') }}</flux:heading>
            </div>

            <div class="flex flex-wrap gap-2">
                @php
                $extensions = ['pdo', 'mbstring', 'openssl', 'json', 'curl', 'gd', 'zip', 'xml'];
                @endphp

                @foreach($extensions as $ext)
                    <flux:badge color="{{ extension_loaded($ext) ? 'green' : 'red' }}">
                        {{ $ext }}
                    </flux:badge>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts::app>
