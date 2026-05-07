<div role="region" aria-label="{{ __('Notifications panel') }}">
    @if($notifications->isEmpty())
        <div class="text-center py-8" role="status">
            <flux:icon name="bell-slash" class="size-12 text-zinc-400 dark:text-zinc-500 mx-auto" aria-hidden="true" />
            <p class="mt-3 text-zinc-500 dark:text-zinc-400">{{ __('No notifications') }}</p>
        </div>
    @else
        <ul class="space-y-1" role="list" aria-label="{{ __('Notifications list') }}">
            @foreach($notifications as $notification)
                <li class="flex items-start gap-3 p-3 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors duration-200 focus-within:ring-2 focus-within:ring-[var(--color-accent)]">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center" aria-hidden="true">
                            <flux:icon name="megaphone" class="size-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-1">
                            {{ $notification->title }}
                        </h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-1 line-clamp-2">
                            {{ Str::limit($notification->content, 100) }}
                        </p>
                        <div class="flex items-center gap-1 text-xs text-zinc-400 dark:text-zinc-500">
                            <flux:icon name="clock" class="size-3" aria-hidden="true" />
                            <time datetime="{{ $notification->created_at->toIso8601String() }}">
                                {{ $notification->created_at->diffForHumans() }}
                            </time>
                        </div>
                    </div>
                    @if($notification->is_pinned)
                        <div class="flex-shrink-0" title="{{ __('Pinned notification') }}">
                            <flux:icon name="star" class="size-4 text-amber-500" aria-label="{{ __('Pinned') }}" />
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>

        @if($notifications->count() > 0)
            <div class="pt-3 mt-3 border-t border-zinc-200 dark:border-zinc-700 text-center">
                <flux:button variant="subtle" size="sm" :href="route('admin.announcements.index')" class="focus:ring-2 focus:ring-[var(--color-accent)]">
                    {{ __('View All Announcements') }}
                    <flux:icon name="arrow-right" class="size-4 ms-1" aria-hidden="true" />
                </flux:button>
            </div>
        @endif
    @endif
</div>
