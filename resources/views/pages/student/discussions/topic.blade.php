<?php
/**
 * Student Discussions - Topic Details Page
 *
 * Purpose: View a discussion topic and its replies, post new replies
 * Route: student.discussions.topic (GET)
 * Controller: App\Http\Controllers\Student\DiscussionController@topic
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Back navigation: Return to forum
 * - Topic header: Title, views count
 * - Original post: Author info, content, timestamps
 * - Topic badges: Pinned, locked status
 * - Replies section: List of replies with author info and content
 * - Best answer badge: Highlight best answer
 * - Reply form: Form to post new reply (if not locked)
 * - Edit/Delete actions: Own replies only
 * - Pagination: Paginated replies
 *
 * Required Data Variables:
 * - $topic: DiscussionTopic model instance
 * - $replies: Paginated collection of DiscussionReply models
 *
 * Dependencies:
 * - Routes: student.discussions.forum, student.discussions.edit-reply, student.discussions.destroy-reply, student.discussions.store-reply
 * - Models: DiscussionTopic, DiscussionReply, DiscussionForum, User
 * - Helpers: __(), route(), auth()
 */
?>
<x-layouts::app :title="$topic->title">

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center mb-2">
        <a href="{{ route('student.discussions.forum', $topic->forum) }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex-1">{{ $topic->title }}</h1>
        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span>{{ $topic->views }} {{ __('lms.views') }}</span>
        </div>
    </div>

    <!-- Original Post -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-sm">
                        <span class="text-white font-semibold text-lg">{{ substr($topic->user?->name ?? 'U', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $topic->user?->name ?? 'Unknown' }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <span>{{ $topic->created_at->format('M d, Y') }}</span>
                            <span>•</span>
                            <span>{{ $topic->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($topic->is_pinned)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v3h2a1 1 0 110 2h-2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5H1a1 1 0 110-2h2V5z"/>
                            </svg>
                            {{ __('lms.pinned') }}
                        </span>
                    @endif
                    @if($topic->isLocked())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('lms.locked') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="prose dark:prose-invert max-w-none">
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $topic->content }}</p>
            </div>
        </div>
    </div>

    <!-- Replies -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ __('lms.replies') }}
                <span class="text-lg font-normal text-gray-600 dark:text-gray-300">({{ $replies->total() }})</span>
            </h2>
        </div>

        @forelse($replies as $reply)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-sm">
                                <span class="text-white font-medium">{{ substr($reply->user?->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $reply->user?->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $reply->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if($reply->is_best_answer)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('lms.best_answer') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-5">
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $reply->content }}</p>
                    </div>
                    @if($reply->user_id == auth()->id())
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex gap-4">
                            <a href="{{ route('student.discussions.edit-reply', $reply) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{ __('lms.edit') }}
                            </a>
                            <form action="{{ route('student.discussions.destroy-reply', $reply) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition duration-200"
                                    onclick="return confirm('{{ __('lms.confirm_delete_reply') }}')">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    {{ __('lms.delete') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <div class="mx-auto w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-gray-600 dark:text-gray-300">{{ __('lms.no_replies_yet') }}</p>
            </div>
        @endforelse

        @if($replies->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $replies->links() }}
            </div>
        @endif
    </div>

    <!-- Reply Form -->
    @if(!$topic->isLocked())
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('lms.post_reply') }}</h3>
        </div>
        <div class="p-5">
            <form action="{{ route('student.discussions.store-reply', $topic) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <textarea name="content" rows="5" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="{{ __('lms.reply_placeholder') }}"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                        {{ __('lms.post_reply') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-yellow-800 dark:text-yellow-200 font-medium">{{ __('lms.topic_is_locked') }}</p>
        </div>
    </div>
    @endif
</div>
</x-layouts::app>

