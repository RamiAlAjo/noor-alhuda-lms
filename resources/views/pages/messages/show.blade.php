<x-layouts::app :title="$message->subject">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('messages.index') }}" class="inline-flex items-center text-sm text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            {{ __('Back to Inbox') }}
        </a>
    </div>

    <!-- Message Header -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white shadow-lg dark:border-neutral-700">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold">{{ $message->subject }}</h1>
                    <p class="mt-1 text-sm text-blue-100">
                        {{ __('From') }}: {{ $message->sender->full_name }} | {{ __('To') }}: {{ $message->receiver->full_name }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-sm text-blue-100">{{ $message->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-4">
        <!-- Message Content -->
        <div class="lg:col-span-3">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 p-6 dark:border-neutral-700">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ strtoupper(substr($message->sender->first_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ $message->sender->full_name }}</h3>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $message->sender->roles->first()?->name ?? 'User' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <flux:button variant="ghost" size="sm" href="{{ route('messages.create', ['user_id' => $message->sender_id]) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                {{ __('Reply') }}
                            </flux:button>
                            <form action="{{ route('messages.destroy', $message->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <flux:button variant="ghost" size="sm" class="text-red-600 hover:text-red-700 dark:text-red-400" onclick="return confirm('{{ __('Are you sure?') }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </flux:button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="message-content">
                        <p class="whitespace-pre-wrap text-neutral-800 leading-relaxed dark:text-neutral-200">{{ $message->content }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Info Sidebar -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="mb-4 font-semibold text-neutral-900 dark:text-neutral-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline size-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ __('Message Info') }}
            </h3>
            <ul class="space-y-4">
                <li>
                    <span class="block text-sm text-neutral-500 dark:text-neutral-400">{{ __('From') }}</span>
                    <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $message->sender->full_name }}</span>
                </li>
                <li>
                    <span class="block text-sm text-neutral-500 dark:text-neutral-400">{{ __('To') }}</span>
                    <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $message->receiver->full_name }}</span>
                </li>
                <li>
                    <span class="block text-sm text-neutral-500 dark:text-neutral-400">{{ __('Date') }}</span>
                    <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $message->created_at->format('Y-m-d') }}</span>
                </li>
                <li>
                    <span class="block text-sm text-neutral-500 dark:text-neutral-400">{{ __('Time') }}</span>
                    <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $message->created_at->format('H:i') }}</span>
                </li>
            </ul>
        </div>
    </div>
</x-layouts::app>
