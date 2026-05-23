<?php

?>

<x-layouts::app :title="__('All Quizzes')">
    <div x-data="{ createQuizModalOpen: false }">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('All Quizzes') }}</h1>
                    <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View and manage quizzes from all your course offerings.') }}</p>
                </div>
                <div class="flex gap-3">
                    <flux:button variant="primary" @click="createQuizModalOpen = true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('Create Quiz') }}
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Enhanced Create Quiz Modal -->
        <div x-show="createQuizModalOpen" 
             x-cloak
             class="fixed inset-0 z-[60] overflow-y-auto"
             @keydown.escape.window="createQuizModalOpen = false">
            
            <!-- Backdrop with transition -->
            <div x-show="createQuizModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-neutral-900/60 backdrop-blur-sm"
                 @click="createQuizModalOpen = false">
            </div>
            
            <!-- Modal Dialog -->
            <div class="flex min-h-screen items-center justify-center p-4">
                <div x-show="createQuizModalOpen"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative z-[70] w-full max-w-lg rounded-2xl bg-white shadow-2xl dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 overflow-hidden"
                     @click.away="createQuizModalOpen = false">
                    
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-neutral-900 dark:text-white">
                                    {{ __('Create New Quiz') }}
                                </h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                                    {{ __('Select a course offering') }}
                                </p>
                            </div>
                        </div>
                        
                        <button @click="createQuizModalOpen = false"
                                class="rounded-lg p-2 text-neutral-400 hover:bg-neutral-200 hover:text-neutral-600 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6h12v12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Offerings List -->
                    <div class="max-h-[380px] overflow-y-auto p-2 space-y-1.5 bg-white dark:bg-neutral-800">
                        @forelse($offerings as $offering)
                            <a href="{{ route('teacher.quizzes.create', $offering) }}"
                               class="group flex items-center justify-between gap-4 rounded-xl border border-neutral-200 px-5 py-4 transition-all hover:border-[var(--color-accent)] hover:shadow-sm dark:border-neutral-700 dark:hover:border-[var(--color-accent)] dark:hover:bg-neutral-900">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-neutral-900 group-hover:text-[var(--color-accent)] dark:text-white dark:group-hover:text-[var(--color-accent)] transition-colors">
                                        {{ $offering->course->name ?? __('Unknown Course') }}
                                    </div>
                                    <div class="mt-0.5 flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                                        <span>{{ $offering->section_name }}</span>
                                        <span class="text-neutral-300 dark:text-neutral-600">•</span>
                                        <span>{{ $offering->semester->name ?? '' }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 text-right">
                                    <div class="flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 01-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ ($offering->enrollments_count ?? $offering->enrollments->count() ?? 0) }}</span>
                                    </div>
                                    
                                    <div class="text-[var(--color-accent)] opacity-0 group-hover:opacity-100 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7m-4 0l7-7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <p class="font-medium text-neutral-700 dark:text-neutral-300">{{ __('No active course offerings') }}</p>
                                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Please contact your administrator to assign offerings.') }}</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 border-t border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900">
                        <button type="button"
                                @click="createQuizModalOpen = false"
                                class="rounded-xl border border-neutral-300 px-5 py-2.5 text-sm font-medium text-neutral-700 transition hover:bg-white dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800">
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
            <flux:button type="button" @click="createQuizModalOpen = true" variant="primary" class="mt-4">
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
    </div>
</x-layouts::app>
