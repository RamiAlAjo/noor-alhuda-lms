<?php
/**
 * Teacher Discussions - Create Topic Page
 *
 * Purpose: Form to create a new discussion topic within a forum
 * Route: teacher.discussions.create-topic (GET)
 * Controller: App\Http\Controllers\Teacher\DiscussionController@createTopic
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Forum info: Display which forum the topic will be created in
 * - Create form: Title, content, pin option
 *
 * Required Data Variables:
 * - $forum: DiscussionForum model instance
 *
 * Dependencies:
 * - Routes: teacher.discussions.forum, teacher.discussions.store-topic
 * - Models: DiscussionForum
 * - Helpers: __(), route(), error()
 */
?>
<x-layouts::app :title="__('Create Topic')">

<div class="p-6 space-y-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('teacher.discussions.forum', $forum) }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.create_topic') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('teacher.discussions.store-topic', $forum) }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    <span class="font-medium">{{ __('lms.posting_in') }}:</span> {{ $forum->title }}
                </p>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('lms.topic_title') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.topic_title_placeholder') }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('lms.content') }} <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="8" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.topic_content_placeholder') }}"></textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_pinned" id="is_pinned" value="1"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_pinned" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ __('lms.pin_topic') }}
                </label>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('teacher.discussions.forum', $forum) }}"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('lms.cancel') }}
                </a>
                <x-button.submit loading-text="Creating..." variant="primary">
                    {{ __('lms.create_topic') }}
                </x-button.submit>
            </div>
        </form>
    </div>
</x-layouts::app>
