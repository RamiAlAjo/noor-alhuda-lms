<?php
/**
 * Page: Create Quiz
 *
 * Purpose: Display a form for creating a new quiz for a course offering.
 * Allows teachers to set quiz details, time limits, attempts, availability, and publish options.
 *
 * Route: teacher.quizzes.create (GET)
 *
 * Controller: App\Http\Controllers\Teacher\QuizController@create
 *
 * Components on this page:
 * - x-app-layout: Main application layout wrapper
 * - Breadcrumb navigation
 * - Form with sections: Basic Information, Time & Attempts, Availability
 * - Sidebar with Publish Options, Shuffle Options, and Action buttons
 * - Form inputs: title, title_ar, description, quiz_type, max_grade, weight, time_limit_minutes, time_limit_seconds, attempts_allowed, passing_score, available_from, available_until, is_published, show_results_immediately, show_correct_answers, show_feedback, shuffle_questions, shuffle_options
 *
 * Required Data variables:
 * - $offering: CourseOffering model instance
 *
 * Dependencies:
 * - Routes: teacher.courses.index, teacher.courses.show, teacher.quizzes.index, teacher.quizzes.store
 * - Helpers: __(), route()
 * - Relationships: CourseOffering->course
 * - Flux UI components: flux:field, flux:label, flux:input, flux:textarea, flux:select, flux:checkbox, flux:button
 *
 * @package App\Views\Pages\Teacher\Quizzes
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('Create Quiz') }} - {{ $offering->course?->name ?? __('Course') }}</x-slot>

    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('Courses') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.courses.show', $offering) }}" class="hover:text-white">{{ $offering->course?->name ?? __('Course') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.quizzes.index', $offering) }}" class="hover:text-white">{{ __('Quizzes') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Create') }}</span>
        </nav>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Create Quiz') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400">{{ $offering->course?->name ?? __('Course') }}</p>
    </div>

    <form method="POST" action="{{ route('teacher.quizzes.store', $offering) }}" class="space-y-6">
        @csrf

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Settings -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Basic Information') }}</h2>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>{{ __('Title') }}</flux:label>
                            <flux:input type="text" name="title" required />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Title (Arabic)') }}</flux:label>
                            <flux:input type="text" name="title_ar" dir="rtl" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Description') }}</flux:label>
                            <flux:textarea name="description" rows="3" />
                        </flux:field>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Quiz Type') }}</flux:label>
                                <flux:select name="quiz_type" required>
                                    <flux:select.option value="quiz">{{ __('Quiz') }}</flux:select.option>
                                    <flux:select.option value="pre_quiz">{{ __('Pre-Quiz') }}</flux:select.option>
                                    <flux:select.option value="post_quiz">{{ __('Post-Quiz') }}</flux:select.option>
                                </flux:select>
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Maximum Score') }}</flux:label>
                                <flux:input type="number" name="max_grade" value="100" min="1" required />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>{{ __('Weight in Final Grade (%)') }}</flux:label>
                            <flux:input type="number" name="weight" value="0" min="0" max="100" />
                        </flux:field>
                    </div>
                </div>

                <!-- Time & Attempts -->
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Time & Attempts') }}</h2>

                    <div class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Time Limit (minutes)') }}</flux:label>
                                <flux:input type="number" name="time_limit_minutes" min="1" max="300" placeholder="{{ __('No limit') }}" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Additional Seconds') }}</flux:label>
                                <flux:input type="number" name="time_limit_seconds" min="0" max="59" value="0" />
                            </flux:field>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Attempts Allowed') }}</flux:label>
                                <flux:input type="number" name="attempts_allowed" min="1" placeholder="{{ __('Unlimited') }}" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Passing Score (%)') }}</flux:label>
                                <flux:input type="number" name="passing_score" min="0" max="100" step="0.01" placeholder="{{ __('No passing score') }}" />
                            </flux:field>
                        </div>
                    </div>
                </div>

                <!-- Availability -->
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Availability') }}</h2>

                    <div class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Available From') }}</flux:label>
                                <flux:input type="datetime-local" name="available_from" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Available Until') }}</flux:label>
                                <flux:input type="datetime-local" name="available_until" />
                            </flux:field>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="space-y-6">
                <!-- Publish Options -->
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Publish Options') }}</h2>

                    <div class="space-y-4">
                        <flux:checkbox name="is_published" label="{{ __('Publish immediately') }}" />

                        <div class="border-t border-neutral-200 pt-4 dark:border-neutral-700">
                            <flux:checkbox name="show_results_immediately" label="{{ __('Show results immediately after submission') }}" checked />
                        </div>

                        <flux:checkbox name="show_correct_answers" label="{{ __('Show correct answers in results') }}" checked />

                        <flux:checkbox name="show_feedback" label="{{ __('Show question feedback') }}" checked />
                    </div>
                </div>

                <!-- Shuffle Options -->
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Shuffle Options') }}</h2>

                    <div class="space-y-4">
                        <flux:checkbox name="shuffle_questions" label="{{ __('Shuffle questions') }}" />

                        <flux:checkbox name="shuffle_options" label="{{ __('Shuffle answer options') }}" />
                    </div>
                </div>

                <!-- Actions -->
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex flex-col gap-3">
                        <flux:button type="submit" variant="primary" class="w-full">
                            <x-button.submit loading-text="Creating..." variant="primary">
                                Create Quiz
                            </x-button.submit>
                        </flux:button>
                        <flux:button href="{{ route('teacher.quizzes.index', $offering) }}" variant="ghost" class="w-full">
                            {{ __('Cancel') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
