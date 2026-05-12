<x-layouts::app :title="__('Backup Management')">
    <div class="mb-8">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.settings.index') }}" class="hover:text-white">{{ __('Settings') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Backups') }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('Backup Management') }}</h1>
        <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('Create and manage database backups') }}</p>
    </div>

    <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                    <flux:icon.archive-box class="size-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('Database Backups') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Automated and manual database backups') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.backup') }}">
                @csrf
                <flux:button type="submit" variant="primary" icon="plus">
                    {{ __('Create Backup') }}
                </flux:button>
            </form>
        </div>

        @if(!empty($backups))
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                    <thead class="bg-stone-50 dark:bg-stone-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">{{ __('Filename') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">{{ __('Size') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">{{ __('Created') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-stone-900 divide-y divide-stone-200 dark:divide-stone-700">
                        @foreach($backups as $backup)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-900 dark:text-stone-100">
                                    {{ $backup['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500 dark:text-stone-400">
                                    {{ $backup['size'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500 dark:text-stone-400">
                                    {{ $backup['modified'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ Storage::url('backups/' . $backup['name']) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" target="_blank">
                                        {{ __('Download') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <flux:icon.archive-box class="mx-auto h-12 w-12 text-stone-400" />
                <h3 class="mt-2 text-sm font-medium text-stone-900 dark:text-stone-100">{{ __('No backups found') }}</h3>
                <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">{{ __('Create your first backup to get started') }}</p>
            </div>
        @endif
    </div>
</x-layouts::app>