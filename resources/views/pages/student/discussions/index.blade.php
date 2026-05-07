<?php
/**
 * Student Discussions - Index Page (Forums List)
 *
 * Purpose: Browse available discussion forums across all enrolled courses
 * Route: student.discussions.index (GET)
 * Controller: App\Http\Controllers\Student\DiscussionController@index
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Search form: Search forums by title
 * - Forums grid: Card-based display of discussion forums
 * - Forum badges: Pinned, locked status indicators
 * - Stats: Topic count, reply count per forum
 * - Pagination: Paginated results
 *
 * Required Data Variables:
 * - $forums: Paginated collection of DiscussionForum models
 *
 * Dependencies:
 * - Routes: student.discussions.forum
 * - Models: DiscussionForum, CourseOffering, Course
 * - Helpers: __(), route(), request()
 */
?>
<x-layouts::app :title="__('lms.discussion_forums')">

<div class="mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.discussion_forums') }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ __('lms.discussion_forums_description') }}</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" class="flex gap-4 items-center">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('lms.search_forums') }}"
                    class="pl-10 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200">
                {{ __('lms.search') }}
            </button>
        </form>
    </div>

    <!-- Forums List -->
    @if($forums->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($forums as $forum)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition duration-300 overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            @if($forum->is_pinned)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v3h2a1 1 0 110 2h-2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5H1a1 1 0 110-2h2V5z"/>
                                    </svg>
                                    {{ __('lms.pinned') }}
                                </span>
                            @endif
                            @if($forum->is_locked)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ __('lms.locked') }}
                                </span>
                            @endif
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                            <a href="{{ route('student.discussions.forum', $forum) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition duration-200">
                                {{ $forum->title }}
                            </a>
                        </h3>

                        @if($forum->description)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                                {{ $forum->description }}
                            </p>
                        @endif

                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300 mb-4">
                            <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $forum->courseOffering?->course?->name ?? __('lms.no_course') }}</span>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $forum->topic_count }}</span>
                                        <span class="ml-1 text-gray-600 dark:text-gray-300">{{ __('lms.topics') }}</span>
                                    </span>
                                    <span class="flex items-center text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $forum->reply_count }}</span>
                                        <span class="ml-1 text-gray-600 dark:text-gray-300">{{ __('lms.replies') }}</span>
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $forum->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $forums->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('lms.no_forums_found') }}</h3>
            <p class="text-gray-600 dark:text-gray-300 max-w-md mx-auto">{{ __('lms.no_forums_student_description') }}</p>
        </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
</x-layouts::app>

