<?php
/**
 * Admin Activity Logs - Statistics Page
 *
 * Purpose: Display analytics and statistics for activity logs with charts and summaries
 * Route: admin.activity-logs.statistics (GET)
 * Controller: App\Http\Controllers\Admin\ActivityLogController@statistics
 *
 * Components:
 * - x-app-layout: Main application layout
 * - Date filter: Select date range for statistics
 * - Stats cards: Total activities count
 * - Charts: Activities by action, by entity type, most active users, daily activity
 *
 * Required Data Variables:
 * - $dateFrom: Start date for statistics
 * - $dateTo: End date for statistics
 * - $totalActivities: Total count of activities in period
 * - $byAction: Activities grouped by action type
 * - $byEntityType: Activities grouped by entity type
 * - $mostActiveUsers: Top users by activity count
 * - $dailyActivity: Daily activity counts
 *
 * Dependencies:
 * - Routes: admin.activity-logs.index
 * - Models: ActivityLog, User
 * - Helpers: __(), route(), number_format()
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.activity_statistics') }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_logs') }}
                </a>
            </div>

            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('lms.activity_statistics') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('lms.statistics_period') }}: {{ $dateFrom }} - {{ $dateTo }}
                </p>
            </div>

            <!-- Date Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.activity-logs.statistics') }}" class="flex gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.date_from') }}</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.date_to') }}</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            {{ __('lms.filter') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.total_activities') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalActivities) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Activities by Action -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.activities_by_action') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($byAction->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center">{{ __('lms.no_data') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach($byAction as $action)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $action->action }}</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($action->count / $totalActivities) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($action->count) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Activities by Entity Type -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.activities_by_entity') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($byEntityType->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center">{{ __('lms.no_data') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach($byEntityType as $entity)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $entity->entity_type ?? 'N/A' }}</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($entity->count / $totalActivities) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($entity->count) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Most Active Users -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.most_active_users') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($mostActiveUsers->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center">{{ __('lms.no_data') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach($mostActiveUsers as $userActivity)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $userActivity->user?->name ?? 'Unknown' }}</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($userActivity->count) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Daily Activity -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.daily_activity') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($dailyActivity->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400 text-center">{{ __('lms.no_data') }}</p>
                        @else
                            <div class="space-y-2">
                                @foreach($dailyActivity->take(7) as $day)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $day->date }}</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($day->count / $dailyActivity->max('count')) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($day->count) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
