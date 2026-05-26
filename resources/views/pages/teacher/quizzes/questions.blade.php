<x-layouts::app :title="__('Questions') . ' - ' . $quiz->title">

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

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex items-center gap-4 text-sm">
                <div class="px-3 py-1.5 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center gap-2">
                    <span class="font-medium">{{ $quiz->questions->count() }}</span>
                    <span class="text-neutral-500 dark:text-neutral-400">{{ __('Questions') }}</span>
                </div>
                <div class="px-3 py-1.5 rounded-lg bg-neutral-100 dark:bg-neutral-800 flex items-center gap-2">
                    <span class="font-medium">{{ $quiz->questions->sum('points') }}</span>
                    <span class="text-neutral-500 dark:text-neutral-400">{{ __('Total Points') }}</span>
                </div>
            </div>

            <div class="flex gap-3">
                <flux:button href="{{ route('teacher.quizzes.index', $offering) }}" variant="ghost">
                    {{ __('Back to Quizzes') }}
                </flux:button>
                <flux:button href="{{ route('teacher.quizzes.preview', [$offering, $quiz]) }}" variant="outline">
                    <flux:icon name="eye" class="mr-2" />
                    {{ __('Preview') }}
                </flux:button>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-300">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
        .question-item.dragging {
            opacity: 0.6;
            border: 2px dashed #6366f1;
        }
    </style>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Questions List -->
        <div class="lg:col-span-2">
            <div class="mb-3 flex items-center justify-between">
                <div class="text-sm font-medium text-neutral-600 dark:text-neutral-400">
                    {{ __('Drag questions to reorder') }}
                </div>
            </div>

            <div id="questionsList" class="space-y-4">
            @if($quiz->questions->count() > 0)
                @foreach($quiz->questions as $index => $question)
                    <div class="question-item rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 cursor-grab active:cursor-grabbing" 
                         data-question-id="{{ $question->id }}" 
                         draggable="true">
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
                                                    <flux:icon name="x-circle" class="h-5 w-5 text-neutral-400" />
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
                            <div class="flex items-center gap-1">
                                <!-- Reorder -->
                                @if(!$loop->first)
                                    <form method="POST" action="{{ route('teacher.quizzes.reorder', [$offering, $quiz]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="order[]" value="{{ $question->id }}">
                                        @foreach($quiz->questions->where('id', '!=', $question->id) as $q)
                                            <input type="hidden" name="order[]" value="{{ $q->id }}">
                                        @endforeach
                                        <button type="submit" class="p-1 text-neutral-400 hover:text-neutral-600" title="{{ __('Move up') }}">
                                            ↑
                                        </button>
                                    </form>
                                @endif

                                @if(!$loop->last)
                                    <form method="POST" action="{{ route('teacher.quizzes.reorder', [$offering, $quiz]) }}" class="inline">
                                        @csrf
                                        @foreach($quiz->questions->where('id', '!=', $question->id) as $q)
                                            <input type="hidden" name="order[]" value="{{ $q->id }}">
                                        @endforeach
                                        <input type="hidden" name="order[]" value="{{ $question->id }}">
                                        <button type="submit" class="p-1 text-neutral-400 hover:text-neutral-600" title="{{ __('Move down') }}">
                                            ↓
                                        </button>
                                    </form>
                                @endif

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
            </div> <!-- /#questionsList -->
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
                        <select name="question_type" id="questionType" onchange="toggleQuestionOptions()" class="w-full">
                            <option value="multiple_choice">{{ __('Multiple Choice') }}</option>
                            <option value="true_false">{{ __('True/False') }}</option>
                            <option value="short_answer">{{ __('Short Answer') }}</option>
                            <option value="essay">{{ __('Essay') }}</option>
                        </select>
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Points') }}</flux:label>
                        <flux:input type="number" name="points" value="1" min="1" required />
                    </flux:field>

                    <!-- Multiple Choice Options -->
                    <div id="multipleChoiceOptions" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <flux:label>{{ __('Options') }}</flux:label>
                            <flux:button type="button" variant="ghost" size="sm" onclick="addMcOption()">
                                <flux:icon name="plus" class="size-4 mr-1" />
                                {{ __('Add Option') }}
                            </flux:button>
                        </div>

                        <div id="optionsContainer" class="space-y-2"></div>

                        <p class="text-xs text-neutral-500">{{ __('Select the radio button next to the correct answer. Minimum 2 options required.') }}</p>
                    </div>

                    <!-- True/False Options -->
                    <div id="trueFalseOptions" class="space-y-3" style="display: none;">
                        <flux:label>{{ __('Correct Answer') }}</flux:label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="correct_answer" value="true" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('True') }}</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="correct_answer" value="false" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-neutral-700 dark:text-neutral-300">{{ __('False') }}</span>
                            </label>
                        </div>
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

            // Just show/hide panels. Disabling is handled on submit to avoid breaking Flux components.
            mcOptions.style.display = (type === 'multiple_choice') ? 'block' : 'none';
            tfOptions.style.display = (type === 'true_false') ? 'block' : 'none';
            modelAnswer.style.display = (!['multiple_choice', 'true_false'].includes(type)) ? 'block' : 'none';
        }

        // Disable fields from non-active sections right before submit (safer with Flux custom elements)
        document.getElementById('questionForm').addEventListener('submit', function () {
            const type = document.getElementById('questionType').value;

            if (type !== 'multiple_choice' && document.getElementById('multipleChoiceOptions')) {
                document.getElementById('multipleChoiceOptions').querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
            }
            if (type !== 'true_false' && document.getElementById('trueFalseOptions')) {
                document.getElementById('trueFalseOptions').querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
            }
            if (!['short_answer', 'essay'].includes(type) && document.getElementById('modelAnswer')) {
                document.getElementById('modelAnswer').querySelectorAll('input, textarea, select').forEach(el => el.disabled = true);
            }
        });

        // Initialize on page load
        toggleQuestionOptions();

        // ==================== Dynamic Multiple Choice Options ====================
        let mcOptionCount = 0;

        function addMcOption() {
            const container = document.getElementById('optionsContainer');
            const index = mcOptionCount++;

            const div = document.createElement('div');
            div.className = 'flex items-center gap-2';
            div.innerHTML = `
                <input type="radio" name="correct_option" value="${index}" class="h-4 w-4 text-indigo-600">
                <input type="text" name="options[${index}][option_text]" 
                       placeholder="{{ __('Option') }} ${index + 1}" 
                       class="flex-1 px-3 py-2 text-sm border border-neutral-300 dark:border-neutral-600 rounded-lg bg-white dark:bg-neutral-800">
                <button type="button" onclick="removeMcOption(this)" 
                        class="text-red-500 hover:text-red-700 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6h12v12"></path>
                    </svg>
                </button>
            `;
            container.appendChild(div);

            // If this is the first option, make it checked by default
            if (container.children.length === 1) {
                div.querySelector('input[type="radio"]').checked = true;
            }
        }

        function removeMcOption(button) {
            const container = document.getElementById('optionsContainer');
            if (container.children.length <= 2) {
                alert('{{ __('At least 2 options are required.') }}');
                return;
            }
            button.closest('.flex').remove();
        }

        function initDefaultMcOptions() {
            const container = document.getElementById('optionsContainer');
            if (container.children.length === 0) {
                // Add 2 default options
                addMcOption();
                addMcOption();
            }
        }

        // Auto-init default options when switching to Multiple Choice
        const questionTypeSelect = document.getElementById('questionType');
        if (questionTypeSelect) {
            questionTypeSelect.addEventListener('change', function () {
                if (this.value === 'multiple_choice') {
                    setTimeout(initDefaultMcOptions, 50); // small delay for display
                }
            });
        }

        // Also init on first load if MC is default
        if (questionTypeSelect && questionTypeSelect.value === 'multiple_choice') {
            setTimeout(initDefaultMcOptions, 100);
        }

        function editQuestion(questionId) {
            // TODO: Implement edit functionality (modal or separate page)
            alert('Edit question feature coming soon. Question ID: ' + questionId);
        }

        // ==================== Drag & Drop Reordering ====================
        function initDragAndDrop() {
            const list = document.getElementById('questionsList');
            if (!list) return;

            let draggedItem = null;

            list.addEventListener('dragstart', function(e) {
                draggedItem = e.target.closest('.question-item');
                if (draggedItem) {
                    draggedItem.classList.add('dragging');
                }
            });

            list.addEventListener('dragend', function(e) {
                if (draggedItem) {
                    draggedItem.classList.remove('dragging');
                    draggedItem = null;
                }
            });

            list.addEventListener('dragover', function(e) {
                e.preventDefault();
                const afterElement = getDragAfterElement(list, e.clientY);
                const item = e.target.closest('.question-item');
                if (item && item !== draggedItem) {
                    if (afterElement == null) {
                        list.appendChild(draggedItem);
                    } else {
                        list.insertBefore(draggedItem, afterElement);
                    }
                }
            });

            list.addEventListener('drop', function(e) {
                e.preventDefault();
                // Save new order
                saveNewOrder();
            });

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.question-item:not(.dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY }).element;
            }
        }

        function saveNewOrder() {
            const list = document.getElementById('questionsList');
            const items = list.querySelectorAll('.question-item');
            const order = Array.from(items).map(item => item.dataset.questionId);

            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("teacher.quizzes.reorder", [$offering, $quiz]) }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            order.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'order[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        // Initialize drag and drop
        initDragAndDrop();
    </script>
</x-layouts::app>
