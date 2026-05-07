<x-layouts::app :title="__('Messages')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Messages') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage your conversations') }}</p>
        </div>
        <flux:button variant="primary" href="{{ route('messages.create') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Compose') }}
        </flux:button>
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Inbox') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $messages->count() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-red-100 p-3 dark:bg-red-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Unread') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $messages->where('is_read', false)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Sent') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $sentMessages->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Inbox -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 overflow-hidden">
            <div class="border-b border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-800/50">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Inbox') }}</h3>
                    @if($messages->where('is_read', false)->count() > 0)
                        <span class="ml-auto rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            {{ $messages->where('is_read', false)->count() }} {{ __('new') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="max-h-[500px] overflow-y-auto">
                @if($messages->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No messages yet') }}</h3>
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('Your inbox is empty') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($messages as $message)
                            <a href="{{ route('messages.show', $message->id) }}"
                               class="flex items-start gap-4 px-6 py-4 transition-all hover:bg-neutral-50 dark:hover:bg-neutral-700/50 {{ !$message->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-neutral-100 font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                    {{ strtoupper(substr($message->sender->first_name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if(!$message->is_read)
                                            <span class="h-2 w-2 rounded-full bg-blue-600 dark:bg-blue-400"></span>
                                        @endif
                                        <span class="font-semibold text-neutral-900 dark:text-neutral-100 {{ !$message->is_read ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                            {{ $message->sender->full_name }}
                                        </span>
                                        @if(!$message->is_read)
                                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ __('New') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ $message->subject }}</p>
                                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 truncate">{{ Str::limit($message->content, 60) }}</p>
                                </div>
                                <div class="flex flex-col items-end text-sm text-neutral-400 dark:text-neutral-500">
                                    <span>{{ $message->created_at->format('M d') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sent Messages -->
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 overflow-hidden">
            <div class="border-b border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-800/50">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Sent') }}</h3>
                    @if($sentMessages->count() > 0)
                        <span class="ml-auto rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300">
                            {{ $sentMessages->count() }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="max-h-[500px] overflow-y-auto">
                @if($sentMessages->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No sent messages') }}</h3>
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('You haven\'t sent any messages yet') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-neutral-100 dark:divide-neutral-700">
                        @foreach($sentMessages as $message)
                            <a href="{{ route('messages.show', $message->id) }}"
                               class="flex items-start gap-4 px-6 py-4 transition-all hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-green-100 font-semibold text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                    {{ strtoupper(substr($message->receiver->first_name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $message->receiver->full_name }}</p>
                                    <p class="font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ $message->subject }}</p>
                                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 truncate">{{ Str::limit($message->content, 60) }}</p>
                                </div>
                                <div class="flex flex-col items-end text-sm text-neutral-400 dark:text-neutral-500">
                                    <span>{{ $message->created_at->format('M d') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts::app>
