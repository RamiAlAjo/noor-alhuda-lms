<x-app-layout>
    <x-slot name="title">{{ __('Edit Quiz') }} - {{ $quiz->title }}</x-slot>

    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('Courses') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.courses.show', $offering) }}" class="hover:text-white">{{ $offering->course?->name ?? __('Course') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.quizzes.index', $offering) }}" class="hover:text-white">{{ __('Quizzes') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Edit') }}</span>
        </nav>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Edit Quiz') }}</h1>
        <p class="text-neutral-600 dark:text-neutral-400">{{ $quiz->title }}</p>
    </div>

    <form method="POST" action="{{ route('teacher.quizzes.update', [$offering, $quiz]) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Settings -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Basic Information') }}</h2>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>{{ __('Title') }}</flux:label>
                            <flux:input type="text" name="title" value="{{ $quiz->title }}" required />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Title (Arabic)') }}</flux:label>
                            <flux:input type="text" name="title_ar" value="{{ $quiz->title_ar }}" dir="rtl" />
                        </flux:field>

                        <flux:field>
                            <flux:label>{{ __('Description') }}</flux:label>
                            <flux:textarea name="description" rows="3">{{ $quiz->description }}</flux:textarea>
                        </flux:field>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Quiz Type') }}</flux:label>
                                <flux:select name="quiz_type" required>
                                    <flux:select.option value="quiz" {{ $quiz->quiz_type === 'quiz' ? 'selected' : '' }}>{{ __('Quiz') }}</flux:select.option>
                                    <flux:select.option value="pre_quiz" {{ $quiz->quiz_type === 'pre_quiz' ? 'selected' : '' }}>{{ __('Pre-Quiz') }}</flux:select.option>
                                    <flux:select.option value="post_quiz" {{ $quiz->quiz_type === 'post_quiz' ? 'selected' : '' }}>{{ __('Post-Quiz') }}</flux:select.option>
                                </flux:select>
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Maximum Score') }}</flux:label>
                                <flux:input type="number" name="max_grade" value="{{ $quiz->max_grade }}" min="1" required />
                            </flux:field>
                        </div>

                        <flux:field>
                            <flux:label>{{ __('Weight in Final Grade (%)') }}</flux:label>
                            <flux:input type="number" name="weight" value="{{ $quiz->weight }}" min="0" max="100" />
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
                                <flux:input type="number" name="time_limit_minutes" value="{{ $quiz->time_limit_minutes }}" min="1" max="300" placeholder="{{ __('No limit') }}" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Additional Seconds') }}</flux:label>
                                <flux:input type="number" name="time_limit_seconds" value="{{ $quiz->time_limit_seconds }}" min="0" max="59" />
                            </flux:field>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label>{{ __('Attempts Allowed') }}</flux:label>
                                <flux:input type="number" name="attempts_allowed" value="{{ $quiz->attempts_allowed }}" min="1" placeholder="{{ __('Unlimited') }}" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Passing Score (%)') }}</flux:label>
                                <flux:input type="number" name="passing_score" value="{{ $quiz->passing_score }}" min="0" max="100" step="0.01" placeholder="{{ __('No passing score') }}" />
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
                                <flux:input type="datetime-local" name="available_from" value="{{ $quiz->available_from?->format('Y-m-d\TH:i') }}" />
                            </flux:field>

                            <flux:field>
                                <flux:label>{{ __('Available Until') }}</flux:label>
                                <flux:input type="datetime-local" name="available_until" value="{{ $quiz->available_until?->format('Y-m-d\TH:i') }}" />
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
                        <flux:checkbox name="is_published" label="{{ __('Published') }}" {{ $quiz->is_published ? 'checked' : '' }} />

                        <div class="border-t border-neutral-200 pt-4 dark:border-neutral-700">
                            <flux:checkbox name="show_results_immediately" label="{{ __('Show results immediately after submission') }}" {{ $quiz->show_results_immediately ? 'checked' : '' }} />
                        </div>

                        <flux:checkbox name="show_correct_answers" label="{{ __('Show correct answers in results') }}" {{ $quiz->show_correct_answers ? 'checked' : '' }} />

                        <flux:checkbox name="show_feedback" label="{{ __('Show question feedback') }}" {{ $quiz->show_feedback ? 'checked' : '' }} />
                    </div>
                </div>

                <!-- Shuffle Options -->
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Shuffle Options') }}</h2>

                    <div class="space-y-4">
                        <flux:checkbox name="shuffle_questions" label="{{ __('Shuffle questions') }}" {{ $quiz->shuffle_questions ? 'checked' : '' }} />

                        <flux:checkbox name="shuffle_options" label="{{ __('Shuffle answer options') }}" {{ $quiz->shuffle_options ? 'checked' : '' }} />
                    </div>
                </div>

                <!-- Actions -->
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="flex flex-col gap-3">
                        <x-button.submit loading-text="Saving..." variant="primary" class="w-full">
                            Save Changes
                        </x-button.submit>
                        <flux:button href="{{ route('teacher.quizzes.questions', [$offering, $quiz]) }}" variant="secondary" class="w-full">
                            {{ __('Manage Questions') }}
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
