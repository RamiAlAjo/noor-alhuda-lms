<x-layouts::app :title="__('Manage Questions')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Manage Questions') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">
                {{ $assessment->title }} - {{ $section->course?->name ?? __('Course') }}
            </p>
        </div>
        <flux:button :href="route('teacher.courses.assessments', $section)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Assessments') }}
        </flux:button>
    </div>

    <!-- Quiz Info -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quiz Settings') }}</h2>
                <div class="mt-2 flex flex-wrap gap-3">
                    @if($assessment->time_limit_minutes > 0)
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $assessment->time_limit_minutes }} {{ __('minutes') }}
                        </span>
                    @endif
                    @if($assessment->attempts_allowed)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ $assessment->attempts_allowed }} {{ __('attempts') }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            {{ __('Unlimited attempts') }}
                        </span>
                    @endif
                    <span class="inline-flex items-center rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        {{ $assessment->questions->count() }} {{ __('questions') }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                        {{ $assessment->questions->sum('points') }} {{ __('total points') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Form -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Add New Question') }}</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Create questions for this quiz') }}</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('teacher.courses.assessments.questions.store', [$section, $assessment]) }}" class="p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <flux:select name="question_type" :label="__('Question Type')" required onchange="toggleOptionsField(this.value)">
                    <flux:select.option value="multiple_choice">{{ __('Multiple Choice') }}</flux:select.option>
                    <flux:select.option value="true_false">{{ __('True/False') }}</flux:select.option>
                    <flux:select.option value="short_answer">{{ __('Short Answer') }}</flux:select.option>
                    <flux:select.option value="essay">{{ __('Essay') }}</flux:select.option>
                </flux:select>
                <flux:input name="points" type="number" :label="__('Points')" placeholder="10" required min="1" />
            </div>

            <div class="mt-4">
                <flux:textarea name="question_text" :label="__('Question Text')" placeholder="{{ __('Enter your question') }}" required rows="3" />
            </div>

            <!-- Options for Multiple Choice -->
            <div id="optionsField" class="mt-4">
                <label class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Answer Options') }}</label>
                <div id="optionsContainer" class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5 mt-6">
                            <input type="radio" name="correct_option" value="0" checked class="h-4 w-4 border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <flux:input name="options[0][option_text]" :placeholder="__('Option 1')" class="flex-1" />
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5 mt-6">
                            <input type="radio" name="correct_option" value="1" class="h-4 w-4 border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <flux:input name="options[1][option_text]" :placeholder="__('Option 2')" class="flex-1" />
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5 mt-6">
                            <input type="radio" name="correct_option" value="2" class="h-4 w-4 border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <flux:input name="options[2][option_text]" :placeholder="__('Option 3')" class="flex-1" />
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex items-center h-5 mt-6">
                            <input type="radio" name="correct_option" value="3" class="h-4 w-4 border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <flux:input name="options[3][option_text]" :placeholder="__('Option 4')" class="flex-1" />
                    </div>
                </div>
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Select the radio button next to the correct answer') }}</p>
            </div>

            <!-- Correct Answer for Short Answer/Essay -->
            <div id="correctAnswerField" class="mt-4" style="display: none;">
                <flux:textarea name="correct_answer" :label="__('Model Answer (Optional)')" placeholder="{{ __('Expected answer for manual grading') }}" rows="2" />
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">{{ __('This will be used as a reference when grading') }}</p>
            </div>

            <div class="mt-6">
                <x-button.submit loading-text="Adding..." variant="primary">
                    Add Question
                </x-button.submit>
            </div>
        </form>
    </div>

    <!-- Questions List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Questions') }}</h2>
        </div>
        <div class="p-6">
            @if($assessment->questions->isEmpty())
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-neutral-500 dark:text-neutral-400">{{ __('No questions added yet') }}</p>
                    <p class="text-sm text-neutral-400 dark:text-neutral-500">{{ __('Add questions using the form above') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($assessment->questions as $qIndex => $question)
                        <div class="flex items-start justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                                    <span class="text-sm font-semibold">{{ $qIndex + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                            {{ $question->points }} {{ __('points') }}
                                        </span>
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
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
                                    <p class="text-neutral-900 dark:text-neutral-100">{!! $question->question_text !!}</p>
                                    @if($question->question_type === 'multiple_choice' && $question->options)
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($question->options as $option)
                                                <span class="inline-flex items-center rounded px-2 py-1 text-xs
                                                    @if(isset($option->is_correct) && $option->is_correct)
                                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @else
                                                        bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300
                                                    @endif">
                                                    {!! $option->option_text !!}
                                                    @if(isset($option->is_correct) && $option->is_correct)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif($question->question_type === 'true_false')
                                        <div class="mt-2">
                                            <span class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Correct Answer') }}:</span>
                                            <span class="ml-2 text-sm font-semibold text-green-600 dark:text-green-400">
                                                {{ $question->correct_answer }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('teacher.questions.destroy', $question) }}" onsubmit="return confirm('Are you sure you want to delete this question?')">
                                @csrf
                                @method('DELETE')
                                <flux:button type="submit" size="sm" variant="danger" class="text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </flux:button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleOptionsField(value) {
            const optionsField = document.getElementById('optionsField');
            const correctAnswerField = document.getElementById('correctAnswerField');
            
            if (value === 'multiple_choice') {
                optionsField.style.display = 'block';
                correctAnswerField.style.display = 'none';
            } else if (value === 'true_false') {
                optionsField.style.display = 'none';
                correctAnswerField.style.display = 'block';
            } else {
                optionsField.style.display = 'none';
                correctAnswerField.style.display = 'block';
            }
        }
    </script>
</x-layouts::app>
