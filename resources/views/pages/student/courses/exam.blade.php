<x-layouts::app :title="__('Exam')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex text-sm text-gray-400">
                    <a href="{{ route('student.courses.index') }}" class="hover:text-white">{{ __('My Courses') }}</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('student.courses.show', $assessment->section) }}" class="hover:text-white">{{ $assessment->section?->course?->name ?? __('Course') }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-white">{{ $assessment->title }}</span>
                </nav>
                <h1 class="mt-2 text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                    {{ $assessment->title }}
                </h1>
                <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                    {{ $assessment->section?->course?->name ?? __('Course') }} - {{ $assessment->section?->name ?? '' }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Time Remaining') }}</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400" id="timer">--:--</p>
            </div>
        </div>

        <!-- Instructions -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Instructions') }}
            </h2>
            <ul class="list-disc space-y-2 pl-5 text-neutral-600 dark:text-neutral-400">
                <li>{{ __('This exam has') }} {{ $assessment->questions->count() }} {{ __('questions') }}</li>
                <li>{{ __('Total marks') }}: {{ $assessment->max_score }}</li>
                <li>{{ __('Weight') }}: {{ $assessment->weight }}%</li>
                @if($assessment->due_date)
                    <li>{{ __('Due date') }}: {{ $assessment->due_date->format('M d, Y H:i') }}</li>
                @endif
                <li>{{ __('Make sure you have a stable internet connection') }}</li>
                <li>{{ __('Do not refresh the page while taking the exam') }}</li>
            </ul>
        </div>

        <!-- Questions -->
        <form method="POST" action="#" class="space-y-6">
            @csrf

            @forelse($assessment->questions as $index => $question)
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="mb-4 flex items-start justify-between">
                        <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                            {{ __('Question') }} {{ $index + 1 }}
                            <span class="text-sm font-normal text-neutral-500">
                                ({{ $question->marks }} {{ __('marks') }})
                            </span>
                        </h3>
                    </div>

                    <div class="mb-4 text-neutral-700 dark:text-neutral-300">
                        {!! $question->question_text !!}
                    </div>

                    @if($question->type === 'multiple_choice')
                        <div class="space-y-2">
                            @foreach($question->options as $optionIndex => $option)
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-neutral-200 p-4 hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-700">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optionIndex }}" class="h-4 w-4 text-neutral-600">
                                    <span class="text-neutral-700 dark:text-neutral-300">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    @elseif($question->type === 'true_false')
                        <div class="flex gap-4">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="answers[{{ $question->id }}]" value="true" class="h-4 w-4 text-neutral-600">
                                <span class="text-neutral-700 dark:text-neutral-300">{{ __('True') }}</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="answers[{{ $question->id }}]" value="false" class="h-4 w-4 text-neutral-600">
                                <span class="text-neutral-700 dark:text-neutral-300">{{ __('False') }}</span>
                            </label>
                        </div>
                    @elseif($question->type === 'short_answer' || $question->type === 'essay')
                        <flux:textarea
                            name="answers[{{ $question->id }}]"
                            rows="4"
                            :placeholder="__('Enter your answer')"
                        ></flux:textarea>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                    <p class="text-center text-neutral-500 dark:text-neutral-400">
                        {{ __('No questions available for this assessment') }}
                    </p>
                </div>
            @endforelse

            <!-- Submit -->
            <div class="flex items-center justify-between">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ __('Make sure to review your answers before submitting') }}
                </p>
                <div class="flex gap-2">
                    <flux:button type="button" variant="ghost" onclick="window.history.back()">
                        {{ __('Save & Continue Later') }}
                    </flux:button>
                    <x-button.submit loading-text="Submitting Exam..." variant="primary" 
                        onclick="return confirm('{{ __('Are you sure you want to submit? You cannot change your answers after submission.') }}')">
                        Submit Exam
                    </x-button.submit>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Timer countdown (if applicable)
        let timeLeft = 3600; // 1 hour default
        const timer = document.getElementById('timer');

        function updateTimer() {
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;

            timer.textContent =
                (hours > 0 ? hours + ':' : '') +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            if (timeLeft <= 0) {
                timer.textContent = '00:00';
                alert('{{ __('Time is up! Your answers will be submitted automatically.') }}');
                // Auto-submit form
                document.querySelector('form').submit();
            }

            timeLeft--;
        }

        // Start timer if exam has time limit
        // setInterval(updateTimer, 1000);
    </script>
    @endpush
</x-layouts.app>
