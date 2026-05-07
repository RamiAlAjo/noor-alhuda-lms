<x-slot name="title">{{ __('lms.discussion_forums') }} - {{ $courseOffering->course?->name ?? __('Course') }}</x-slot>

<div class="p-6 space-y-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('student.discussions.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.discussion_forums') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ $courseOffering->course?->code ?? '' }} - {{ $courseOffering->course?->name ?? __('Course') }}</p>
        </div>
    </div>

    @if($forums->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <flux:icon name="chat-bubble-left-right" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-600 dark:text-gray-300">{{ __('lms.no_forums_yet') }}</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($forums as $forum)
                <a href="{{ route('student.discussions.forum', $forum) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $forum->title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">
                                {{ $forum->description }}
                            </p>
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-300">
                                <span class="flex items-center gap-1">
                                    <flux:icon name="chat-bubble-left" class="w-4 h-4" />
                                    {{ $forum->topics->count() }} {{ __('lms.topics') }}
                                </span>
                                @if($forum->is_locked)
                                    <span class="flex items-center gap-1 text-red-500">
                                        <flux:icon name="lock-closed" class="w-4 h-4" />
                                        {{ __('lms.locked') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <flux:icon name="chevron-right" class="w-5 h-5 text-gray-400" />
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $forums->links() }}
        </div>
    @endif
</div>
