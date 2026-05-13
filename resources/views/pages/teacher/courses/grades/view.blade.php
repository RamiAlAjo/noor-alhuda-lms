<x-layouts::app :title="__('View Grade')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 rounded-xl p-3">
                        <flux:icon.clipboard-document-list class="w-8 h-8" />
                    </div>
                    <div>
                        <nav class="flex text-sm text-gray-300">
                            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('teacher.courses.index') }}</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('teacher.courses.show', $section) }}" class="hover:text-white">{{ $section->course?->name . ' - ' . $section->section_name }}</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('teacher.courses.grades', $section) }}" class="hover:text-white">{{ __('Grades') }}</a>
                            <span class="mx-2">/</span>
                            <span class="text-white">{{ $assessment->title }}</span>
                        </nav>
                        <h1 class="mt-1 text-2xl font-bold text-white">
                            {{ $assessment->title }} - {{ __('Quiz Results') }}
                        </h1>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($ungradedEnrollments->count() > 0)
                    <flux:button :href="route('teacher.courses.assessments.bulk-grade', [$section, $assessment])" variant="primary" class="!bg-green-600 hover:!bg-green-700">
                        {{ __('Bulk Grade') }} ({{ $ungradedEnrollments->count() }})
                    </flux:button>
                    @endif
                    <flux:button :href="route('teacher.courses.grades', $section)" variant="ghost" class="!text-white hover:!bg-white/20">
                        {{ __('Back to Grades') }}
                        <flux:icon.arrow-left class="w-4 h-4 ml-2" />
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Quiz Info -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-indigo-10 p-2">
                        <flux:icon.clipboard-document-list class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Questions') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $assessment->questions->count() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-green-10 p-2">
                        <flux:icon.chart-bar class="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Points') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $assessment->max_grade }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-amber-10 p-2">
                        <flux:icon.clock class="w-5 h-5 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Time Limit') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $assessment->time_limit_minutes > 0 ? $assessment->time_limit_minutes . ' min' : __('No limit') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-purple-10 p-2">
                        <flux:icon.user-group class="w-5 h-5 text-purple-600" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Attempts') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $assessment->studentGrades->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Grades Table -->
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800 shadow-sm overflow-hidden">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Student Results') }}</h2>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('View all student submissions for this quiz') }}</p>
            </div>

            @if($assessment->studentGrades->isEmpty())
                <div class="p-8 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                        <flux:icon.clipboard-document-list class="h-8 w-8 text-neutral-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No submissions yet') }}</h3>
                    <p class="text-neutral-500 dark:text-neutral-400">{{ __('Students haven\'t taken this quiz yet.') }}</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-neutral-50 dark:bg-neutral-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    {{ __('Student') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    {{ __('Score') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    {{ __('Percentage') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    {{ __('Status') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    {{ __('Submitted') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($assessment->studentGrades as $grade)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                                    {{ $grade->student?->initials() ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                    {{ $grade->student?->name ?? __('Unknown') }}
                                                </div>
                                                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                                    {{ $grade->student?->student_id ?? $grade->student?->user_id ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                            {{ $grade->grade }} / {{ $grade->max_grade }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-neutral-900 dark:text-neutral-100">
                                            {{ number_format($grade->percentage, 1) }}%
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if($grade->passed)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ __('Passed') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                {{ __('Failed') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-neutral-500 dark:text-neutral-400">
                                            {{ $grade->submitted_at ? $grade->submitted_at->format('M d, Y - h:i A') : '-' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <flux:button size="sm" variant="ghost" :href="route('teacher.courses.grades.view', [$section, $assessment]) . '?student=' . $grade->student_id">
                                            {{ __('View Details') }}
                                        </flux:button>
                                        @if(in_array($assessment->quiz_type, ['quiz', 'pre_quiz', 'post_quiz']))
                                            <flux:button size="sm" variant="primary" :href="route('teacher.courses.assessments.grade', [$section, $assessment, $grade])">
                                                {{ __('Grade') }}
                                            </flux:button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Question Analysis -->
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800 shadow-sm overflow-hidden">
            <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Question Analysis') }}</h2>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Overview of how students performed on each question') }}</p>
            </div>

            <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                @foreach($assessment->questions as $qIndex => $question)
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300 flex-shrink-0">
                                {{ $qIndex + 1 }}
                            </div>
                            <div class="flex-1">
                                <h3 class="text-base font-medium text-neutral-900 dark:text-neutral-100 mb-2">
                                    {!! $question->question_text !!}
                                </h3>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400 mb-3">
                                    {{ __('Points') }}: {{ $question->points }} |
                                    {{ __('Type') }}: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                </p>

                                <!-- Options -->
                                <div class="space-y-2">
                                    @foreach($question->options as $option)
                                        <div class="flex items-center gap-3 p-2 rounded-lg
                                            @if($option->is_correct) bg-green-50 dark:bg-green-900/20
                                            @else bg-neutral-50 dark:bg-neutral-900
                                            @endif">
                                            @if($option->is_correct)
                                                <flux:icon.check-circle class="w-5 h-5 text-green-600" />
                                            @else
                                                <flux:icon.x-circle class="w-5 h-5 text-neutral-400" />
                                            @endif
                                            <span class="@if($option->is_correct) font-medium text-green-700 dark:text-green-300 @else text-neutral-600 dark:text-neutral-400 @endif">
                                                {!! $option->option_text !!}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
