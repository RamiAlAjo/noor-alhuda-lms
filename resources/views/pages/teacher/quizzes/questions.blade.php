<x-app-layout>
    <x-slot name="title">{{ __('Questions') }} - {{ $quiz->title }}</x-slot>

    <div class="mb-6">
        <nav class="flex text-sm text-gray-400">
            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('Courses') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.courses.show', $offering) }}" class="hover:text-white">{{ $offering->course?->name ?? __('Course') }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('teacher.quizzes.index', $offering) }}" class="hover:text-white">{{ __('Quizzes') }}</a>
            <span class="mx-2">/</span>
            <span class="text-white">{{ $quiz->title }}</span>
            <span class="mx-2">/</span>
            <span class="text-white">{{ __('Questions') }}</span>
        </nav>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Questions') }}</h1>
            <p class="text-neutral-600 dark:text-neutral-400">{{ $quiz->title }}</p>
        </div>
        <div class="flex gap-3">
            <flux:button href="{{ route('teacher.quizzes.index', $offering) }}" variant="ghost">
                {{ __('Back to Quizzes') }}
            </flux:button>
            <flux:button href="{{ route('teacher.quizzes.preview', [$offering, $quiz]) }}" variant="secondary">
                <flux:icon name="eye" class="mr-2" />
                {{ __('Preview') }}
            </flux:button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Questions List -->
        <div class="lg:col-span-2 space-y-4">
            @if($quiz->questions->count() > 0)
                @foreach($quiz->questions as $index => $question)
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800" data-question-id="{{ $question->id }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                        {{ $index + 1 }}
                                    </span>
                                    <flux:badge>
                                        @if($question->question_type === 'multiple_choice')
                                            {{ __('Multiple Choice') }}
                                        @elseif($question->question_type === 'true_false')
                                            {{ __('True/False') }}
                                        @elseif($question->question_type === 'short_answer')
                                            {{ __('Short Answer') }}
                                        @else
                                            {{ __('Essay') }}
                                        @endif
                                    </flux:badge>
                                    <span class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $question->points }} {{ __('points') }}
                                    </span>
                                </div>
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    {!! $question->question_text !!}
                                </div>

                                @if(in_array($question->question_type, ['multiple_choice', 'true_false']) && $question->options)
                                    <div class="mt-4 space-y-2">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center gap-2 rounded-lg p-2 @if($option['is_correct']) bg-green-50 dark:bg-green-900/20 @else bg-neutral-50 dark:bg-neutral-700/50 @endif">
                                                @if($option['is_correct'])
                                                    <flux:icon name="check-circle" class="h-5 w-5 text-green-500" />
                                                @else
                                                    <flux:icon name="circle" class="h-5 w-5 text-neutral-400" />
                                                @endif
                                                <span class="{{ $option['is_correct'] ? 'font-medium text-green-700 dark:text-green-300' : 'text-neutral-600 dark:text-neutral-300' }}">
                                                    {{ $option['option_text'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->correct_answer)
                                    <div class="mt-4 rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20">
                                        <span class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ __('Model Answer:') }}</span>
                                        <p class="mt-1 text-blue-600 dark:text-blue-400">{{ $question->correct_answer }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:button variant="ghost" size="sm" icon="pencil" onclick="editQuestion({{ $question->id }})" />
                                <form method="POST" action="{{ route('teacher.quizzes.questions.destroy', [$offering, $quiz, $question]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-700" onclick="return confirm('{{ __('Are you sure?') }}')" />
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="rounded-xl border border-neutral-200 bg-white p-12 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <flux:icon name="puzzle-piece" class="mx-auto h-12 w-12 text-neutral-400" />
                    <h3 class="mt-4 text-lg font-medium text-neutral-900 dark:text-white">{{ __('No questions yet') }}</h3>
                    <p class="mt-2 text-neutral-600 dark:text-neutral-400">{{ __('Add your first question using the form on the right.') }}</p>
                </div>
            @endif
        </div>

        <!-- Add Question Form -->
        <div class="space-y-6">
            <div class="sticky top-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Add Question') }}</h2>

                <form method="POST" action="{{ route('teacher.quizzes.questions.store', [$offering, $quiz]) }}" id="questionForm" class="space-y-4">
                    @csrf

                    <flux:field>
                        <flux:label>{{ __('Question Text') }}</flux:label>
                        <flux:textarea name="question_text" rows="3" required id="questionText" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Question Type') }}</flux:label>
                        <flux:select name="question_type" id="questionType" onchange="toggleQuestionOptions()">
                            <flux:select.option value="multiple_choice">{{ __('Multiple Choice') }}</flux:select.option>
                            <flux:select.option value="true_false">{{ __('True/False') }}</flux:select.option>
                            <flux:select.option value="short_answer">{{ __('Short Answer') }}</flux:select.option>
                            <flux:select.option value="essay">{{ __('Essay') }}</flux:select.option>
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Points') }}</flux:label>
                        <flux:input type="number" name="points" value="1" min="1" required />
                    </flux:field>

                    <!-- Multiple Choice Options -->
                    <div id="multipleChoiceOptions" class="space-y-3">
                        <flux:label>{{ __('Options') }}</flux:label>
                        <div id="optionsContainer">
                            <div class="flex items-center gap-2 mb-2">
                                <input type="radio" name="correct_option" value="0" checked class="h-4 w-4 text-indigo-600">
                                <flux:input type="text" name="options[0][option_text]" placeholder="{{ __('Option 1') }}" class="flex-1" />
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <input type="radio" name="correct_option" value="1" class="h-4 w-4 text-indigo-600">
                                <flux:input type="text" name="options[1][option_text]" placeholder="{{ __('Option 2') }}" class="flex-1" />
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <input type="radio" name="correct_option" value="2" class="h-4 w-4 text-indigo-600">
                                <flux:input type="text" name="options[2][option_text]" placeholder="{{ __('Option 3') }}" class="flex-1" />
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <input type="radio" name="correct_option" value="3" class="h-4 w-4 text-indigo-600">
                                <flux:input type="text" name="options[3][option_text]" placeholder="{{ __('Option 4') }}" class="flex-1" />
                            </div>
                        </div>
                        <p class="text-xs text-neutral-500">{{ __('Select the radio button next to the correct answer.') }}</p>
                    </div>

                    <!-- True/False Options -->
                    <div id="trueFalseOptions" class="space-y-3" style="display: none;">
                        <flux:label>{{ __('Correct Answer') }}</flux:label>
                        <flux:radio.group>
                            <flux:radio name="correct_answer" value="true" label="{{ __('True') }}" checked />
                            <flux:radio name="correct_answer" value="false" label="{{ __('False') }}" />
                        </flux:radio.group>
                    </div>

                    <!-- Short Answer / Essay Model Answer -->
                    <div id="modelAnswer" class="space-y-3" style="display: none;">
                        <flux:field>
                            <flux:label>{{ __('Model Answer (for reference)') }}</flux:label>
                            <flux:textarea name="correct_answer" rows="3" placeholder="{{ __('Enter the expected answer for grading reference...') }}" />
                        </flux:field>
                    </div>

                    <x-button.submit loading-text="Adding..." variant="primary" class="w-full">
                        <flux:icon name="plus" class="size-5" />
                        Add Question
                    </x-button.submit>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleQuestionOptions() {
            const type = document.getElementById('questionType').value;
            const mcOptions = document.getElementById('multipleChoiceOptions');
            const tfOptions = document.getElementById('trueFalseOptions');
            const modelAnswer = document.getElementById('modelAnswer');

            mcOptions.style.display = 'none';
            tfOptions.style.display = 'none';
            modelAnswer.style.display = 'none';

            if (type === 'multiple_choice') {
                mcOptions.style.display = 'block';
            } else if (type === 'true_false') {
                tfOptions.style.display = 'block';
            } else {
                modelAnswer.style.display = 'block';
            }
        }

        // Initialize on page load
        toggleQuestionOptions();
    </script>
</x-app-layout>
