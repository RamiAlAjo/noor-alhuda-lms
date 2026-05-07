<?php
/**
 * Admin Activity Logs - Show Page
 *
 * Purpose: Display detailed view of a single activity log entry
 * Route: admin.activity-logs.show (GET)
 * Controller: App\Http\Controllers\Admin\ActivityLogController@show
 *
 * Components:
 * - x-app-layout: Main application layout
 * - Detail definition list: Full log details including user, action, entity, IP, user agent
 *
 * Required Data Variables:
 * - $activityLog: ActivityLog model instance
 *
 * Dependencies:
 * - Routes: admin.activity-logs.index, admin.users.show
 * - Models: ActivityLog, User
 * - Helpers: __(), route()
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.activity_log_details') }}</x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_logs') }}
                </a>
            </div>

            <!-- Activity Log Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                        {{ __('lms.activity_log_details') }}
                    </h2>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.id') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->id }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.user') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($activityLog->user)
                                    <a href="{{ route('admin.users.show', $activityLog->user) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ $activityLog->user->name }}
                                    </a>
                                @else
                                    {{ __('lms.system') }}
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.action') }}</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @switch($activityLog->action)
                                        @case('create') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                        @case('update') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @break
                                        @case('delete') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                        @case('login') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @break
                                        @case('logout') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @break
                                        @default bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @endswitch
                                ">
                                    {{ $activityLog->action }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.entity_type') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->entity_type ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.entity_id') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->entity_id ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.description') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $activityLog->description }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.ip_address') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->ip_address ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.user_agent') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono text-xs break-all">{{ $activityLog->user_agent ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.created_at') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $activityLog->created_at->format('M d, Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
