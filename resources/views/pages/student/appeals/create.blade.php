<?php
/**
 * Page: Submit Grade Appeal
 *
 * Purpose: Display a form for submitting a new grade appeal.
 * Allows students to select a grade, provide subject, description, justification, and upload attachments.
 *
 * Route: student.appeals.create (GET)
 *
 * Controller: App\Http\Controllers\Student\GradeAppealController@create
 *
 * Components on this page:
 * - Back navigation link
 * - Form with sections:
 *   - Grade selection dropdown (grouped by course)
 *   - Subject input (required)
 *   - Description textarea (required)
 *   - Student justification textarea (required)
 *   - Grade information inputs (current grade, requested grade)
 *   - File attachments upload
 * - Submit and Cancel buttons
 *
 * Required Data variables:
 * - $enrollments: Collection of Enrollment objects with grades
 * - $grade: Optional Grade object for pre-selection
 *
 * Dependencies:
 * - Routes: student.appeals.index, student.appeals.store
 * - Helpers: __(), route(), old()
 * - Relationships: Enrollment->offering->course, Enrollment->grades->assessment
 * - Flux UI components: flux:icon
 *
 * @package App\Views\Pages\Student\Appeals
 */
?>
<x-slot name="title">{{ __('lms.submit_grade_appeal') }}</x-slot>

<div class="mb-6">
    <div class="flex items-center mb-4">
        <a href="{{ route('student.appeals.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.submit_grade_appeal') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <form action="{{ route('student.appeals.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Grade Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.select_grade') }}</label>
                <select name="grade_id" id="grade_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('lms.select_grade_optional') }}</option>
                    @foreach($enrollments as $enrollment)
                        <optgroup label="{{ $enrollment->offering?->course?->name ?? __('Unknown Course') }}">
                            @foreach($enrollment->grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id || ($grade && $grade->id == $grade?->id) ? 'selected' : '' }}>
                                    {{ $grade->assessment?->title ?? 'Assessment' }} - {{ $grade->grade }}%
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ __('lms.select_grade_description') }}</p>
            </div>

            <!-- Subject -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.subject') }} <span class="text-red-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.appeal_subject_placeholder') }}" required>
                @error('subject')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.description') }} <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.appeal_description_placeholder') }}" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Student Justification -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.justification') }} <span class="text-red-500">*</span></label>
                <textarea name="student_justification" rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.justification_placeholder') }}" required>{{ old('student_justification') }}</textarea>
                @error('student_justification')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Grade Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.current_grade') }}</label>
                    <input type="number" name="current_grade" step="0.01" min="0" max="100" value="{{ old('current_grade', $grade?->grade) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="e.g., 75.50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.requested_grade') }}</label>
                    <input type="number" name="requested_grade" step="0.01" min="0" max="100" value="{{ old('requested_grade') }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        placeholder="e.g., 85.00">
                </div>
            </div>

            <!-- Attachments -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('lms.attachments') }}</label>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4">
                    <input type="file" name="attachments[]" multiple
                        class="w-full text-gray-700 dark:text-gray-300"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        {{ __('lms.attachments_help') }}
                    </p>
                </div>
                @error('attachments.*')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('student.appeals.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('lms.cancel') }}
                </a>
                <x-button.submit loading-text="Submitting..." variant="primary">
                    {{ __('lms.submit_appeal') }}
                </x-button.submit>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('grade_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        // You can add AJAX here to fetch grade details
    });
</script>
@endpush
