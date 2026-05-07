<?php
/**
 * Teacher Discussions - Index Page (Forums List)
 *
 * Purpose: Manage discussion forums for teacher's courses
 * Route: teacher.discussions.index (GET)
 * Controller: App\Http\Controllers\Teacher\DiscussionController@index
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Create forum button: Link to create new forum
 * - Forums list: Card-based display with title, description, course, topic count
 * - Locked indicator: Show if forum is locked
 * - Empty state: If no forums exist
 *
 * Required Data Variables:
 * - $forums: Collection of DiscussionForum models for teacher's courses
 *
 * Dependencies:
 * - Routes: teacher.discussions.create-forum, teacher.discussions.forum
 * - Models: DiscussionForum, CourseOffering, Course
 * - Helpers: __(), route()
 */
?>
<x-layouts::app :title="__('Discussion Forums')">

<div class="p-6 space-y-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.discussion_forums') }}</h1>
        <a href="{{ route('teacher.discussions.create-forum') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <flux:icon name="plus" class="w-5 h-5 mr-2" />
            {{ __('lms.create_forum') }}
        </a>
    </div>

    @if($forums->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <flux:icon name="chat-bubble-left-right" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.no_forums_yet') }}</p>
            <a href="{{ route('teacher.discussions.create-forum') }}" class="inline-block mt-4 text-blue-600 hover:text-blue-700">
                {{ __('lms.create_first_forum') }}
            </a>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($forums as $forum)
                <a href="{{ route('teacher.discussions.forum', $forum) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $forum->title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">
                                {{ $forum->description }}
                            </p>
                            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <flux:icon name="academic-cap" class="w-4 h-4" />
                                    {{ $forum->courseOffering?->course?->name ?? 'N/A' }}
                                </span>
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
    @endif
</div>
</x-layouts::app>
