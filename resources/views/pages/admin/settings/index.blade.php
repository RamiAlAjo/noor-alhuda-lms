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

        @if(session('success'))
            <div class="mt-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                <div class="flex items-center">
                    <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400 mr-3" />
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                <div class="flex items-center">
                    <flux:icon.exclamation-triangle class="size-5 text-red-600 dark:text-red-400 mr-3" />
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Settings Categories -->
    <div class="space-y-8">
        <!-- General Settings -->
        @if($categories['general']->count() > 0)
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <flux:icon.cog-6-tooth class="size-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('General Settings') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Basic application configuration and branding') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($categories['general'] as $setting)
                        <div class="space-y-2">
                            <flux:label for="{{ $setting->key }}">{{ $setting->label }}</flux:label>
                            @if($setting->type === 'boolean')
                                <flux:checkbox name="{{ $setting->key }}" :checked="$setting->value" />
                            @elseif($setting->type === 'select' && $setting->options)
                                <flux:select name="{{ $setting->key }}">
                                    @foreach($setting->options as $option)
                                        <option value="{{ $option }}" {{ $setting->value == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </flux:select>
                            @else
                                <flux:input name="{{ $setting->key }}" value="{{ $setting->value }}" />
                            @endif
                            @if($setting->description)
                                <p class="text-xs text-stone-500 dark:text-stone-400">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <x-button.submit loading-text="Saving..." variant="primary">
                    Save General Settings
                </x-button.submit>
            </form>
        </div>
        @endif

        <!-- Security Settings -->
        @if($categories['security']->count() > 0)
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                    <flux:icon.shield-check class="size-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('Security Settings') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Password policies and authentication settings') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($categories['security'] as $setting)
                        <div class="space-y-2">
                            <flux:label for="{{ $setting->key }}">{{ $setting->label }}</flux:label>
                            @if($setting->type === 'boolean')
                                <flux:checkbox name="{{ $setting->key }}" :checked="$setting->value" />
                            @elseif($setting->type === 'integer')
                                <flux:input type="number" name="{{ $setting->key }}" value="{{ $setting->value }}" />
                            @else
                                <flux:input name="{{ $setting->key }}" value="{{ $setting->value }}" />
                            @endif
                            @if($setting->description)
                                <p class="text-xs text-stone-500 dark:text-stone-400">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <x-button.submit loading-text="Saving..." variant="primary">
                    Save Security Settings
                </x-button.submit>
            </form>
        </div>
        @endif

        <!-- Email Settings -->
        @if($categories['email']->count() > 0)
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                    <flux:icon.envelope class="size-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('Email Settings') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('SMTP configuration and email settings') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($categories['email'] as $setting)
                        <div class="space-y-2">
                            <flux:label for="{{ $setting->key }}">{{ $setting->label }}</flux:label>
                            @if($setting->type === 'select' && $setting->options)
                                <flux:select name="{{ $setting->key }}">
                                    @foreach($setting->options as $option)
                                        <option value="{{ $option }}" {{ $setting->value == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </flux:select>
                            @elseif($setting->type === 'integer')
                                <flux:input type="number" name="{{ $setting->key }}" value="{{ $setting->value }}" />
                            @else
                                <flux:input name="{{ $setting->key }}" value="{{ $setting->value }}" type="{{ $setting->key === 'mail_password' ? 'password' : 'text' }}" />
                            @endif
                            @if($setting->description)
                                <p class="text-xs text-stone-500 dark:text-stone-400">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-4">
                    <x-button.submit loading-text="Saving..." variant="primary">
                        Save Email Settings
                    </x-button.submit>

                    <form method="POST" action="{{ route('admin.settings.test-email') }}" class="inline">
                        @csrf
                        <flux:input name="test_email" placeholder="test@example.com" class="mr-2" />
                         <x-button.submit loading-text="{{ __('Sending...') }}" variant="secondary">
                             {{ __('Test Email') }}
                         </x-button.submit>
                    </form>
                </div>
            </form>
        </div>
        @endif

        <!-- System Settings -->
        @if($categories['system']->count() > 0)
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30">
                    <flux:icon.cpu-chip class="size-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('System Settings') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('System maintenance, caching, and backup settings') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    @foreach($categories['system'] as $setting)
                        @if($setting->key !== 'maintenance_mode')
                        <div class="space-y-2">
                            <flux:label for="{{ $setting->key }}">{{ $setting->label }}</flux:label>
                            @if($setting->type === 'boolean')
                                <flux:checkbox name="{{ $setting->key }}" :checked="$setting->value" />
                            @elseif($setting->type === 'select' && $setting->options)
                                <flux:select name="{{ $setting->key }}">
                                    @foreach($setting->options as $option)
                                        <option value="{{ $option }}" {{ $setting->value == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </flux:select>
                            @else
                                <flux:input name="{{ $setting->key }}" value="{{ $setting->value }}" />
                            @endif
                            @if($setting->description)
                                <p class="text-xs text-stone-500 dark:text-stone-400">{{ $setting->description }}</p>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>

                <x-button.submit loading-text="Saving..." variant="primary">
                    Save System Settings
                </x-button.submit>
            </form>

            <!-- Maintenance Mode -->
            <div class="mt-8 border-t border-stone-200 pt-6 dark:border-stone-700">
                <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100 mb-4">{{ __('Maintenance Mode') }}</h3>
                <form method="POST" action="{{ route('admin.settings.maintenance') }}" class="space-y-4">
                    @csrf
                    <div class="flex items-center gap-4">
                        <flux:checkbox name="maintenance_mode" :checked="\App\Models\SystemSetting::get('maintenance_mode', false)" />
                        <div>
                            <flux:label for="maintenance_mode">{{ __('Enable Maintenance Mode') }}</flux:label>
                            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Put the application in maintenance mode') }}</p>
                        </div>
                    </div>
                    <flux:textarea name="maintenance_message" :label="__('Maintenance Message')" rows="2" placeholder="The system is currently under maintenance. Please try again later.">
{{ \App\Models\SystemSetting::get('maintenance_message', 'The system is currently under maintenance. Please try again later.') }}
                    </flux:textarea>
                     <x-button.submit loading-text="{{ __('Toggling...') }}" variant="secondary">
                         {{ __('Toggle Maintenance Mode') }}
                     </x-button.submit>
                </form>
            </div>
        </div>
        @endif

        <!-- Backup Management -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 dark:bg-indigo-900/30">
                    <flux:icon.archive-box class="size-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('Backup Management') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Create and manage database backups') }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <form method="POST" action="{{ route('admin.settings.backup') }}" class="inline-block">
                    @csrf
                    <x-button.submit loading-text="Creating Backup..." variant="primary">
                        <flux:icon name="archive-box" class="size-5" />
                        Create Database Backup
                    </x-button.submit>
                </form>

                <p class="text-sm text-stone-600 dark:text-stone-400">
                    {{ __('Backups are stored in') }} <code>storage/app/backups/</code>
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 rounded-xl border border-stone-200 bg-stone-50 p-6 dark:border-stone-700 dark:bg-stone-800/50">
        <h2 class="mb-4 text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Quick Actions') }}</h2>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
                @csrf
                <x-button.submit loading-text="{{ __('Clearing...') }}" variant="secondary" class="w-full">
                    {{ __('Clear Cache') }}
                </x-button.submit>
            </form>

            <form method="POST" action="{{ route('admin.settings.clear-all-caches') }}">
                @csrf
                <x-button.submit loading-text="{{ __('Clearing...') }}" variant="secondary" class="w-full">
                    {{ __('Clear All Caches') }}
                </x-button.submit>
            </form>

            <form method="POST" action="{{ route('admin.settings.backup') }}">
                @csrf
                <x-button.submit loading-text="{{ __('Creating Backup...') }}" variant="secondary" class="w-full">
                    {{ __('Create Backup') }}
                </x-button.submit>
            </form>

            <flux:button :href="route('admin.settings.logs')" variant="outline" icon="document-text" class="w-full">
                {{ __('View Logs') }}
            </flux:button>

            <flux:button :href="route('admin.settings.backups')" variant="outline" icon="archive-box" class="w-full">
                {{ __('Manage Backups') }}
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
