<?php
/**
 * Student Discussions - Forum Topics Page
 *
 * Purpose: View all topics within a discussion forum
 * Route: student.discussions.forum (GET)
 * Controller: App\Http\Controllers\Student\DiscussionController@forum
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Back navigation: Return to forums list
 * - Forum header: Title, course, description
 * - Locked indicator: Show if forum is locked
 * - Search form: Search topics
 * - Topics table: List of topics with author, replies, views, last activity
 * - Topic badges: Pinned, locked, announcement indicators
 * - Create topic button: If forum is not locked
 * - Pagination: Paginated results
 *
 * Required Data Variables:
 * - $forum: DiscussionForum model instance
 * - $topics: Paginated collection of DiscussionTopic models
 *
 * Dependencies:
 * - Routes: student.discussions.index, student.discussions.create-topic, student.discussions.topic
 * - Models: DiscussionForum, DiscussionTopic, User, CourseOffering, Course
 * - Helpers: __(), route(), request(), number_format()
 */
?>
<x-layouts::app :title="$forum->title">

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('student.discussions.index') }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $forum->title }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $forum->courseOffering?->course?->name }}</span>
                </div>
            </div>
        </div>
        @if(!$forum->is_locked)
            <a href="{{ route('student.discussions.create-topic', $forum) }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('lms.new_topic') }}
            </a>
        @endif
    </div>

    @if($forum->description)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $forum->description }}</p>
        </div>
    @endif

    @if($forum->is_locked)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span class="text-yellow-800 dark:text-yellow-200 font-medium">{{ __('lms.forum_is_locked') }}</span>
            </div>
        </div>
    @endif

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="flex gap-4 items-center">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('lms.search_topics') }}"
                    class="pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <x-button.submit loading-text="Searching..." variant="secondary">
                {{ __('lms.search') }}
            </x-button.submit>
        </form>
    </div>

    <!-- Topics List -->
    @if($topics->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.topic') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.author') }}</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">{{ __('lms.replies') }}</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider w-24">{{ __('lms.views') }}</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.last_activity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($topics as $topic)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition duration-150" onclick="window.location='{{ route('student.discussions.topic', $topic) }}'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1.5">
                                            @if($topic->is_pinned)
                                                <span class="p-1 rounded-full bg-yellow-100 dark:bg-yellow-900/50">
                                                    <svg class="w-3.5 h-3.5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v3h2a1 1 0 110 2h-2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5H1a1 1 0 110-2h2V5z"/>
                                                    </svg>
                                                </span>
                                            @endif
                                            @if($topic->is_locked)
                                                <span class="p-1 rounded-full bg-red-100 dark:bg-red-900/50">
                                                    <svg class="w-3.5 h-3.5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            @endif
                                            @if($topic->is_announcement)
                                                <span class="p-1 rounded-full bg-purple-100 dark:bg-purple-900/50">
                                                    <svg class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                        <span class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">{{ $topic->title }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                            <span class="text-white text-xs font-medium">{{ substr($topic->user?->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $topic->user?->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ $topic->reply_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format($topic->views_count) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-gray-300">
                                        @if($topic->last_reply_at)
                                            <div>{{ $topic->last_reply_at->diffForHumans() }}</div>
                                            @if($topic->lastReplyUser)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">by {{ $topic->lastReplyUser?->name ?? 'Unknown' }}</div>
                                            @endif
                                        @else
                                            <div>{{ $topic->created_at->diffForHumans() }}</div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $topics->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('lms.no_topics_found') }}</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">{{ __('lms.no_topics_description') }}</p>
            @if(!$forum->is_locked)
                <a href="{{ route('student.discussions.create-topic', $forum) }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ __('lms.create_first_topic') }}
                </a>
            @endif
        </div>
    @endif
</div>
</x-layouts::app>
