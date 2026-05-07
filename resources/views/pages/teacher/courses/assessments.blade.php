<x-layouts::app :title="__('Assessments')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Assessments') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $section->course?->name ?? __('Unknown Course') }} - {{ __('Section') }} {{ $section->section_name }}</p>
        </div>
        <flux:button :href="route('teacher.courses.show', $section)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <!-- Create Assessment Form -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Create New Assessment') }}</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Create quizzes, exams, and assignments') }}</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('teacher.courses.assessments.store', $section) }}" class="p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <flux:input name="title" :label="__('Title')" placeholder="{{ __('Assessment title') }}" required />
                <flux:select name="type" :label="__('Type')" required>
                    <flux:select.option value="quiz">{{ __('Quiz') }}</flux:select.option>
                    <flux:select.option value="midterm">{{ __('Midterm Exam') }}</flux:select.option>
                    <flux:select.option value="final">{{ __('Final Exam') }}</flux:select.option>
                    <flux:select.option value="assignment">{{ __('Assignment') }}</flux:select.option>
                    <flux:select.option value="project">{{ __('Project') }}</flux:select.option>
                    <flux:select.option value="presentation">{{ __('Presentation') }}</flux:select.option>
                </flux:select>
            </div>

            <!-- Quiz Specific Fields -->
            <div class="mt-4 rounded-lg border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-800 dark:bg-indigo-900/20">
                <h3 class="mb-3 text-sm font-semibold text-indigo-900 dark:text-indigo-200">{{ __('Quiz Settings') }}</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:select name="quiz_type" :label="__('Quiz Type')">
                        <flux:select.option value="none">{{ __('No Quiz') }}</flux:select.option>
                        <flux:select.option value="quiz">{{ __('Quiz') }}</flux:select.option>
                        <flux:select.option value="pre_quiz">{{ __('Pre-Quiz') }}</flux:select.option>
                        <flux:select.option value="post_quiz">{{ __('Post-Quiz') }}</flux:select.option>
                    </flux:select>
                    <flux:input name="time_limit_minutes" type="number" :label="__('Time Limit (minutes)')" placeholder="0 = {{ __('No limit') }}" min="0" />
                </div>
                <div class="mt-3 grid gap-4 md:grid-cols-3">
                    <flux:input name="attempts_allowed" type="number" :label="__('Attempts Allowed')" placeholder="{{ __('Unlimited') }}" min="1" />
                    <flux:input name="passing_score" type="number" :label="__('Passing Score (%)')" placeholder="0-100" min="0" max="100" />
                    <flux:input name="available_from" type="datetime-local" :label="__('Available From')" />
                </div>
                <div class="mt-3 grid gap-4 md:grid-cols-3">
                    <flux:input name="available_until" type="datetime-local" :label="__('Available Until')" />
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="shuffle_questions" id="shuffle_questions" value="1" class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="shuffle_questions" class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Shuffle Questions') }}</label>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="shuffle_options" id="shuffle_options" value="1" class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="shuffle_options" class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('Shuffle Options') }}</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <flux:input name="max_score" type="number" :label="__('Max Score')" placeholder="100" required />
                <flux:input name="weight" type="number" :label="__('Weight (%)')" placeholder="10" required />
                <flux:input name="due_date" type="date" :label="__('Due Date')" />
            </div>
            <div class="mt-4">
                <flux:textarea name="description" :label="__('Description')" placeholder="{{ __('Optional description') }}" rows="3" />
            </div>
            <div class="mt-6">
                <flux:button type="submit" variant="primary">
                    {{ __('Create Assessment') }}
                </flux:button>
            </div>
        </form>
    </div>

    <!-- Assessments List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Created Assessments') }}</h2>
        </div>
        <div class="p-6">
            @if($section->assessments->isEmpty())
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-neutral-500 dark:text-neutral-400">{{ __('No assessments created yet') }}</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($section->assessments as $assessment)
                        <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-neutral-100">{{ $assessment->title }}</p>
                                    <div class="mt-1 flex flex-wrap items-center gap-2">
                                        <span class="text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ $assessment->assessmentType?->name ?? $assessment->type }} - {{ $assessment->max_grade }} points
                                        </span>
                                        @if($assessment->quiz_type && $assessment->quiz_type !== 'none')
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                @if($assessment->quiz_type === 'quiz') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                                @elseif($assessment->quiz_type === 'pre_quiz') bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200
                                                @elseif($assessment->quiz_type === 'post_quiz') bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200
                                                @endif">
                                                @if($assessment->quiz_type === 'quiz')
                                                    {{ __('Quiz') }}
                                                @elseif($assessment->quiz_type === 'pre_quiz')
                                                    {{ __('Pre-Quiz') }}
                                                @else
                                                    {{ __('Post-Quiz') }}
                                                @endif
                                            </span>
                                        @endif
                                        @if($assessment->time_limit_minutes && $assessment->time_limit_minutes > 0)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $assessment->time_limit_minutes }} {{ __('min') }}
                                            </span>
                                        @endif
                                        @if($assessment->attempts_allowed)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $assessment->attempts_allowed }} {{ __('attempts') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                {{ __('Unlimited attempts') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(!empty($assessment->quiz_type) && $assessment->quiz_type !== 'none')
                                    <flux:button size="sm" :href="route('teacher.courses.assessments.questions', [$section, $assessment])" variant="primary">
                                        {{ __('Manage Questions') }}
                                    </flux:button>
                                    <flux:button size="sm" :href="route('teacher.courses.assessments.preview', [$section, $assessment])" variant="secondary">
                                        {{ __('Preview') }}
                                    </flux:button>
                                @else
                                    <flux:button size="sm" variant="ghost">
                                        {{ __('Edit') }}
                                    </flux:button>
                                @endif
                                <flux:button size="sm" :href="route('teacher.courses.grades.view', [$section, $assessment])" variant="subtle">
                                    {{ __('View Grades') }}
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
