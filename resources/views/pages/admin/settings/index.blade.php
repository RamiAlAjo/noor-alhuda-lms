{{--
    =============================================================================
    ADMIN SYSTEM SETTINGS INDEX VIEW
    =============================================================================

    Purpose: Central hub for system configuration and administration.

    Route: admin.settings.index
    Controller: Admin\SettingController@index

    Components:
    - Settings Cards (clickable):
      * General Settings - System name, timezone, language
      * Appearance - Themes, colors, logos
      * Security - Password policies, 2FA, sessions
      * System Info - PHP version, database details
      * Backup & Maintenance - DB backups, logs, maintenance mode
    - Quick Actions: Clear Cache, Create Backup, View Logs
    - System Status cards: System Status, Total Users, Laravel Version, Last Backup
    - Breadcrumb navigation

    Dependencies:
    - route('admin.settings.index') - General settings
    - route('admin.settings.theme') - Theme settings
    - route('profile.edit') - Security settings
    - route('admin.settings.system-info') - System info
    - route('admin.settings.backups') - Backup management
    - route('admin.settings.clear-cache') - Clear cache action
    - route('admin.settings.backup') - Create backup action
    - route('admin.settings.logs') - View logs
    - \App\Models\User::count() - Get total users
    - app()->version() - Get Laravel version

    =============================================================================
--}}
<x-layouts::app :title="__('System Settings')">
    <!-- Header with Breadcrumb -->
    <div class="mb-8">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('System Settings') }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('System Settings') }}</h1>
        <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('Manage system configuration and preferences for Noor Alhuda LMS') }}</p>
    </div>

    <!-- Settings Cards -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- General Settings -->
        <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-stone-700 dark:bg-stone-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-transform group-hover:scale-110 dark:bg-blue-900/30 dark:text-blue-400">
                <flux:icon.cog-6-tooth class="size-7" />
            </div>
            <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('General Settings') }}</h3>
            <p class="mb-4 text-sm text-stone-500 dark:text-stone-400">{{ __('Configure system name, timezone, language defaults, and basic preferences') }}</p>
            <flux:button :href="route('admin.settings.index')" variant="ghost" size="sm" icon="chevron-right" icon:variant="end">
                {{ __('Manage') }}
            </flux:button>
        </div>

        <!-- Appearance -->
        <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-stone-700 dark:bg-stone-800">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-purple-100 text-purple-600 transition-transform group-hover:scale-110 dark:bg-purple-900/30 dark:text-purple-400">
                <flux:icon.paint-brush class="size-7" />
            </div>
            <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Appearance') }}</h3>
            <p class="mb-4 text-sm text-stone-500 dark:text-stone-400">{{ __('Customize themes, colors, logos, and visual presentation') }}</p>
            <flux:button :href="route('admin.settings.theme')" variant="ghost" size="sm" icon="chevron-right" icon:variant="end">
                {{ __('Manage') }}
            </flux:button>
        </div>

        <!-- Security -->
        <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-stone-700 dark:bg-stone-800">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-red-100 text-red-600 transition-transform group-hover:scale-110 dark:bg-red-900/30 dark:text-red-400">
                <flux:icon.shield-check class="size-7" />
            </div>
            <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Security') }}</h3>
            <p class="mb-4 text-sm text-stone-500 dark:text-stone-400">{{ __('Password policies, two-factor auth, session management') }}</p>
            <flux:button :href="route('profile.edit')" variant="ghost" size="sm" icon="chevron-right" icon:variant="end">
                {{ __('Manage') }}
            </flux:button>
        </div>

        <!-- System Info -->
        <div class="group relative overflow-hidden rounded-xl border border-stone-200 bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-stone-700 dark:bg-stone-800">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-yellow-100 text-yellow-600 transition-transform group-hover:scale-110 dark:bg-yellow-900/30 dark:text-yellow-400">
                <flux:icon.cpu-chip class="size-7" />
            </div>
            <h3 class="mb-2 text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('System Info') }}</h3>
            <p class="mb-4 text-sm text-stone-500 dark:text-stone-400">{{ __('View system information, PHP version, and database details') }}</p>
            <flux:button :href="route('admin.settings.system-info')" variant="ghost" size="sm" icon="chevron-right" icon:variant="end">
                {{ __('View') }}
            </flux:button>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 rounded-xl border border-stone-200 bg-stone-50 p-6 dark:border-stone-700 dark:bg-stone-800/50">
        <h2 class="mb-4 text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Quick Actions') }}</h2>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
                @csrf
                <flux:button type="submit" variant="outline" icon="arrow-path">
                    {{ __('Clear Cache') }}
                </flux:button>
            </form>
            <flux:button :href="route('admin.settings.logs')" variant="outline" icon="document-text">
                {{ __('View Logs') }}
            </flux:button>
        </div>
    </div>

    <!-- System Status -->
    <div class="mt-6 grid gap-6 md:grid-cols-4">
        <div class="rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-700 dark:bg-stone-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                    <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-stone-600 dark:text-stone-400">{{ __('System Status') }}</p>
                    <p class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Operational') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-700 dark:bg-stone-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <flux:icon.users class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-stone-600 dark:text-stone-400">{{ __('Total Users') }}</p>
                    <p class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{\App\Models\User::count()}}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-stone-200 bg-white p-4 dark:border-stone-700 dark:bg-stone-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                    <flux:icon.academic-cap class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-stone-600 dark:text-stone-400">{{ __('Laravel Version') }}</p>
                    <p class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ substr(app()->version(), 0, 10) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
