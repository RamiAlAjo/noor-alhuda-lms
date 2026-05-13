<x-layouts::app :title="__('Bulk Grade Assessment')">
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
                            <a href="{{ route('teacher.courses.index') }}" class="hover:text-white">{{ __('My Courses') }}</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('teacher.courses.show', $section) }}" class="hover:text-white">{{ $section->course?->name . ' - ' . $section->name }}</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('teacher.courses.grades', $section) }}" class="hover:text-white">{{ __('Grades') }}</a>
                            <span class="mx-2">/</span>
                            <span class="text-white">{{ $assessment->title }}</span>
                        </nav>
                        <h1 class="mt-1 text-2xl font-bold text-white">
                            {{ __('Bulk Grade') }}: {{ $assessment->title }}
                        </h1>
                        <p class="mt-1 text-indigo-100">
                            {{ __('Grade multiple students at once') }}
                        </p>
                    </div>
                </div>
                <flux:button :href="route('teacher.courses.grades.view', [$section, $assessment])" variant="ghost" class="!text-white hover:!bg-white/20">
                    {{ __('Back to Assessment') }}
                    <flux:icon.arrow-left class="w-4 h-4 ml-2" />
                </flux:button>
            </div>
        </div>

        <!-- Assessment Info -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-indigo-100 p-2 dark:bg-indigo-900">
                        <flux:icon.users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Students to Grade') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $ungradedEnrollments->count() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-green-100 p-2 dark:bg-green-900">
                        <flux:icon.clipboard-document-list class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Assessment Type') }}</p>
                        <p class="text-lg font-bold text-neutral-900 dark:text-neutral-100">
                            {{ ucfirst(str_replace('_', ' ', $assessment->assessment_type ?? 'quiz')) }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900">
                        <flux:icon.chart-bar class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Max Score') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $assessment->max_score ?? 100 }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-amber-100 p-2 dark:bg-amber-900">
                        <flux:icon.check-circle class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Passing Score') }}</p>
                        <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $assessment->passing_score ?? 60 }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if($ungradedEnrollments->count() > 0)
        <!-- Bulk Grading Form -->
        <form action="{{ route('teacher.courses.assessments.bulk-grade.store', [$section, $assessment]) }}" method="POST" class="space-y-6">
            @csrf

            <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800 shadow-sm overflow-hidden">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Grade Students') }}</h2>
                        <div class="flex items-center gap-2">
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" id="selectAll" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                                {{ __('Select All') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @foreach($ungradedEnrollments as $enrollment)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-200 font-semibold text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ strtoupper(substr($enrollment->student->first_name ?? 'U', 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-base font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $enrollment->student->name }}
                                    </h3>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $enrollment->student->email }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <input type="checkbox" name="selected_students[]" value="{{ $enrollment->student_id }}" class="student-checkbox rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500">
                                <div class="flex items-center gap-2">
                                    <flux:input
                                        name="grades[{{ $enrollment->student_id }}][grade]"
                                        type="number"
                                        placeholder="{{ __('Grade') }}"
                                        min="0"
                                        :max="$assessment->max_score ?? 100"
                                        class="w-20"
                                        required
                                    />
                                    <span class="text-sm text-neutral-500">/ {{ $assessment->max_score ?? 100 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" name="grades[{{ $enrollment->student_id }}][student_id]" value="{{ $enrollment->student_id }}">

                        <!-- Feedback -->
                        <div class="mt-4">
                            <flux:textarea
                                name="grades[{{ $enrollment->student_id }}][feedback]"
                                placeholder="{{ __('Add feedback (optional)') }}"
                                rows="2"
                                class="w-full"
                            />
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ __('Selected students will be graded with the entered scores') }}
                        </p>
                        <div class="flex items-center gap-3">
                            <flux:button :href="route('teacher.courses.grades.view', [$section, $assessment])" variant="ghost">
                                {{ __('Cancel') }}
                            </flux:button>
                            <flux:button type="submit" variant="primary" id="bulkGradeBtn" disabled>
                                {{ __('Save Grades') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @else
        <!-- No students to grade -->
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800 shadow-sm">
            <div class="p-12 text-center">
                <div class="flex justify-center mb-4">
                    <div class="rounded-full bg-green-100 p-4 dark:bg-green-900">
                        <flux:icon.check-circle class="w-8 h-8 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-2">
                    {{ __('All students graded!') }}
                </h3>
                <p class="text-neutral-500 dark:text-neutral-400 mb-6">
                    {{ __('All enrolled students have been graded for this assessment.') }}
                </p>
                <flux:button :href="route('teacher.courses.grades.view', [$section, $assessment])" variant="primary">
                    {{ __('View Results') }}
                </flux:button>
            </div>
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            const bulkGradeBtn = document.getElementById('bulkGradeBtn');

            // Handle select all
            selectAllCheckbox.addEventListener('change', function() {
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkGradeButton();
            });

            // Handle individual checkboxes
            studentCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkGradeButton);
            });

            function updateBulkGradeButton() {
                const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
                bulkGradeBtn.disabled = checkedBoxes.length === 0;
            }

            // Form submission validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('{{ __("Please select at least one student to grade") }}');
                    return false;
                }

                // Validate grades
                let valid = true;
                checkedBoxes.forEach(checkbox => {
                    const studentId = checkbox.value;
                    const gradeInput = document.querySelector(`input[name="grades[${studentId}][grade]"]`);
                    const grade = parseFloat(gradeInput.value);

                    if (isNaN(grade) || grade < 0 || grade > {{ $assessment->max_score ?? 100 }}) {
                        valid = false;
                        gradeInput.focus();
                        return false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('{{ __("Please enter valid grades for all selected students") }}');
                    return false;
                }
            });
        });
    </script>
</x-layouts::app>