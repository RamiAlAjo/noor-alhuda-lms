<?php
/**
 * Page: Teacher Quiz List
 *
 * Purpose: Display a list of quizzes for a specific course offering.
 * Allows teachers to view, create, edit, publish, and manage quizzes.
 *
 * Route: teacher.quizzes.index (GET)
 *
 * Controller: App\Http\Controllers\Teacher\QuizController@index
 *
 * Components on this page:
 * - x-app-layout: Main application layout wrapper
 * - Breadcrumb navigation
 * - Header with course info and Create Quiz button
 * - Quiz cards showing title, description, question count, time limit, attempts
 * - Dropdown menu for each quiz: Manage Questions, Analytics, Preview, Edit, Publish/Unpublish, Duplicate, Delete
 * - Empty state when no quizzes exist
 *
 * Required Data variables:
 * - $offering: CourseOffering model instance
 * - $quizzes: Collection of Quiz objects
 *
 * Dependencies:
 * - Routes: teacher.courses.index, teacher.courses.show, teacher.quizzes.create, teacher.quizzes.questions, teacher.quizzes.analytics, teacher.quizzes.preview, teacher.quizzes.edit, teacher.quizzes.toggle-publish, teacher.quizzes.duplicate, teacher.quizzes.destroy
 * - Helpers: __(), route()
 * - Relationships: CourseOffering->course, Quiz->questions_count, Quiz->completed_attempts_count
 * - Flux UI components: flux:button, flux:badge, flux:dropdown, flux:menu
 *
 * @package App\Views\Pages\Teacher\Quizzes
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('Quizzes') }} - {{ $offering->course?->name ?? __('Course') }}</x-slot>

    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('Courses') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.courses.show', $offering) }}" class="hover:text-white">{{ $offering->course?->name ?? __('Course') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Quizzes') }}</span>
        </nav>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Quizzes') }}</h1>
            <p class="text-neutral-600 dark:text-neutral-400">{{ $offering->course?->name ?? __('Course') }} - {{ $offering->semester?->name ?? '' }}</p>
        </div>
        <flux:button href="{{ route('teacher.quizzes.create', $offering) }}" variant="primary">
            <flux:icon name="plus" class="mr-2" />
            {{ __('Create Quiz') }}
        </flux:button>
    </div>

    @if($quizzes->count() > 0)
        <div class="grid gap-4">
            @foreach($quizzes as $quiz)
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">{{ $quiz->title }}</h3>
                                @if($quiz->is_published)
                                    <flux:badge color="green">{{ __('Published') }}</flux:badge>
                                @else
                                    <flux:badge color="yellow">{{ __('Draft') }}</flux:badge>
                                @endif
                                @if($quiz->quiz_type === 'quiz')
                                    <flux:badge color="emerald">{{ __('Quiz') }}</flux:badge>
                                @elseif($quiz->quiz_type === 'pre_quiz')
                                    <flux:badge color="blue">{{ __('Pre-Quiz') }}</flux:badge>
                                @elseif($quiz->quiz_type === 'post_quiz')
                                    <flux:badge color="purple">{{ __('Post-Quiz') }}</flux:badge>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">{{ $quiz->description }}</p>
                            <div class="mt-3 flex flex-wrap gap-4 text-sm text-neutral-500 dark:text-neutral-400">
                                <span class="flex items-center gap-1">
                                    <flux:icon name="question-mark-circle" />
                                    {{ $quiz->questions_count }} {{ __('questions') }}
                                </span>
                                @if($quiz->time_limit_minutes)
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="clock" />
                                        {{ $quiz->time_limit_minutes }} {{ __('minutes') }}
                                    </span>
                                @endif
                                @if($quiz->attempts_allowed)
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="arrow-path" />
                                        {{ $quiz->attempts_allowed }} {{ __('attempts allowed') }}
                                    </span>
                                @endif
                                @if($quiz->passing_score)
                                    <span class="flex items-center gap-1">
                                        <flux:icon name="check-circle" />
                                        {{ $quiz->passing_score }}% {{ __('passing score') }}
                                    </span>
                                @endif
                                <span class="flex items-center gap-1">
                                    <flux:icon name="users" />
                                    {{ $quiz->completed_attempts_count }} {{ __('submissions') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:dropdown>
                                <flux:button variant="ghost" icon="ellipsis-horizontal" />
                                <flux:menu>
                                    <flux:menu.item href="{{ route('teacher.quizzes.questions', [$offering, $quiz]) }}">
                                        <flux:icon name="puzzle-piece" class="mr-2" />
                                        {{ __('Manage Questions') }}
                                    </flux:menu.item>
                                    <flux:menu.item href="{{ route('teacher.quizzes.analytics', [$offering, $quiz]) }}">
                                        <flux:icon name="chart-bar" class="mr-2" />
                                        {{ __('View Analytics') }}
                                    </flux:menu.item>
                                    <flux:menu.item href="{{ route('teacher.quizzes.preview', [$offering, $quiz]) }}">
                                        <flux:icon name="eye" class="mr-2" />
                                        {{ __('Preview') }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item href="{{ route('teacher.quizzes.edit', [$offering, $quiz]) }}">
                                        <flux:icon name="pencil" class="mr-2" />
                                        {{ __('Edit') }}
                                    </flux:menu.item>
                                    <flux:menu.item wire:click="togglePublish({{ $quiz->id }})" href="{{ route('teacher.quizzes.toggle-publish', [$offering, $quiz]) }}">
                                        @if($quiz->is_published)
                                            <flux:icon name="eye-slash" class="mr-2" />
                                            {{ __('Unpublish') }}
                                        @else
                                            <flux:icon name="eye" class="mr-2" />
                                            {{ __('Publish') }}
                                        @endif
                                    </flux:menu.item>
                                    <flux:menu.item href="{{ route('teacher.quizzes.duplicate', [$offering, $quiz]) }}">
                                        <flux:icon name="document-duplicate" class="mr-2" />
                                        {{ __('Duplicate') }}
                                    </flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item color="danger" href="{{ route('teacher.quizzes.destroy', [$offering, $quiz]) }}" wire:click="deleteQuiz({{ $quiz->id }})">
                                        <flux:icon name="trash" class="mr-2" />
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white p-12 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <flux:icon name="clipboard-document-list" class="mx-auto h-12 w-12 text-neutral-400" />
            <h3 class="mt-4 text-lg font-medium text-neutral-900 dark:text-white">{{ __('No quizzes yet') }}</h3>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">{{ __('Create your first quiz to assess your students.') }}</p>
            <flux:button href="{{ route('teacher.quizzes.create', $offering) }}" variant="primary" class="mt-4">
                {{ __('Create Quiz') }}
            </flux:button>
        </div>
    @endif
</x-app-layout>
