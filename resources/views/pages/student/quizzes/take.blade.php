<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ __('direction') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assessment->title }} - {{ __('Quiz') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Quiz-specific styles */
        .quiz-timer {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 50;
            background: linear-gradient(to right, #1f2937, #374151);
            color: white;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .quiz-timer.warning {
            background: linear-gradient(to right, #b45309, #d97706);
        }

        .quiz-timer.danger {
            background: linear-gradient(to right, #dc2626, #ef4444);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .quiz-content {
            margin-top: 80px;
            padding: 2rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .question-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .dark .question-card {
            background: #1f2937;
            border-color: #374151;
        }

        .option-label {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .dark .option-label {
            border-color: #374151;
        }

        .option-label:hover {
            border-color: #6366f1;
            background: #f5f3ff;
        }

        .dark .option-label:hover {
            background: #312e81;
        }

        .option-label.selected {
            border-color: #6366f1;
            background: #eef2ff;
        }

        .dark .option-label.selected {
            background: #3730a3;
        }

        .option-label input[type="radio"],
        .option-label input[type="checkbox"] {
            margin-right: 0.75rem;
            width: 1.25rem;
            height: 1.25rem;
            accent-color: #6366f1;
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            background: #6366f1;
            color: white;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        /* Prevent accidental navigation */
        .no-nav {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 no-nav" oncontextmenu="return false;">
    <!-- Timer Bar -->
    @if($assessment->time_limit_minutes > 0)
    <div class="quiz-timer" id="timerBar">
        <div class="flex items-center gap-4">
            <span class="font-semibold">{{ $assessment->title }}</span>
            <span class="text-sm opacity-80">{{ $assessment->offering?->course?->name ?? '' }}</span>
        </div>
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-mono text-xl font-bold" id="timerDisplay">
                {{ str_pad($timeRemaining['hours'], 2, '0', STR_PAD_LEFT) }}:
                {{ str_pad($timeRemaining['minutes'], 2, '0', STR_PAD_LEFT) }}:
                {{ str_pad($timeRemaining['seconds'], 2, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>
    @endif

    <!-- Quiz Content with Navigation -->
    <div class="quiz-content">
        <form method="POST" action="{{ route('student.quizzes.submit', $assessment) }}" id="quizForm">
            @csrf
            <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

            <!-- Question Navigation Panel -->
            <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Question Navigator') }}</h3>
                    <span class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $questions->count() }} {{ __('questions') }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($questions as $qIndex => $question)
                        @php
                            $savedAnswer = $savedAnswers->get($question->id);
                            $isAnswered = $savedAnswer && (
                                $savedAnswer->option_id ||
                                $savedAnswer->text_answer ||
                                $savedAnswer->answer
                            );
                        @endphp
                        <button
                            type="button"
                            onclick="scrollToQuestion({{ $qIndex }})"
                            class="question-nav-btn w-10 h-10 rounded-lg text-sm font-semibold transition-all
                                @if($qIndex === 0)
                                    bg-indigo-600 text-white
                                @elseif($isAnswered)
                                    bg-green-100 text-green-700 border border-green-300 dark:bg-green-900 dark:text-green-300 dark:border-green-700
                                @else
                                    bg-neutral-100 text-neutral-600 border border-neutral-300 dark:bg-neutral-700 dark:text-neutral-300 dark:border-neutral-600 hover:bg-neutral-200 dark:hover:bg-neutral-600
                                @endif"
                            title="{{ $question->question_type }}"
                            data-question-id="{{ $question->id }}"
                        >
                            {{ $qIndex + 1 }}
                        </button>
                    @endforeach
                </div>
                <div class="mt-3 flex items-center gap-4 text-xs text-neutral-500 dark:text-neutral-400">
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 rounded bg-indigo-600"></div>
                        <span>{{ __('Current') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 rounded bg-green-100 border border-green-300 dark:bg-green-900 dark:border-green-700"></div>
                        <span>{{ __('Answered') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <div class="w-3 h-3 rounded bg-neutral-100 border border-neutral-300 dark:bg-neutral-700 dark:border-neutral-600"></div>
                        <span>{{ __('Unanswered') }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assessment->title }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ $assessment->offering?->course?->name ?? '' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Attempt') }} {{ $attempt->attempt_number }}</p>
                </div>
            </div>

            <!-- Questions -->
            @foreach($questions as $qIndex => $question)
                <div class="question-card" data-question="{{ $qIndex }}" data-question-id="{{ $question->id }}">
                    <div class="mb-4 flex items-start">
                        <span class="question-number">{{ $qIndex + 1 }}</span>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{!! $question->question_text !!}</h3>
                            <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ $question->points }} {{ __('points') }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 ml-11">
                        @php
                            $optionsData = $question->options;
                            // Handle both string (JSON) and array formats
                            if (is_string($optionsData)) {
                                $optionsData = json_decode($optionsData, true);
                            }
                            // Convert associative array format {"A":"...","B":"..."} to indexed format
                            if (is_array($optionsData) && !empty($optionsData) && isset($optionsData[array_key_first($optionsData)]) && is_string($optionsData[array_key_first($optionsData)])) {
                                $convertedOptions = [];
                                foreach ($optionsData as $key => $value) {
                                    $convertedOptions[] = [
                                        'id' => $key,
                                        'option_text' => $value,
                                        'is_correct' => false,
                                    ];
                                }
                                $optionsData = $convertedOptions;
                            }
                            $options = $optionsData ?? [];
                            if ($assessment->shuffle_options && is_array($options)) {
                                shuffle($options);
                            }
                            $savedAnswer = $savedAnswers->get($question->id);
                        @endphp

                        @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                            @foreach($options as $optionIndex => $option)
                                @php
                                    $optionId = $option['id'] ?? ($optionIndex + 1);
                                    $optionText = $option['option_text'] ?? $option;
                                    $isSelected = $savedAnswer && $savedAnswer->option_id == $optionId;
                                @endphp
                                <label class="option-label {{ $isSelected ? 'selected' : '' }}" data-question-id="{{ $question->id }}">
                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $optionId }}"
                                        {{ $isSelected ? 'checked' : '' }}
                                        class="answer-input"
                                        onchange="updateNavigation({{ $question->id }}, true)"
                                    >
                                    <span class="text-gray-700 dark:text-gray-300">{{ $optionText }}</span>
                                </label>
                            @endforeach
                        @elseif($question->question_type === 'short_answer')
                            <div class="mt-4">
                                <textarea
                                    name="answers[{{ $question->id }}]"
                                    class="answer-input w-full rounded-lg border-gray-300 p-4 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="3"
                                    placeholder="{{ __('Type your short answer here...') }}"
                                    oninput="updateNavigation({{ $question->id }}, this.value.trim().length > 0)"
                                >{{ $savedAnswer->text_answer ?? '' }}</textarea>
                            </div>
                        @elseif($question->question_type === 'essay')
                            <div class="mt-4">
                                <textarea
                                    name="answers[{{ $question->id }}]"
                                    class="answer-input w-full rounded-lg border-gray-300 p-4 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="8"
                                    placeholder="{{ __('Type your essay answer here...') }}"
                                    oninput="updateNavigation({{ $question->id }}, this.value.trim().length > 0)"
                                >{{ $savedAnswer->text_answer ?? '' }}</textarea>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Take your time to write a comprehensive answer.') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Navigation & Submit -->
            <div class="mt-8 flex items-center justify-between">
                <button type="button" class="px-4 py-2 text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100" onclick="confirmNavigation()">
                    {{ __('Exit Quiz') }}
                </button>

                <div class="flex gap-3">
                    <x-button.submit loading-text="Submitting Quiz..." variant="primary" class="bg-green-600 hover:bg-green-700 focus:ring-green-500">
                        Submit Quiz
                    </x-button.submit>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Timer functionality
        @if($assessment->time_limit_minutes > 0)
        let totalSeconds = {{ $timeRemaining['total'] }};
        let timerInterval;

        function updateTimer() {
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            document.getElementById('timerDisplay').textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            const timerBar = document.getElementById('timerBar');
            if (totalSeconds <= 60) {
                timerBar.classList.add('danger');
                timerBar.classList.remove('warning');
            } else if (totalSeconds <= 300) {
                timerBar.classList.add('warning');
            }

            if (totalSeconds <= 0) {
                clearInterval(timerInterval);
                document.getElementById('quizForm').submit();
                return;
            }

            totalSeconds--;
        }

        // Start timer
        timerInterval = setInterval(updateTimer, 1000);
        @endif

        // Prevent accidental navigation
        function confirmNavigation() {
            if (confirm('{{ __("Are you sure you want to exit? Your progress will be saved.") }}')) {
                window.location.href = '{{ route("student.quizzes.index") }}';
            }
        }

        // Warn before leaving
        window.onbeforeunload = function() {
            return '{{ __("Are you sure you want to leave? Your quiz progress will be saved.") }}';
        };

        // Update navigation button status
        function updateNavigation(questionId, isAnswered) {
            document.querySelectorAll('.question-nav-btn').forEach(btn => {
                const btnQuestionId = btn.getAttribute('data-question-id');
                if (btnQuestionId === questionId.toString() && isAnswered) {
                    btn.classList.remove('bg-neutral-100', 'text-neutral-600', 'border-neutral-300', 'dark:bg-neutral-700', 'dark:text-neutral-300', 'dark:border-neutral-600');
                    btn.classList.add('bg-green-100', 'text-green-700', 'border', 'border-green-300', 'dark:bg-green-900', 'dark:text-green-300', 'dark:border-green-700');
                }
            });
        }

        // Question navigation function
        function scrollToQuestion(index) {
            const questions = document.querySelectorAll('.question-card');
            if (questions[index]) {
                questions[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Update current question indicator
                document.querySelectorAll('.question-nav-btn').forEach((btn, i) => {
                    if (i === index) {
                        btn.classList.remove('bg-neutral-100', 'text-neutral-600', 'border-neutral-300', 'bg-green-100', 'text-green-700', 'border-green-300', 'dark:bg-neutral-700', 'dark:text-neutral-300', 'dark:border-neutral-600', 'dark:bg-green-900', 'dark:text-green-300', 'dark:border-green-700');
                        btn.classList.add('bg-indigo-600', 'text-white');
                    } else {
                        btn.classList.remove('bg-indigo-600', 'text-white');
                    }
                });
            }
        }

        // Add selected class on change for radio buttons
        document.querySelectorAll('.answer-input[type="radio"]').forEach(input => {
            input.addEventListener('change', function() {
                // For radio buttons, remove selected from siblings
                if (this.closest('.space-y-3')) {
                    this.closest('.space-y-3').querySelectorAll('.option-label').forEach(label => {
                        label.classList.remove('selected');
                    });
                }
                if (this.checked && this.closest('.option-label')) {
                    this.closest('.option-label').classList.add('selected');
                }
            });
        });
    </script>
</body>
</html>
