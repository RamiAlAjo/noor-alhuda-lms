<?php
/**
 * Teacher Discussions - Topic Details Page
 *
 * Purpose: View a discussion topic and its replies, post replies, manage content
 * Route: teacher.discussions.topic (GET)
 * Controller: App\Http\Controllers\Teacher\DiscussionController@topic
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Topic content: Original post with author info
 * - Pinned badge: If topic is pinned
 * - Replies list: All replies with author info
 * - Delete action: Delete own replies
 * - Reply form: Post new reply
 *
 * Required Data Variables:
 * - $topic: DiscussionTopic model instance
 * - $replies: Collection of DiscussionReply models
 *
 * Dependencies:
 * - Routes: teacher.discussions.forum, teacher.discussions.delete-reply, teacher.discussions.store-reply
 * - Models: DiscussionTopic, DiscussionReply, DiscussionForum, User
 * - Helpers: __(), route(), auth()
 */
?>
<x-layouts::app :title="$topic->title">

<div class="p-6 space-y-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('teacher.discussions.forum', $topic->forum) }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex-1">{{ $topic->title }}</h1>
    </div>

    <!-- Original Post -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                    <span class="text-blue-600 dark:text-blue-400 font-medium">{{ substr($topic->user?->name ?? 'U', 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $topic->user?->name ?? 'Unknown' }}</p>
                    <p class="text-sm text-gray-500">{{ $topic->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @if($topic->is_pinned)
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    <flux:icon name="thumbtack" class="w-3 h-3 mr-1" />
                    {{ __('lms.pinned') }}
                </span>
            @endif
        </div>
        <div class="prose dark:prose-invert max-w-none">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $topic->content }}</p>
        </div>
    </div>

    <!-- Replies -->
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ __('lms.replies') }} ({{ $replies->count() }})
        </h2>

        @forelse($replies as $reply)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <span class="text-green-600 dark:text-green-400 font-medium">{{ substr($reply->user?->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $reply->user?->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-500">{{ $reply->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if($reply->user_id == auth()->id())
                        <form action="{{ route('teacher.discussions.delete-reply', $reply) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                onclick="return confirm('{{ __('lms.confirm_delete_reply') }}')">
                                <flux:icon name="trash" class="w-5 h-5" />
                            </button>
                        </form>
                    @endif
                </div>
                <div class="prose dark:prose-invert max-w-none">
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $reply->content }}</p>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">{{ __('lms.no_replies_yet') }}</p>
        @endforelse
    </div>

    <!-- Reply Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.post_reply') }}</h3>
        <form action="{{ route('teacher.discussions.store-reply', $topic) }}" method="POST">
            @csrf
            <div class="mb-4">
                <textarea name="content" rows="4" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.reply_placeholder') }}"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ __('lms.post_reply') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::app>

