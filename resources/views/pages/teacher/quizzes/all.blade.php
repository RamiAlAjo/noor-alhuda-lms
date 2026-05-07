<?php

?>

<x-layouts::app :title="__('All Quizzes')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('All Quizzes') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('View and manage quizzes from all your course offerings.') }}</p>
    </div>

    @if($quizzes->isEmpty())
        <div class="text-center py-12 rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No quizzes found') }}</h3>
            <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('You haven\'t created any quizzes yet.') }}</p>
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
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-start text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Created') }}</th>
                            <th class="px-4 py-3 text-end text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quizzes as $quiz)
                            <tr class="border-b border-neutral-100 dark:border-neutral-800 hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $quiz->title }}</span>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $quiz->section?->course?->name ?? __('Unknown Course') }}
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                                    {{ $quiz->questions_count ?? 0 }}
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
                                    <a href="{{ route('teacher.quizzes.index', $quiz->section) }}" class="text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100">
                                        {{ __('View') }}
                                    </a>
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
