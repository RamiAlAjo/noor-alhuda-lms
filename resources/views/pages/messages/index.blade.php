<x-layouts::app :title="__('Messages')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Messages') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage your conversations and messages') }}</p>
        </div>
        <div class="flex gap-3">
            <flux:button variant="outline" href="{{ route('messages.create') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                {{ __('New Message') }}
            </flux:button>
            <flux:button variant="primary" href="{{ route('messages.conversation.create') }}" x-data="{ open: false }" @click="open = true">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ __('New Group') }}
            </flux:button>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="{{ route('messages.index', ['tab' => 'conversations']) }}"
               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $tab === 'conversations' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                {{ __('Conversations') }}
            </a>
            <a href="{{ route('messages.index', ['tab' => 'inbox']) }}"
               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $tab === 'inbox' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                {{ __('Inbox') }}
            </a>
            <a href="{{ route('messages.index', ['tab' => 'sent']) }}"
               class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $tab === 'sent' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                {{ __('Sent') }}
            </a>
        </nav>
    </div>

    @if($tab === 'conversations')
        <!-- Conversations Stats -->
        <div class="mb-8 grid gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Conversations') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $conversations->total() }}</p>
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
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Unread Messages') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $conversations->sum(function ($conversation) {
                                return $conversation->getUnreadCountForUser(auth()->id());
                            }) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
                        <flux:icon name="user-group" class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Group Chats') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $conversations->filter(function ($conversation) {
                                return $conversation->is_group;
                            })->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/30">
                        <flux:icon name="chat-bubble-left-right" class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Direct Messages') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $conversations->filter(function ($conversation) {
                                return !$conversation->is_group;
                            })->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="bg-white dark:bg-stone-800 rounded-lg border border-stone-200 dark:border-stone-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                <h2 class="text-lg font-semibold text-stone-900 dark:text-stone-100">{{ __('Your Conversations') }}</h2>
            </div>

            <div class="divide-y divide-stone-200 dark:divide-stone-700">
                @forelse($conversations as $conversation)
                    <a href="{{ route('messages.conversation', $conversation->id) }}"
                       class="block p-6 hover:bg-stone-50 dark:hover:bg-stone-700/50 transition-colors {{ $conversation->getUnreadCountForUser(auth()->id()) > 0 ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if($conversation->is_group)
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                        <flux:icon name="user-group" class="size-6 text-white" />
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                                        <flux:icon name="user" class="size-6 text-white" />
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-stone-900 dark:text-stone-100">
                                            {{ $conversation->display_title }}
                                        </h3>
                                        @if($conversation->latestMessage)
                                            <p class="text-sm text-stone-600 dark:text-stone-400 mt-1 truncate">
                                                <span class="font-medium">{{ $conversation->latestMessage->sender->name }}:</span>
                                                {{ Str::limit($conversation->latestMessage->content, 60) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 ml-4">
                                        @if($conversation->latestMessage)
                                            <span class="text-xs text-stone-500 dark:text-stone-400">
                                                {{ $conversation->latestMessage->created_at->diffForHumans() }}
                                            </span>
                                        @endif

                                        @php $unreadCount = $conversation->getUnreadCountForUser(auth()->id()) @endphp
                                        @if($unreadCount > 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center">
                        <flux:icon name="chat-bubble-left-right" class="mx-auto h-12 w-12 text-stone-400" />
                        <h3 class="mt-2 text-sm font-medium text-stone-900 dark:text-stone-100">{{ __('No conversations yet') }}</h3>
                        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">{{ __('Start a conversation by clicking the buttons above') }}</p>
                    </div>
                @endforelse
            </div>

            @if($conversations->hasPages())
                <div class="px-6 py-4 border-t border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-800/50">
                    {{ $conversations->links() }}
                </div>
            @endif
        </div>

    @else
        <!-- Legacy Inbox/Sent View -->

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
@endif
</x-layouts::app>
