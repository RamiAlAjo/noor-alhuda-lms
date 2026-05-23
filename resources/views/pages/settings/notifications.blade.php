{{--
    Notification Settings Page
--}}
<x-layouts::app :title="__('Notification Settings')">
    <div class="mb-8">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('settings') }}" class="hover:text-white">{{ __('Settings') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Notifications') }}</span>
        </nav>

        <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('Notification Settings') }}</h1>
        <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('Manage your notification preferences and delivery methods') }}</p>
    </div>

    <div class="space-y-6">
        <!-- General Notification Settings -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <flux:icon.bell class="size-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('General Settings') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Control how and when you receive notifications') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Delivery Methods -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-stone-900 dark:text-stone-100">{{ __('Delivery Methods') }}</h3>

                    <div class="space-y-3">
                        <flux:checkbox name="notification_email" :checked="auth()->user()->settings?->notification_email ?? true">
                            {{ __('Email Notifications') }}
                        </flux:checkbox>
                        <p class="text-sm text-stone-500 dark:text-stone-400 ml-6">{{ __('Receive notifications via email') }}</p>

                        <flux:checkbox name="notification_push" :checked="auth()->user()->settings?->notification_push ?? true">
                            {{ __('Push Notifications') }}
                        </flux:checkbox>
                        <p class="text-sm text-stone-500 dark:text-stone-400 ml-6">{{ __('Receive real-time push notifications in your browser') }}</p>
                    </div>
                </div>

                <!-- Notification Types -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-stone-900 dark:text-stone-100">{{ __('Notification Types') }}</h3>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-3">
                            <flux:checkbox name="notification_grades" :checked="auth()->user()->settings?->notification_grades ?? true">
                                {{ __('Grade Notifications') }}
                            </flux:checkbox>
                            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('When grades are posted') }}</p>

                            <flux:checkbox name="notification_enrollment" :checked="auth()->user()->settings?->notification_enrollment ?? true">
                                {{ __('Enrollment Notifications') }}
                            </flux:checkbox>
                            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Enrollment status changes') }}</p>

                            <flux:checkbox name="notification_payments" :checked="auth()->user()->settings?->notification_payments ?? true">
                                {{ __('Payment Notifications') }}
                            </flux:checkbox>
                            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Payment status updates') }}</p>
                        </div>

                        <div class="space-y-3">
                            <flux:checkbox name="notification_announcements" :checked="auth()->user()->settings?->notification_announcements ?? true">
                                {{ __('Course Announcements') }}
                            </flux:checkbox>
                            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('New course announcements') }}</p>

                            <flux:checkbox name="notification_reminders" :checked="auth()->user()->settings?->notification_reminders ?? true">
                                {{ __('Reminders') }}
                            </flux:checkbox>
                            <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Assignment deadlines and events') }}</p>
                        </div>
                    </div>
                </div>

                <x-button.submit loading-text="{{ __('Saving...') }}">
                    {{ __('Save Notification Preferences') }}
                </x-button.submit>
            </form>
        </div>

        <!-- Test Notifications -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                    <flux:icon.paper-airplane class="size-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('Test Notifications') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Send test notifications to verify your settings') }}</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <form method="POST" action="{{ route('settings.notifications.test') }}" class="inline-block w-full">
                    @csrf
                    <input type="hidden" name="type" value="push">
                    <x-button.submit loading-text="{{ __('Sending Test...') }}" variant="secondary" class="w-full">
                        {{ __('Test Push Notification') }}
                    </x-button.submit>
                </form>

                <form method="POST" action="{{ route('settings.notifications.test') }}" class="inline-block w-full">
                    @csrf
                    <input type="hidden" name="type" value="email">
                    <x-button.submit loading-text="{{ __('Sending Test...') }}" variant="secondary" class="w-full">
                        {{ __('Test Email Notification') }}
                    </x-button.submit>
                </form>

                <form method="POST" action="{{ route('settings.notifications.test') }}" class="inline-block w-full">
                    @csrf
                    <input type="hidden" name="type" value="sound">
                    <x-button.submit loading-text="{{ __('Playing...') }}" variant="secondary" class="w-full">
                        {{ __('Test Sound') }}
                    </x-button.submit>
                </form>
            </div>
        </div>

        <!-- Notification History -->
        <div class="rounded-xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-800">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30">
                    <flux:icon.clock class="size-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-stone-900 dark:text-stone-100">{{ __('Recent Notifications') }}</h2>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Your recent notification history') }}</p>
                </div>
            </div>

            @if(auth()->user()->notifications->count() > 0)
                <div class="space-y-3">
                    @foreach(auth()->user()->notifications->take(5) as $notification)
                        <div class="flex items-start gap-3 rounded-lg border border-stone-200 p-4 dark:border-stone-700">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-{{ $notification->color }}-100 text-{{ $notification->color }}-600 dark:bg-{{ $notification->color }}-900/30 dark:text-{{ $notification->color }}-400">
                                <flux:icon name="{{ $notification->icon }}" class="size-4" />
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-stone-900 dark:text-stone-100">{{ $notification->title }}</h4>
                                <p class="text-sm text-stone-600 dark:text-stone-400">{{ $notification->content }}</p>
                                <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->is_read)
                                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2" title="{{ __('Unread') }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        {{ __('View All Notifications') }}
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <flux:icon.bell class="mx-auto h-12 w-12 text-stone-400" />
                    <h3 class="mt-2 text-sm font-medium text-stone-900 dark:text-stone-100">{{ __('No notifications yet') }}</h3>
                    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">{{ __('You will see your notification history here') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>