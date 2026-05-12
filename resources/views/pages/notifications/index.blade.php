{{--
    User Notifications Page
--}}
<x-layouts::app :title="__('Notifications')">
    <div class="mb-8">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-white">{{ __('Dashboard') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Notifications') }}</span>
        </nav>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="mt-4 text-3xl font-bold text-stone-900 dark:text-stone-100">{{ __('Notifications') }}</h1>
                <p class="mt-1 text-stone-500 dark:text-stone-400">{{ __('Stay updated with your latest notifications') }}</p>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-sm text-stone-500 dark:text-stone-400">
                    {{ auth()->user()->notifications()->unread()->count() }} {{ __('unread') }}
                </span>
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <flux:button type="submit" variant="outline" size="sm">
                        {{ __('Mark All Read') }}
                    </flux:button>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Statistics -->
    <div class="grid gap-4 md:grid-cols-4 mb-8">
        <div class="bg-white dark:bg-stone-800 rounded-lg p-4 border border-stone-200 dark:border-stone-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon name="bell" class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-stone-900 dark:text-stone-100">{{ auth()->user()->notifications()->count() }}</p>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Total') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-stone-800 rounded-lg p-4 border border-stone-200 dark:border-stone-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg">
                    <flux:icon name="eye-slash" class="size-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-stone-900 dark:text-stone-100">{{ auth()->user()->notifications()->unread()->count() }}</p>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Unread') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-stone-800 rounded-lg p-4 border border-stone-200 dark:border-stone-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon name="eye" class="size-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-stone-900 dark:text-stone-100">{{ auth()->user()->notifications()->where('is_read', true)->count() }}</p>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('Read') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-stone-800 rounded-lg p-4 border border-stone-200 dark:border-stone-700">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <flux:icon name="calendar-days" class="size-5 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-stone-900 dark:text-stone-100">{{ auth()->user()->notifications()->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
                    <p class="text-sm text-stone-500 dark:text-stone-400">{{ __('This Week') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-stone-800 rounded-lg border border-stone-200 dark:border-stone-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
            <h2 class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('All Notifications') }}</h2>
        </div>

        <div class="divide-y divide-stone-200 dark:divide-stone-700">
            @forelse(auth()->user()->notifications()->orderBy('created_at', 'desc')->paginate(20) as $notification)
                <div class="p-6 hover:bg-stone-50 dark:hover:bg-stone-700/50 transition-colors {{ !$notification->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @php
                                $typeConfig = \App\Models\Notification::getTypeConfig($notification->type);
                                $color = in_array($notification->color ?? 'slate', ['slate', 'gray', 'zinc', 'neutral', 'stone', 'red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose'])
                                    ? $notification->color ?? 'slate'
                                    : 'slate';
                            @endphp
                            <div class="w-10 h-10 rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 flex items-center justify-center">
                                <flux:icon name="{{ $notification->icon }}" class="size-5 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-stone-900 dark:text-stone-100 {{ !$notification->is_read ? 'font-semibold' : '' }}">
                                        {{ $notification->title }}
                                    </h3>
                                    <p class="text-sm text-stone-600 dark:text-stone-400 mt-1">
                                        {{ $notification->content }}
                                    </p>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-stone-500 dark:text-stone-400">
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        <span class="capitalize">{{ $typeConfig['label'] }}</span>
                                        @if($notification->link)
                                            <a href="{{ $notification->link }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                {{ __('View') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 ml-4">
                                    @if(!$notification->is_read)
                                        <div class="w-2 h-2 bg-blue-500 rounded-full" title="{{ __('Unread') }}"></div>
                                    @endif

                                    <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-stone-400 hover:text-stone-600 dark:text-stone-500 dark:hover:text-stone-300" title="{{ __('Mark as Read') }}">
                                            <flux:icon name="eye" class="size-4" />
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('notifications.delete', $notification) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-stone-400 hover:text-red-600 dark:text-stone-500 dark:hover:text-red-400" title="{{ __('Delete') }}" onclick="return confirm('{{ __('Are you sure you want to delete this notification?') }}')">
                                            <flux:icon name="trash" class="size-4" />
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <flux:icon name="bell-slash" class="mx-auto h-12 w-12 text-stone-400" />
                    <h3 class="mt-2 text-sm font-medium text-stone-900 dark:text-stone-100">{{ __('No notifications') }}</h3>
                    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">{{ __('You\'re all caught up!') }}</p>
                </div>
            @endforelse
        </div>

        @if(auth()->user()->notifications()->count() > 20)
            <div class="px-6 py-4 border-t border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800/50">
                {{ auth()->user()->notifications()->orderBy('created_at', 'desc')->paginate(20)->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>