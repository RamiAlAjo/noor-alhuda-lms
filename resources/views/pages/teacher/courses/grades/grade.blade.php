<x-layouts::app :title="__('Grade Quiz')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Grade Quiz') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">
                {{ $assessment->title }} - {{ $studentGrade->student->name }}
            </p>
        </div>
        <flux:button :href="route('teacher.courses.grades.view', [$section, $assessment])" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Results') }}
        </flux:button>
    </div>

    <!-- Student Info -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                <span class="text-2xl font-bold">{{ substr($studentGrade->student->name, 0, 1) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $studentGrade->student->name }}</h2>
                <p class="text-neutral-500 dark:text-neutral-400">{{ $studentGrade->student->email }}</p>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __('Submitted') }}: {{ $studentGrade->submitted_at?->format('M d, Y - h:i A') ?? __('N/A') }}
                </p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Current Score') }}</p>
                <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                    {{ $studentGrade->grade ?? 0 }} <span class="text-lg font-normal">/ {{ $studentGrade->max_grade ?? $assessment->max_grade }}</span>
                </p>
                @if($studentGrade->percentage)
                    <p class="text-sm @if($studentGrade->passed) text-green-600 @else text-red-600 @endif">
                        {{ $studentGrade->percentage }}% - 
                        @if($studentGrade->passed)
                            {{ __('Passed') }}
                        @else
                            {{ __('Failed') }}
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Grading Form -->
    <form method="POST" action="{{ route('teacher.courses.assessments.grade.store', [$section, $assessment, $studentGrade]) }}">
        @csrf
        
        <!-- Questions & Answers -->
        <div class="space-y-6">
            @foreach($assessment->questions as $qIndex => $question)
                @php
                    $studentAnswer = $question->studentAnswers->firstWhere('student_id', $studentGrade->student_id);
                    $currentPoints = $studentAnswer?->points_earned ?? 0;
                @endphp
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                                    <span class="text-sm font-semibold">{{ $qIndex + 1 }}</span>
                                </span>
                                <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">
                                    {{ $question->points }} {{ __('points') }}
                                </span>
                            </div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                @if($question->question_type === 'multiple_choice') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($question->question_type === 'true_false') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($question->question_type === 'short_answer') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @endif">
                                @if($question->question_type === 'multiple_choice')
                                    {{ __('Multiple Choice') }}
                                @elseif($question->question_type === 'true_false')
                                    {{ __('True/False') }}
                                @elseif($question->question_type === 'short_answer')
                                    {{ __('Short Answer') }}
                                @else
                                    {{ __('Essay') }}
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <!-- Question -->
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{!! $question->question_text !!}</h3>
                        </div>
                        
                        <!-- Student's Answer -->
                        <div class="mb-4 rounded-lg bg-neutral-50 p-4 dark:bg-neutral-700">
                            <p class="mb-2 text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Student\'s Answer') }}:</p>
                            @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                                @if($studentAnswer && $studentAnswer->option_id)
                                    @php
                                        $selectedOption = $question->options->firstWhere('id', $studentAnswer->option_id);
                                    @endphp
                                    <p class="text-neutral-900 dark:text-neutral-100">
                                        {{ $selectedOption?->option_text ?? __('No answer') }}
                                    </p>
                                    @if($selectedOption?->is_correct)
                                        <span class="mt-2 inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('Correct') }}
                                        </span>
                                    @else
                                        <span class="mt-2 inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                            {{ __('Incorrect') }}
                                        </span>
                                    @endif
                                @else
                                    <p class="text-neutral-500 dark:text-neutral-400">{{ __('No answer provided') }}</p>
                                @endif
                            @else
                                <p class="text-neutral-900 dark:text-neutral-100 whitespace-pre-wrap">
                                    {{ $studentAnswer?->text_answer ?? __('No answer provided') }}
                                </p>
                            @endif
                        </div>
                        
                        <!-- Model Answer (if available) -->
                        @if(in_array($question->question_type, ['short_answer', 'essay']) && $question->correct_answer)
                            <div class="mb-4 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                                <p class="mb-2 text-sm font-medium text-blue-800 dark:text-blue-200">{{ __('Model Answer') }}:</p>
                                <p class="text-neutral-700 dark:text-neutral-300">{{ $question->correct_answer }}</p>
                            </div>
                        @endif
                        
                        <!-- Grading Controls -->
                        <div class="mt-4 flex items-end gap-4">
                            <div class="w-32">
                                <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('Points') }}
                                </label>
                                <input 
                                    type="number" 
                                    name="grades[{{ $question->id }}][points]" 
                                    value="{{ $currentPoints }}"
                                    min="0" 
                                    max="{{ $question->points }}"
                                    class="w-full rounded-lg border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                <p class="mt-1 text-xs text-neutral-500">/ {{ $question->points }}</p>
                            </div>
                            <div class="flex-1">
                                <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                    {{ __('Feedback (optional)') }}
                                </label>
                                <textarea 
                                    name="grades[{{ $question->id }}][feedback]"
                                    rows="2"
                                    class="w-full rounded-lg border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="{{ __('Provide feedback for this answer') }}"
                                >{{ $studentAnswer?->feedback ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Overall Feedback -->
        <div class="mt-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="mb-4 text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('Overall Feedback') }}</h3>
            <textarea 
                name="feedback"
                rows="4"
                class="w-full rounded-lg border-neutral-300 px-3 py-2 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="{{ __('Provide overall feedback for this quiz attempt') }}"
            >{{ $studentGrade->feedback ?? '' }}</textarea>
        </div>
        
        <!-- Submit -->
        <div class="mt-6 flex justify-end gap-4">
            <flux:button :href="route('teacher.courses.grades.view', [$section, $assessment])" variant="outline">
                {{ __('Cancel') }}
            </flux:button>
            <x-button.submit loading-text="Saving..." variant="primary">
                Save Grades
            </x-button.submit>
        </div>
    </form>
</x-layouts::app>
