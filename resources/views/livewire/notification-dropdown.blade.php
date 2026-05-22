<div
    x-data="{
        isOpen: @entangle('isOpen'),
        soundEnabled: @entangle('soundEnabled'),
        showToasts: []
    }"
    x-on:notification-received.window="handleNotification($event)"
    x-on:click.away="isOpen = false"
    x-on:keydown.escape.window="isOpen = false"
    x-init="console.log('Notification dropdown initialized'); $wire.checkForNewNotifications(); setInterval(() => { console.log('Checking for new notifications...'); $wire.checkForNewNotifications(); }, 10000)"
    class="relative"
>
    <!-- Notification Bell Button -->
    <flux:tooltip :content="__('Notifications')" position="bottom">
        <button
            type="button"
            x-on:click="isOpen = !isOpen"
            x-bind:aria-expanded="isOpen"
            aria-controls="notifications-dropdown"
            class="relative !h-10 navbar-icon-btn flex items-center justify-center rounded-lg transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] focus:ring-offset-2"
            aria-label="{{ __('Notifications') }}"
            aria-haspopup="true"
            role="button"
        >
            <flux:icon name="bell" class="size-5 text-zinc-600 dark:text-zinc-400" aria-hidden="true" />

            <!-- Unread Count Badge -->
            @if($unreadCount > 0)
                <span
                    class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center px-1 animate-pulse"
                    x-show="{{ $unreadCount }} > 0"
                    aria-label="{{ __(':count unread notifications', ['count' => $unreadCount]) }}"
                >
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </button>
    </flux:tooltip>

    <!-- Dropdown Panel -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        x-trap.noscroll="isOpen"
        class="absolute end-0 mt-2 w-80 sm:w-96 rounded-xl shadow-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 z-50 overflow-hidden"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="notifications-menu"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60">
            <div class="flex items-center justify-between gap-3">
                <!-- Title -->
                <div class="min-w-0">
                    <h2 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100" id="notifications-menu">
                        {{ __('Notifications') }}
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400" aria-live="polite">
                        @if($unreadCount > 0)
                            {{ $unreadCount }} {{ __('unread') }}
                        @else
                            {{ __('All caught up!') }}
                        @endif
                    </p>
                </div>

                <!-- Quick Actions -->
                <div class="flex items-center gap-1.5">
                    <!-- Mark all as read -->
                    @if($unreadCount > 0)
                        <button
                            type="button"
                            wire:click="markAllAsRead"
                            class="px-2.5 py-1 text-xs font-medium rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)] hover:bg-[var(--color-accent)]/15 transition-colors focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)]"
                        >
                            {{ __('Mark all read') }}
                        </button>
                    @endif

                    <!-- Sound Toggle -->
                    <button
                        type="button"
                        x-on:click="$wire.toggleSound()"
                        class="p-1.5 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)]"
                        x-bind:title="soundEnabled ? '{{ __('Disable sound') }}' : '{{ __('Enable sound') }}'"
                        x-bind:aria-label="soundEnabled ? '{{ __('Disable notification sound') }}' : '{{ __('Enable notification sound') }}'"
                        x-bind:aria-pressed="soundEnabled"
                    >
                        <flux:icon name="speaker-wave" class="size-4 text-zinc-500" x-show="soundEnabled" aria-hidden="true" />
                        <flux:icon name="speaker-x-mark" class="size-4 text-zinc-500" x-show="!soundEnabled" aria-hidden="true" />
                    </button>

                    <!-- Push Toggle -->
                    <button
                        type="button"
                        x-on:click="$wire.togglePush()"
                        class="p-1.5 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)]"
                        x-bind:title="pushEnabled ? '{{ __('Disable push') }}' : '{{ __('Enable push') }}'"
                        x-bind:aria-label="pushEnabled ? '{{ __('Disable push notifications') }}' : '{{ __('Enable push notifications') }}'"
                        x-bind:aria-pressed="pushEnabled"
                    >
                        <flux:icon name="bell-alert" class="size-4 text-zinc-500" x-show="pushEnabled" aria-hidden="true" />
                        <flux:icon name="bell-slash" class="size-4 text-zinc-500" x-show="!pushEnabled" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Row -->
        <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 space-y-3">
            <!-- Search -->
            <div class="relative">
                <flux:input
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="{{ __('Search notifications...') }}"
                    class="w-full pl-9 text-sm"
                />
                <flux:icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" />
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-2">
                <select
                    wire:model.live="filterType"
                    class="flex-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 px-3 py-1.5 focus:ring-2 focus:ring-[var(--color-accent)]"
                >
                    <option value="all">{{ __('All Types') }}</option>
                    <option value="grade">{{ __('Grades') }}</option>
                    <option value="enrollment">{{ __('Enrollment') }}</option>
                    <option value="payment">{{ __('Payments') }}</option>
                    <option value="announcement">{{ __('Announcements') }}</option>
                    <option value="reminder">{{ __('Reminders') }}</option>
                    <option value="system">{{ __('System') }}</option>
                </select>

                <label class="flex items-center gap-1.5 text-sm whitespace-nowrap px-2 py-1 rounded-lg border border-zinc-300 dark:border-zinc-600 cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="showUnreadOnly"
                        class="rounded border-zinc-300 dark:border-zinc-600 text-[var(--color-accent)] focus:ring-[var(--color-accent)]"
                    />
                    <span class="text-xs">{{ __('Unread only') }}</span>
                </label>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-[340px] overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-600" role="list" aria-label="{{ __('Notifications list') }}">
            @forelse($notifications as $notification)
                <div
                    class="group relative px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ !$notification['is_read'] ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}"
                    role="listitem"
                    x-data="{ showDelete: false }"
                    x-on:mouseenter="showDelete = true"
                    x-on:mouseleave="showDelete = false"
                    x-on:focusin="showDelete = true"
                    x-on:focusout="showDelete = false"
                >
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        @php
                            $color = in_array($notification['color'] ?? 'slate', ['slate', 'gray', 'zinc', 'neutral', 'stone', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'])
                                ? $notification['color'] ?? 'slate'
                                : 'slate';
                        @endphp
                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 flex items-center justify-center" aria-hidden="true">
                            <flux:icon name="{{ $notification['icon'] ?? 'bell' }}" class="size-4 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <a
                                href="{{ $notification['link'] ?? '#' }}"
                                x-on:click="$wire.markAsRead({{ $notification['id'] }})"
                                class="block focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] rounded"
                            >
                                <p class="text-sm font-medium {{ !$notification['is_read'] ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400' }}">
                                    {{ $notification['title'] }}
                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-2 mt-0.5">
                                    {{ Str::limit($notification['content'], 80) }}
                                </p>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">
                                    <time datetime="{{ $notification['created_at'] }}">{{ $notification['created_at'] }}</time>
                                </p>
                            </a>
                        </div>

                        <!-- Unread Indicator & Actions -->
                        <div class="flex items-center gap-1">
                            @if(!$notification['is_read'])
                                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0" aria-label="{{ __('Unread') }}" title="{{ __('Unread notification') }}"></div>
                            @endif

                            <!-- Delete Button -->
                            <button
                                type="button"
                                x-on:click.stop="$wire.deleteNotification({{ $notification['id'] }})"
                                x-show="showDelete"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="p-1 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                                aria-label="{{ __('Delete notification: :title', ['title' => $notification['title']]) }}"
                            >
                                <flux:icon name="x-mark" class="size-3.5 text-zinc-400" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center" role="status">
                    <flux:icon name="bell-slash" class="size-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" aria-hidden="true" />
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No notifications yet') }}</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ __('We\'ll notify you when something arrives') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($notifications) > 0)
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
                <a
                    href="{{ route('notifications.index') }}"
                    class="flex items-center justify-center gap-2 text-sm text-[var(--color-accent)] hover:underline font-medium focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)] rounded"
                >
                    {{ __('View all notifications') }}
                    <flux:icon name="arrow-right" class="size-4" aria-hidden="true" />
                </a>
            </div>
        @endif
    </div>

    <!-- Toast Notifications -->
    <div
        x-show="showToasts.length > 0"
        class="fixed top-4 right-4 z-[100] space-y-2"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    >
        <template x-for="(toast, index) in showToasts" :key="index">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
                class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 max-w-sm"
                role="alert"
            >
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[var(--color-accent)]/10 flex items-center justify-center" aria-hidden="true">
                    <flux:icon name="bell" class="size-4 text-[var(--color-accent)]" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="toast.title"></p>
                </div>
                <button
                    type="button"
                    x-on:click="toast.visible = false"
                    class="flex-shrink-0 p-1 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                    aria-label="{{ __('Dismiss notification') }}"
                >
                    <flux:icon name="x-mark" class="size-4 text-zinc-400" aria-hidden="true" />
                </button>
            </div>
        </template>
    </div>

    <!-- Notification Sound (Web Audio API fallback) -->
    <div id="notificationSound" style="display: none;"></div>

    <script>
        let pushSubscription = null;

        // Register service worker for push notifications
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered successfully:', registration.scope);

                    // Check if push is supported
                    if ('PushManager' in window) {
                        registration.pushManager.getSubscription()
                            .then(function(subscription) {
                                if (subscription) {
                                    pushSubscription = subscription;
                                    console.log('Already subscribed to push notifications');
                                } else {
                                    console.log('Not subscribed to push notifications');
                                }
                            });
                    }
                })
                .catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
        }

        // Request push notification permission
        async function requestPushPermission() {
            if (!('Notification' in window)) {
                console.log('This browser does not support notifications');
                return false;
            }

            if (Notification.permission === 'granted') {
                return true;
            }

            if (Notification.permission !== 'denied') {
                const permission = await Notification.permission;
                if (permission === 'granted') {
                    return true;
                }
            }

            return false;
        }

        // Show browser notification (fallback for when service worker isn't available)
        function showBrowserNotification(title, options = {}) {
            if (Notification.permission === 'granted') {
                try {
                    const notification = new Notification(title, {
                        body: options.body || 'You have a new notification',
                        icon: options.icon || '/favicon.ico',
                        badge: options.badge || '/favicon.ico',
                        tag: options.tag || 'noor-notification',
                        requireInteraction: options.requireInteraction || false,
                        silent: options.silent || false,
                        ...options
                    });

                    notification.onclick = function() {
                        window.focus();
                        if (options.url) {
                            window.location.href = options.url;
                        }
                        notification.close();
                    };

                    // Auto-close after 5 seconds if not requiring interaction
                    if (!options.requireInteraction) {
                        setTimeout(() => notification.close(), 5000);
                    }
                } catch (e) {
                    console.log('Browser notification failed:', e.message);
                }
            }
        }

        function handleNotification(event) {
            console.log('Notification received:', event.detail);

            const { soundEnabled, title, type, pushEnabled } = event.detail;

            // Play sound if enabled
            if (soundEnabled) {
                console.log('Playing notification sound for type:', type);
                playNotificationSound(type || 'default');
            } else {
                console.log('Sound disabled');
            }

            // Show browser notification if push is enabled
            if (pushEnabled && Notification.permission === 'granted') {
                console.log('Showing browser notification');
                showBrowserNotification(title, {
                    body: 'You have a new notification from Noor LMS',
                    icon: '/favicon.ico',
                    url: window.location.href,
                    tag: `noor-${type}`,
                    requireInteraction: type === 'announcement' || type === 'system'
                });
            }

            // Show toast notification
            this.showToasts.push({
                title: title,
                type: type,
                visible: true
            });

            // Auto-hide toast after 5 seconds
            setTimeout(() => {
                const toast = this.showToasts.find(t => t.title === title);
                if (toast) {
                    toast.visible = false;
                }
            }, 5000);
        }

        // Enhanced Web Audio API notification sounds with different types
        function playNotificationSound(type = 'default') {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                // Different sound patterns for different notification types
                switch (type) {
                    case 'grade':
                        // Success sound - pleasant ascending tone
                        oscillator.frequency.setValueAtTime(523, audioContext.currentTime); // C5
                        oscillator.frequency.setValueAtTime(659, audioContext.currentTime + 0.1); // E5
                        oscillator.frequency.setValueAtTime(784, audioContext.currentTime + 0.2); // G5
                        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.4);
                        break;

                    case 'reminder':
                        // Reminder sound - gentle pulsing
                        oscillator.frequency.setValueAtTime(440, audioContext.currentTime); // A4
                        oscillator.frequency.setValueAtTime(440, audioContext.currentTime + 0.15);
                        oscillator.frequency.setValueAtTime(440, audioContext.currentTime + 0.3);
                        gainNode.gain.setValueAtTime(0.15, audioContext.currentTime);
                        gainNode.gain.setValueAtTime(0.05, audioContext.currentTime + 0.1);
                        gainNode.gain.setValueAtTime(0.15, audioContext.currentTime + 0.15);
                        gainNode.gain.setValueAtTime(0.05, audioContext.currentTime + 0.25);
                        gainNode.gain.setValueAtTime(0.15, audioContext.currentTime + 0.3);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.5);
                        break;

                    case 'announcement':
                        // Announcement sound - clear and attention-grabbing
                        oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5
                        oscillator.frequency.setValueAtTime(660, audioContext.currentTime + 0.1); // E5
                        oscillator.frequency.setValueAtTime(880, audioContext.currentTime + 0.2); // A5
                        gainNode.gain.setValueAtTime(0.25, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                        break;

                    default:
                        // Default notification sound
                        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.3);
                }

                console.log(`Playing ${type} notification sound`);
            } catch (e) {
                console.log('Notification sound not available:', e.message);
                // Fallback: try to play system notification sound if available
                try {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('New Notification', {
                            body: 'You have a new notification',
                            icon: '/favicon.ico',
                            silent: false
                        });
                    }
                } catch (fallbackError) {
                    console.log('Fallback notification failed:', fallbackError.message);
                }
            }
        }
    </script>
</div>
