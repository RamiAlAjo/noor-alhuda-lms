<?php

?>

<x-layouts::app :title="__('All Quizzes')">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('All Quizzes') }}</h1>
                <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View and manage quizzes from all your course offerings.') }}</p>
            </div>
            <div class="flex gap-3">
                <flux:button variant="primary" x-data="{ open: false }" @click="open = true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('Create Quiz') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Create Quiz Modal -->
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: false }">
        <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>
            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all dark:bg-neutral-800 sm:my-8 sm:w-full sm:max-w-md sm:align-middle">
                <div class="bg-white px-4 pt-5 pb-4 dark:bg-neutral-800 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-neutral-100">{{ __('Select Course Offering') }}</h3>
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-neutral-400 mb-4">{{ __('Choose which course offering this quiz will belong to:') }}</p>
                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            @forelse($offerings as $offering)
                                <a href="{{ route('teacher.quizzes.create', $offering) }}"
                                   class="block p-3 rounded-lg border border-neutral-200 hover:border-blue-300 hover:bg-blue-50 dark:border-neutral-600 dark:hover:border-blue-500 dark:hover:bg-blue-900/20 transition-colors">
                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ $offering->course->name }}</div>
                                    <div class="text-sm text-neutral-600 dark:text-neutral-400">{{ $offering->section_name }} - {{ $offering->semester->localized_name ?? $offering->semester->name }}</div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-500 mt-1">{{ $offering->enrollments->count() }} {{ __('students') }}</div>
                                </a>
                            @empty
                                <div class="text-center py-4 text-neutral-500 dark:text-neutral-400">
                                    {{ __('No course offerings found. Please contact administrator.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 dark:bg-neutral-700 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 sm:mt-0 sm:w-auto sm:text-sm" @click="open = false">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quiz Statistics -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Quizzes') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $quizzes->count() }}</p>
                    @if($quizzes->count() > 0)
                        <p class="text-xs text-blue-600 dark:text-blue-400">{{ $quizzes->where('is_published', true)->count() }} {{ __('published') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Attempts') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $quizzes->sum('analytics.total_attempts') }}</p>
                    @if($quizzes->count() > 0)
                        <p class="text-xs text-green-600 dark:text-green-400">{{ number_format($quizzes->avg('analytics.completion_rate'), 1) }}% {{ __('avg completion') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Average Score') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        @if($quizzes->count() > 0 && $quizzes->avg('analytics.avg_score'))
                            {{ number_format($quizzes->avg('analytics.avg_score'), 1) }}%
                        @else
                            -
                        @endif
                    </p>
                    @if($quizzes->count() > 0 && $quizzes->max('analytics.highest_score'))
                        <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('Highest') }}: {{ number_format($quizzes->max('analytics.highest_score'), 1) }}%</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900 dark:text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Recent Activity') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                        @php
                            $recentQuizzes = $quizzes->where('created_at', '>=', now()->subDays(7))->count();
                        @endphp
                        {{ $recentQuizzes }}
                    </p>
                    <p class="text-xs text-orange-600 dark:text-orange-400">{{ __('last 7 days') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($quizzes->isEmpty())
        <div class="text-center py-12 rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No quizzes found') }}</h3>
            <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('You haven\'t created any quizzes yet.') }}</p>
            <flux:button href="#" x-data="{ open: true }" @click="open = true" variant="primary" class="mt-4">
                {{ __('Create Your First Quiz') }}
            </flux:button>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 dark:border-neutral-700">
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Quiz Title') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Course') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Questions') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Attempts') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Avg Score') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Created') }}</th>
                            <th class="px-4 py-3 text-end text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quizzes as $quiz)
                            <tr class="border-b border-neutral-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-4 py-3">
                                    <div>
                                        <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $quiz->title }}</span>
                                        @if($quiz->quiz_type !== 'quiz')
                                            <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                                                {{ ucfirst($quiz->quiz_type) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    <div>{{ $quiz->section?->course?->name ?? __('Unknown Course') }}</div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-500">{{ $quiz->section?->section_name ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $quiz->questions_count ?? 0 }}
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $quiz->analytics['total_attempts'] ?? 0 }}
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    @if(isset($quiz->analytics['avg_score']) && $quiz->analytics['avg_score'])
                                        {{ number_format($quiz->analytics['avg_score'], 1) }}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($quiz->is_published)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('Published') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                            {{ __('Draft') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $quiz->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('teacher.quizzes.index', $quiz->section) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                            {{ __('View') }}
                                        </a>
                                        <a href="{{ route('teacher.quizzes.analytics', [$quiz->section, $quiz]) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 text-sm">
                                            {{ __('Analytics') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 px-4 pb-4">
                {{ $quizzes->links() }}
            </div>
        </div>
    @endif
</x-layouts::app>
