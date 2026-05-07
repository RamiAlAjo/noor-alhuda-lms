<?php
/**
 * Teacher Discussions - Forum Topics Page
 *
 * Purpose: View and manage topics within a discussion forum
 * Route: teacher.discussions.forum (GET)
 * Controller: App\Http\Controllers\Teacher\DiscussionController@forum
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Back navigation: Return to forums list
 * - Forum header: Title, description
 * - Locked notice: Warning if forum is locked
 * - Topics table: List with author, replies, last activity, actions
 * - Create topic button: If forum is not locked
 * - Delete forum action: Delete entire forum
 * - Delete topic action: Delete individual topics
 *
 * Required Data Variables:
 * - $forum: DiscussionForum model instance
 * - $topics: Collection of DiscussionTopic models
 *
 * Dependencies:
 * - Routes: teacher.discussions.index, teacher.discussions.create-topic, teacher.discussions.topic, teacher.discussions.delete-topic, teacher.discussions.delete-forum
 * - Models: DiscussionForum, DiscussionTopic, User
 * - Helpers: __(), route()
 */
?>
<x-layouts::app :title="$forum->title">

<div class="p-6 space-y-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('teacher.discussions.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $forum->title }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ $forum->description }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if(!$forum->is_locked)
                <a href="{{ route('teacher.discussions.create-topic', $forum) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <flux:icon name="plus" class="w-5 h-5 mr-2" />
                    {{ __('lms.new_topic') }}
                </a>
            @else
                <span class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg">
                    <flux:icon name="lock-closed" class="w-4 h-4 mr-2" />
                    {{ __('lms.forum_locked') }}
                </span>
            @endif
        </div>
    </div>

    @if($forum->is_locked)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
            <div class="flex items-center gap-2 text-yellow-800 dark:text-yellow-200">
                <flux:icon name="exclamation-triangle" class="w-5 h-5" />
                <span>{{ __('lms.forum_is_locked_notice') }}</span>
            </div>
        </div>
    @endif

    @if($topics->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <flux:icon name="chat-bubble-left-right" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.no_topics_yet') }}</p>
            @if(!$forum->is_locked)
                <a href="{{ route('teacher.discussions.create-topic', $forum) }}" class="inline-block mt-4 text-blue-600 hover:text-blue-700">
                    {{ __('lms.create_first_topic') }}
                </a>
            @endif
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('lms.topic') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('lms.author') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('lms.replies') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('lms.last_activity') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('lms.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($topics as $topic)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <a href="{{ route('teacher.discussions.topic', $topic) }}" class="text-lg font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $topic->title }}
                                </a>
                                @if($topic->is_pinned)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ __('lms.pinned') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                {{ $topic->user?->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                {{ $topic->replies->count() }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                {{ $topic->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('teacher.discussions.delete-topic', $topic) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                        onclick="return confirm('{{ __('lms.confirm_delete_topic') }}')">
                                        <flux:icon name="trash" class="w-5 h-5" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="flex justify-end">
        <form action="{{ route('teacher.discussions.delete-forum', $forum) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                onclick="return confirm('{{ __('lms.confirm_delete_forum') }}')">
                <flux:icon name="trash" class="w-5 h-5" />
                {{ __('lms.delete_forum') }}
            </button>
        </form>
    </div>
</x-layouts::app>
