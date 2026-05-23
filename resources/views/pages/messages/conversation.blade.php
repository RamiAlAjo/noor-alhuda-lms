<x-layouts::app :title="$conversation->display_title">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-4">
            <a href="{{ route('messages.index') }}" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            @if($conversation->is_group)
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-purple-400 to-pink-500">
                    <flux:icon name="user-group" class="size-5 text-white" />
                </div>
            @else
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-indigo-500">
                    <flux:icon name="user" class="size-5 text-white" />
                </div>
            @endif

            <div>
                <h1 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ $conversation->display_title }}</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    @if($conversation->is_group)
                        {{ $conversation->participants->count() }} {{ __('members') }}
                    @else
                        {{ $conversation->participants->where('id', '!=', auth()->id())->first()?->name ?? __('Direct Message') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Search Messages -->
            <flux:button variant="ghost" size="sm" x-data="{ searchOpen: false, searchQuery: '', searchResults: [] }" @click="searchOpen = true">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </flux:button>

            <!-- Conversation Settings -->
            <flux:button variant="ghost" size="sm" x-data="{ open: false }" @click="open = true">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </flux:button>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="flex flex-1 overflow-hidden">
        <!-- Messages Area -->
        <div class="flex-1 flex flex-col">
            <!-- Pinned Messages -->
            @if($pinnedMessages->count() > 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-200 dark:border-yellow-800 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('Pinned Messages') }}</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($pinnedMessages as $pinnedMessage)
                            <div class="bg-white dark:bg-neutral-800 rounded-lg p-3 border border-yellow-200 dark:border-yellow-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-neutral-200 font-semibold text-xs text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ strtoupper(substr($pinnedMessage->sender->first_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $pinnedMessage->sender->name }}</span>
                                            <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $pinnedMessage->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-neutral-700 dark:text-neutral-300 line-clamp-2">{!! $pinnedMessage->formatContentWithMentions() !!}</p>
                                    </div>
                                    <button
                                        class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 unpin-btn"
                                        data-message-id="{{ $pinnedMessage->id }}"
                                        title="{{ __('Unpin message') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Messages List -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4">
                @forelse($messages->reverse() as $message)
                    @php
                        $isReply = $message->parent_id !== null;
                        $marginClass = $isReply ? 'ml-12' : '';
                    @endphp

                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} {{ $marginClass }}">
                        <div class="max-w-xs lg:max-w-md xl:max-w-lg">
                            @if($isReply)
                                <!-- Reply indicator -->
                                <div class="flex items-center gap-2 mb-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    <span>{{ __('Replying to') }} {{ $message->parent->sender->name }}</span>
                                </div>
                            @endif

                            <div class="flex items-start gap-3 {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-200 font-semibold text-xs text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ strtoupper(substr($message->sender->first_name ?? 'U', 0, 1)) }}
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $message->sender->name }}</span>
                                        <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $message->created_at->diffForHumans() }}</span>
                                        @if($message->sender_id === auth()->id())
                                            <div class="flex items-center gap-1">
                                                <!-- Status indicators for sent messages -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                @php
                                                    $readByCount = $message->conversation->participants()
                                                        ->where('users.id', '!=', $message->sender_id)
                                                        ->where('conversation_participants.last_read_at', '>', $message->created_at)
                                                        ->count();
                                                    $totalRecipients = $message->conversation->participants()->where('users.id', '!=', $message->sender_id)->count();
                                                @endphp
                                                @if($readByCount > 0)
                                                    <span class="text-xs text-blue-600 dark:text-blue-400">{{ $readByCount }}/{{ $totalRecipients }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="rounded-lg px-4 py-2 {{ $message->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-neutral-100 text-neutral-900 dark:bg-neutral-700 dark:text-neutral-100' }}">
                                        @if($message->parent)
                                            <!-- Show quoted parent message -->
                                            <div class="border-l-2 border-neutral-300 pl-3 mb-2 opacity-75 dark:border-neutral-600">
                                                <p class="text-xs text-neutral-600 dark:text-neutral-400 line-clamp-2">{{ Str::limit($message->parent->content, 100) }}</p>
                                            </div>
                                        @endif

                                        <p class="whitespace-pre-wrap text-sm">{!! $message->formatContentWithMentions() !!}</p>

                                        <!-- Attachments -->
                                        @if($message->messageAttachments->count() > 0)
                                            <div class="mt-2 space-y-1">
                                                @foreach($message->messageAttachments as $attachment)
                                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="flex items-center gap-2 text-xs underline {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-blue-600 dark:text-blue-400' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        {{ $attachment->original_filename }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Message Reactions -->
                                    @php
                                        $reactionSummary = $message->reactions->groupBy('reaction_type')->map(function($group) {
                                            return ['count' => $group->count(), 'users' => $group->pluck('user.name')->toArray()];
                                        });
                                        $userReactions = $message->reactions->where('user_id', auth()->id())->pluck('reaction_type')->toArray();
                                    @endphp

                                    @if($reactionSummary->count() > 0)
                                        <div class="flex items-center gap-1 mt-2 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                            @foreach($reactionSummary as $type => $data)
                                                <button
                                                    class="flex items-center gap-1 px-2 py-1 text-xs rounded-full border {{ in_array($type, $userReactions) ? 'bg-blue-100 border-blue-300 text-blue-700 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-300' : 'bg-neutral-100 border-neutral-300 text-neutral-600 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-400' }} hover:bg-neutral-200 dark:hover:bg-neutral-600 reaction-btn"
                                                    data-message-id="{{ $message->id }}"
                                                    data-reaction-type="{{ $type }}"
                                                >
                                                    <span>{{ $type }}</span>
                                                    <span>{{ $data['count'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Message Actions -->
                                    <div class="flex items-center gap-1 mt-1 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                        <!-- Add Reactions -->
                                        <div class="relative">
                                            <button class="text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 reaction-menu-btn" data-message-id="{{ $message->id }}">
                                                😊
                                            </button>
                                            <div class="reaction-menu absolute bottom-full mb-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg p-2 shadow-lg hidden z-10">
                                                <div class="flex gap-1">
                                                    <button class="text-lg hover:scale-110 transition-transform reaction-option" data-reaction-type="👍" data-message-id="{{ $message->id }}">👍</button>
                                                    <button class="text-lg hover:scale-110 transition-transform reaction-option" data-reaction-type="❤️" data-message-id="{{ $message->id }}">❤️</button>
                                                    <button class="text-lg hover:scale-110 transition-transform reaction-option" data-reaction-type="😂" data-message-id="{{ $message->id }}">😂</button>
                                                    <button class="text-lg hover:scale-110 transition-transform reaction-option" data-reaction-type="😮" data-message-id="{{ $message->id }}">😮</button>
                                                    <button class="text-lg hover:scale-110 transition-transform reaction-option" data-reaction-type="😢" data-message-id="{{ $message->id }}">😢</button>
                                                    <button class="text-lg hover:scale-110 transition-transform reaction-option" data-reaction-type="👎" data-message-id="{{ $message->id }}">👎</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pin/Unpin -->
                                        @php
                                            $isAdmin = !$message->conversation->is_group || $message->conversation->participants()->where('users.id', auth()->id())->where('conversation_participants.is_admin', true)->exists();
                                        @endphp
                                        @if($isAdmin)
                                            @if($message->is_pinned)
                                                <button
                                                    class="text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 ml-2 unpin-btn"
                                                    data-message-id="{{ $message->id }}"
                                                    title="{{ __('Unpin message') }}"
                                                >
                                                    📌
                                                </button>
                                            @else
                                                <button
                                                    class="text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 ml-2 pin-btn"
                                                    data-message-id="{{ $message->id }}"
                                                    title="{{ __('Pin message') }}"
                                                >
                                                    📌
                                                </button>
                                            @endif
                                        @endif

                                        <!-- Forward -->
                                        <button
                                            data-forward="{{ $message->id }}"
                                            class="text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 ml-2 forward-button"
                                        >
                                            {{ __('Forward') }}
                                        </button>

                                        <!-- Reply -->
                                        <button
                                            data-reply="{{ $message->id }}"
                                            class="text-xs text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200 ml-2 reply-button"
                                        >
                                            {{ __('Reply') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No messages yet') }}</h3>
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('Start the conversation by sending a message below') }}</p>
                    </div>
                @endforelse
            </div>

            <!-- Typing Indicators -->
            <div id="typing-indicators" class="border-t border-neutral-200 bg-white px-4 py-2 dark:border-neutral-700 dark:bg-neutral-800 hidden">
                <div class="flex items-center gap-2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-neutral-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-neutral-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-neutral-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                    <span id="typing-text" class="text-sm text-neutral-500 dark:text-neutral-400"></span>
                </div>
            </div>

            <!-- Message Input -->
            <div class="border-t border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
                <form action="{{ route('messages.send', $conversation->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf

                    <!-- Reply Context -->
                    <div id="reply-context" class="hidden rounded-lg border border-neutral-200 bg-neutral-50 p-3 dark:border-neutral-700 dark:bg-neutral-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Replying to') }} <span id="reply-to-name"></span></span>
                            </div>
                            <button type="button" id="cancel-reply" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400" id="reply-to-content"></p>
                        <input type="hidden" name="parent_id" id="parent-id">
                    </div>

                    <!-- Message Input -->
                    <div class="flex items-end gap-3">
                        <!-- File Upload -->
                        <label class="flex-shrink-0">
                            <input type="file" name="attachments[]" multiple class="hidden" id="file-input">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </div>
                        </label>

                        <!-- Text Input -->
                        <div class="flex-1">
                            <textarea
                                name="content"
                                rows="1"
                                class="w-full resize-none rounded-lg border border-neutral-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                placeholder="{{ __('Type a message...') }}"
                                required
                                onkeydown="if(event.key === 'Enter' && !event.shiftKey) { event.preventDefault(); this.form.submit(); }"
                            ></textarea>
                        </div>

                        <!-- Send Button -->
                        <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>

                    <!-- File Preview -->
                    <div id="file-preview" class="hidden space-y-2"></div>
                </form>
            </div>
        </div>

        <!-- Participants Sidebar (for group chats) -->
        @if($conversation->is_group)
            <div class="w-64 border-l border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
                <div class="p-4 border-b border-neutral-200 dark:border-neutral-700">
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Members') }} ({{ $conversation->participants->count() }})</h3>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($conversation->participants as $participant)
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-200 font-semibold text-xs text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                    {{ strtoupper(substr($participant->first_name ?? 'U', 0, 1)) }}
                                </div>
                                @if($participant->isOnline())
                                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white dark:border-neutral-800 rounded-full"></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $participant->name }}</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                    @if($participant->isOnline())
                                        {{ __('Online') }}
                                    @else
                                        {{ __('Offline') }}
                                    @endif
                                    • {{ $participant->roles->first()?->name ?? 'Member' }}
                                </p>
                            </div>
                            @if($participant->id === $conversation->created_by)
                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ __('Admin') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Message Search Modal -->
    <div x-show="searchOpen" class="fixed inset-0 z-50 overflow-y-auto" x-data="{ searchOpen: false, searchQuery: '', searchResults: [], isSearching: false }">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="searchOpen = false"></div>
            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-neutral-800 sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
                <div class="bg-white px-4 pt-5 pb-4 dark:bg-neutral-800 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-neutral-100">{{ __('Search Messages') }}</h3>
                    <div class="mt-4">
                        <div class="relative">
                            <input
                                type="text"
                                x-model="searchQuery"
                                @input.debounce.300ms="performSearch"
                                class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2 focus:border-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700"
                                placeholder="{{ __('Search in this conversation...') }}"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Search Results -->
                    <div x-show="searchResults.length > 0" class="mt-4 max-h-96 overflow-y-auto">
                        <div class="space-y-2">
                            <template x-for="result in searchResults" :key="result.id">
                                <div class="rounded-lg border border-gray-200 p-3 hover:bg-gray-50 dark:border-neutral-600 dark:hover:bg-neutral-700">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-neutral-100" x-text="result.sender.name"></span>
                                        <span class="text-xs text-gray-500 dark:text-neutral-400" x-text="formatDate(result.created_at)"></span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-neutral-300" x-html="highlightSearch(result.content, searchQuery)"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="searchQuery && searchResults.length === 0 && !isSearching" class="mt-4 text-center text-gray-500 dark:text-neutral-400">
                        {{ __('No messages found') }}
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 dark:bg-neutral-700 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 sm:mt-0 sm:w-auto sm:text-sm" @click="searchOpen = false; searchQuery = ''; searchResults = []">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Forward Message Modal -->
    <div x-show="forwardModal" class="fixed inset-0 z-50 overflow-y-auto" x-data="{ forwardModal: false, selectedConversation: null }">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="forwardModal = false"></div>
            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-neutral-800 sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <div class="bg-white px-4 pt-5 pb-4 dark:bg-neutral-800 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-neutral-100">{{ __('Forward Message') }}</h3>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mb-4">{{ __('Select a conversation to forward this message to:') }}</p>

                        <!-- Conversations List -->
                        <div class="max-h-60 overflow-y-auto space-y-2">
                            @php
                                $userConversations = auth()->user()->conversations()->where('conversations.id', '!=', $conversation->id)->get();
                            @endphp
                            @forelse($userConversations as $conv)
                                <div
                                    class="p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:border-neutral-600 dark:hover:bg-neutral-700"
                                    @click="selectedConversation = {{ $conv->id }}; forwardModal = false; forwardMessage({{ $conv->id }})"
                                >
                                    <div class="flex items-center gap-3">
                                        @if($conv->is_group)
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                                                <flux:icon name="user-group" class="size-4 text-purple-600 dark:text-purple-400" />
                                            </div>
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                                <flux:icon name="user" class="size-4 text-blue-600 dark:text-blue-400" />
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-neutral-100">{{ $conv->display_title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                                {{ $conv->is_group ? $conv->participants->count() . ' members' : 'Direct message' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-neutral-400 text-center py-4">{{ __('No other conversations available') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 dark:bg-neutral-700 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 sm:mt-0 sm:w-auto sm:text-sm" @click="forwardModal = false">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversation Settings Modal -->
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: false }">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-neutral-800 sm:my-8 sm:w-full sm:max-w-md sm:align-middle">
                <div class="bg-white px-4 pt-5 pb-4 dark:bg-neutral-800 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-neutral-100">{{ __('Conversation Settings') }}</h3>
                    <div class="mt-4 space-y-4">
                        @if($conversation->is_group)
                            <!-- Group Info -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">{{ __('Group Name') }}</label>
                                    <form action="#" method="POST" class="flex gap-2" onsubmit="updateGroupName(event, {{ $conversation->id }})">
                                        @csrf
                                        <input type="text" value="{{ $conversation->title }}" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100" id="groupNameInput">
                                         <x-button.submit loading-text="{{ __('Updating...') }}" class="px-3 py-2 text-sm">
                                             {{ __('Update') }}
                                         </x-button.submit>
                                    </form>
                                </div>

                                <p class="text-sm text-gray-600 dark:text-neutral-400">{{ __('Group conversation with') }} {{ $conversation->participants->count() }} {{ __('members') }}</p>

                                <!-- Members List -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-neutral-100 mb-2">{{ __('Members') }}</h4>
                                    <div class="max-h-32 overflow-y-auto space-y-1">
                                        @foreach($conversation->participants as $participant)
                                            <div class="flex items-center justify-between py-1">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs">
                                                        {{ strtoupper(substr($participant->name, 0, 1)) }}
                                                    </div>
                                                    <span class="text-sm text-gray-900 dark:text-neutral-100">{{ $participant->name }}</span>
                                                    @if($participant->pivot->is_admin)
                                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-200">{{ __('Admin') }}</span>
                                                    @endif
                                                </div>
                                                @php
                                                    $isAdmin = $conversation->participants()->where('users.id', auth()->id())->where('conversation_participants.is_admin', true)->exists();
                                                @endphp
                                                @if($isAdmin && $participant->id !== auth()->id())
                                                    <form action="#" method="POST" class="inline" onsubmit="removeMember(event, {{ $conversation->id }}, {{ $participant->id }})">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs" onclick="return confirm('{{ __('Remove') }} {{ $participant->name }} {{ __('from the group?') }}')">
                                                            {{ __('Remove') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                @php
                                    $isAdmin = $conversation->participants()->where('users.id', auth()->id())->where('conversation_participants.is_admin', true)->exists();
                                @endphp
                                @if($isAdmin)
                                    <!-- Add Member -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-1">{{ __('Add Member') }}</label>
                                        <form action="#" method="POST" class="flex gap-2" onsubmit="addMember(event, {{ $conversation->id }})">
                                            @csrf
                                            <select class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100" id="addMemberSelect" required>
                                                <option value="">{{ __('Select user to add') }}</option>
                                                @php
                                                    $existingUserIds = $conversation->participants->pluck('id')->toArray();
                                                    $availableUsers = \App\Models\User::whereNotIn('id', $existingUserIds)->get();
                                                @endphp
                                                @foreach($availableUsers as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                                {{ __('Add') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="border-t border-gray-200 dark:border-neutral-600 pt-3 space-y-3">
                                <!-- Leave Group -->
                                <form action="#" method="POST" onsubmit="leaveGroup(event, {{ $conversation->id }})">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('{{ __('Are you sure you want to leave this group?') }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        {{ __('Leave Group') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-sm text-gray-600 dark:text-neutral-400">{{ __('Direct conversation') }}</p>
                        @endif

                        <div class="border-t border-gray-200 dark:border-neutral-600 pt-3">
                            @if($conversation->isArchived())
                                <form action="{{ route('messages.conversation.unarchive', $conversation->id) }}" method="PATCH" class="inline">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        {{ __('Unarchive Conversation') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('messages.conversation.archive', $conversation->id) }}" method="PATCH" class="inline">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 text-sm text-orange-600 hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300" onclick="return confirm('{{ __('Are you sure you want to archive this conversation?') }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        {{ __('Archive Conversation') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 dark:bg-neutral-700 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 sm:mt-0 sm:w-auto sm:text-sm" @click="open = false">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto scroll to bottom on load
        document.getElementById('messages-container').scrollTop = document.getElementById('messages-container').scrollHeight;

        // Mention autocomplete
        let mentionParticipants = @json($conversation->participants->map(function($p) {
            return ['id' => $p->id, 'name' => $p->name];
        }));

        // Typing indicators
        let typingUsers = [];
        let typingTimeout;

        // File upload preview
        document.getElementById('file-input').addEventListener('change', function(e) {
            const files = e.target.files;
            const preview = document.getElementById('file-preview');
            preview.innerHTML = '';

            if (files.length > 0) {
                preview.classList.remove('hidden');
                for (let file of files) {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400';
                    div.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        ${file.name}
                    `;
                    preview.appendChild(div);
                }
            } else {
                preview.classList.add('hidden');
            }
        });

        // Forward functionality
        let forwardMessageId = null;

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('forward-button') || e.target.closest('.forward-button')) {
                const button = e.target.classList.contains('forward-button') ? e.target : e.target.closest('.forward-button');
                forwardMessageId = button.dataset.forward;
                document.querySelector('[x-data*="forwardModal"]').__x.$data.forwardModal = true;
            }
        });

        function forwardMessage(targetConversationId) {
            if (!forwardMessageId) return;

            fetch(`/messages/${forwardMessageId}/forward`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    conversation_id: targetConversationId,
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message forwarded successfully');
                } else {
                    alert(data.error || 'Failed to forward message');
                }
            })
            .catch(error => console.error('Forward error:', error));
        }

        // Group management functions
        function updateGroupName(event, conversationId) {
            event.preventDefault();
            const nameInput = document.getElementById('groupNameInput');
            const newName = nameInput.value.trim();

            if (!newName) {
                alert('{{ __("Group name cannot be empty") }}');
                return;
            }

            fetch(`/messages/conversation/${conversationId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ title: newName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to update group name');
                }
            })
            .catch(error => console.error('Update group name error:', error));
        }

        function addMember(event, conversationId) {
            event.preventDefault();
            const select = document.getElementById('addMemberSelect');
            const userId = select.value;

            if (!userId) return;

            fetch(`/messages/conversation/${conversationId}/participants`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to add member');
                }
            })
            .catch(error => console.error('Add member error:', error));
        }

        function removeMember(event, conversationId, userId) {
            event.preventDefault();

            fetch(`/messages/conversation/${conversationId}/participants/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to remove member');
                }
            })
            .catch(error => console.error('Remove member error:', error));
        }

        function leaveGroup(event, conversationId) {
            event.preventDefault();

            fetch(`/messages/conversation/${conversationId}/leave`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("messages.index") }}';
                } else {
                    alert(data.error || 'Failed to leave group');
                }
            })
            .catch(error => console.error('Leave group error:', error));
        }
            })
            .catch(error => console.error('Forward error:', error));

            forwardMessageId = null;
        }

        // Reply functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('reply-button') || e.target.closest('.reply-button')) {
                const button = e.target.classList.contains('reply-button') ? e.target : e.target.closest('.reply-button');
                const messageId = button.dataset.reply;
                const messageElement = button.closest('.flex');

                // Find the sender name and content within the message element
                const senderName = messageElement.querySelector('.text-sm.font-medium')?.textContent || 'Unknown';
                const messageContent = messageElement.querySelector('p.whitespace-pre-wrap')?.textContent.trim() || '';

                document.getElementById('reply-to-name').textContent = senderName;
                document.getElementById('reply-to-content').textContent = messageContent.substring(0, 100) + (messageContent.length > 100 ? '...' : '');
                document.getElementById('parent-id').value = messageId;
                document.getElementById('reply-context').classList.remove('hidden');
                document.querySelector('textarea[name="content"]').focus();
            }
        });

        // Cancel reply
        document.getElementById('cancel-reply').addEventListener('click', function() {
            document.getElementById('reply-context').classList.add('hidden');
            document.getElementById('parent-id').value = '';
        });

        // Message search functionality
        function performSearch() {
            const query = this.searchQuery;
            if (query.length < 1) {
                this.searchResults = [];
                return;
            }

            this.isSearching = true;

            fetch(`{{ route('messages.conversation.search', $conversation->id) }}?query=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                this.isSearching = false;
                if (data.success) {
                    this.searchResults = data.results;
                }
            })
            .catch(error => {
                this.isSearching = false;
                console.error('Search error:', error);
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }

        function highlightSearch(text, query) {
            if (!query) return text;
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<mark class="bg-yellow-200 dark:bg-yellow-800">$1</mark>');
        }

        // Mention autocomplete
        const textarea = document.querySelector('textarea[name="content"]');
        const mentionDropdown = document.createElement('div');
        mentionDropdown.className = 'absolute z-10 bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto hidden';
        mentionDropdown.style.bottom = '100%';
        mentionDropdown.style.left = '0';
        mentionDropdown.style.width = '200px';

        textarea.parentNode.style.position = 'relative';
        textarea.parentNode.appendChild(mentionDropdown);

        textarea.addEventListener('input', function(e) {
            const cursorPos = this.selectionStart;
            const text = this.value;
            const beforeCursor = text.substring(0, cursorPos);
            const match = beforeCursor.match(/@(\w*)$/);

            if (match) {
                const query = match[1].toLowerCase();
                const matches = mentionParticipants.filter(p =>
                    p.name.toLowerCase().includes(query)
                ).slice(0, 5);

                if (matches.length > 0) {
                    mentionDropdown.innerHTML = matches.map(p =>
                        `<div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-mention="${p.name}">${p.name}</div>`
                    ).join('');
                    mentionDropdown.classList.remove('hidden');
                } else {
                    mentionDropdown.classList.add('hidden');
                }
            } else {
                mentionDropdown.classList.add('hidden');
            }
        });

        mentionDropdown.addEventListener('click', function(e) {
            if (e.target.dataset.mention) {
                const mention = '@' + e.target.dataset.mention;
                const textarea = document.querySelector('textarea[name="content"]');
                const cursorPos = textarea.selectionStart;
                const text = textarea.value;
                const beforeCursor = text.substring(0, cursorPos);
                const afterCursor = text.substring(cursorPos);

                // Replace the @query with @name
                const newBefore = beforeCursor.replace(/@\w*$/, mention + ' ');
                textarea.value = newBefore + afterCursor;
                textarea.selectionStart = textarea.selectionEnd = newBefore.length;
                mentionDropdown.classList.add('hidden');
                textarea.focus();
            }
        });

        // Hide dropdown when clicking elsewhere
        document.addEventListener('click', function(e) {
            if (!textarea.contains(e.target) && !mentionDropdown.contains(e.target)) {
                mentionDropdown.classList.add('hidden');
            }
        });

        // Reaction functionality
        document.addEventListener('click', function(e) {
            // Show reaction menu
            if (e.target.classList.contains('reaction-menu-btn')) {
                const messageId = e.target.dataset.messageId;
                const menu = e.target.nextElementSibling;
                document.querySelectorAll('.reaction-menu').forEach(m => m.classList.add('hidden'));
                menu.classList.remove('hidden');
                e.stopPropagation();
            }
            // Hide reaction menus when clicking elsewhere
            else if (!e.target.closest('.reaction-menu') && !e.target.classList.contains('reaction-menu-btn')) {
                document.querySelectorAll('.reaction-menu').forEach(m => m.classList.add('hidden'));
            }

            // Handle reaction clicks
            if (e.target.classList.contains('reaction-option')) {
                const messageId = e.target.dataset.messageId;
                const reactionType = e.target.dataset.reactionType;

                toggleReaction(messageId, reactionType);
                document.querySelectorAll('.reaction-menu').forEach(m => m.classList.add('hidden'));
            }

            // Handle existing reaction clicks
            if (e.target.closest('.reaction-btn')) {
                const button = e.target.closest('.reaction-btn');
                const messageId = button.dataset.messageId;
                const reactionType = button.dataset.reactionType;

                toggleReaction(messageId, reactionType);
            }
        });

        function toggleReaction(messageId, reactionType) {
            fetch(`/messages/${messageId}/reaction`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ reaction_type: reactionType })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show updated reactions
                    location.reload();
                }
            })
            .catch(error => console.error('Reaction error:', error));
        }

        // Pin/Unpin functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('pin-btn')) {
                const messageId = e.target.dataset.messageId;
                pinMessage(messageId);
            }

            if (e.target.classList.contains('unpin-btn')) {
                const messageId = e.target.dataset.messageId;
                unpinMessage(messageId);
            }
        });

        function pinMessage(messageId) {
            fetch(`/messages/${messageId}/pin`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to pin message');
                }
            })
            .catch(error => console.error('Pin error:', error));
        }

        function unpinMessage(messageId) {
            fetch(`/messages/${messageId}/unpin`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Failed to unpin message');
                }
            })
            .catch(error => console.error('Unpin error:', error));
        }

        // Typing functionality
        const messageInput = document.querySelector('textarea[name="content"]');

        messageInput.addEventListener('input', function() {
            clearTimeout(typingTimeout);
            startTyping();

            typingTimeout = setTimeout(() => {
                stopTyping();
            }, 3000); // Stop typing after 3 seconds of inactivity
        });

        messageInput.addEventListener('blur', function() {
            stopTyping();
        });

        function startTyping() {
            fetch(`{{ route('messages.typing.start', $conversation->id) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            }).catch(error => console.error('Typing start error:', error));
        }

        function stopTyping() {
            fetch(`{{ route('messages.typing.stop', $conversation->id) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            }).catch(error => console.error('Typing stop error:', error));
        }

        function updateTypingIndicators(typingUsers) {
            const indicator = document.getElementById('typing-indicators');
            const text = document.getElementById('typing-text');

            if (typingUsers && typingUsers.length > 0) {
                const names = typingUsers.map(u => u.name).join(', ');
                text.textContent = names + (typingUsers.length === 1 ? ' is typing...' : ' are typing...');
                indicator.classList.remove('hidden');
            } else {
                indicator.classList.add('hidden');
            }
        }

        // Poll for typing users
        setInterval(() => {
            fetch(`{{ route('messages.conversation.typing', $conversation->id) }}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateTypingIndicators(data.typing_users);
                }
            })
            .catch(error => console.error('Typing poll error:', error));
        }, 2000);
    </script>
</x-layouts::app>