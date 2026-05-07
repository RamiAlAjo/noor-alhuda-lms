<?php
/**
 * Page: Teacher Student Progress Report
 *
 * Purpose: Display detailed progress report for a specific student in a course.
 * Shows overall progress, earned weight, assessment progress with grades and class averages.
 *
 * Route: teacher.reports.student-progress (GET)
 *
 * Controller: App\Http\Controllers\Teacher\ReportController@studentProgress
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Course and student selection form
 * - Overall progress summary cards (progress %, earned weight, assessments completed)
 * - Assessment progress table with grades and class averages
 * - Export CSV button
 *
 * Required Data variables:
 * - $offerings: Collection of CourseOffering objects for the teacher
 * - $offeringId: Currently selected offering ID
 * - $students: Collection of enrolled students (when offering selected)
 * - $selectedStudentId: Currently selected student ID
 * - $report: Report data array (optional)
 *
 * Dependencies:
 * - Routes: teacher.reports.export-student-progress
 * - Helpers: __(), route(), number_format()
 * - Relationships: CourseOffering->course, Student->name
 *
 * @package App\Views\Pages\Teacher\Reports
 */
?>
<x-layouts::app :title="__('lms.student_progress_report')">

<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.student_progress_report') }}</h1>

    <!-- Course and Student Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.select_student') }}</h2>
        <form method="GET" action="{{ route('teacher.reports.student-progress') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.select_course') }}</label>
                <select name="offering_id" id="offeringSelect" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('lms.select_a_course') }}</option>
                    @foreach($offerings as $offering)
                        <option value="{{ $offering->id }}" {{ $offeringId == $offering->id ? 'selected' : '' }}>
                            {{ $offering->course?->name ?? __('Course') }} - {{ $offering->section_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($offeringId && $students->count() > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.select_student') }}</label>
                <select name="student_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('lms.select_a_student') }}</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                {{ __('lms.view_report') }}
            </button>
        </form>
    </div>

    @if($report)
    <!-- Student Progress Report -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $report['student']->name }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $report['offering']->course?->name ?? __('Course') }} - {{ $report['offering']->section_name }}
                </p>
            </div>
            <a href="{{ route('teacher.reports.export-student-progress', [$offeringId, $selectedStudentId]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                {{ __('lms.export_csv') }}
            </a>
        </div>

        <!-- Overall Progress -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('lms.overall_progress') }}</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ number_format($report['overall_progress'], 2) }}%</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <p class="text-sm text-green-600 dark:text-green-400">{{ __('lms.earned_weight') }}</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ number_format($report['earned_weight'], 2) }} / {{ number_format($report['total_weight'], 2) }}</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <p class="text-sm text-purple-600 dark:text-purple-400">{{ __('lms.assessments_completed') }}</p>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">
                    {{ $report['grades']->count() }} / {{ $report['assessments']->count() }}
                </p>
            </div>
        </div>

        <!-- Assessment Progress Table -->
        <div>
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">{{ __('lms.assessment_progress') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.assessment') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.weight') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.grade') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.class_average') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.weighted_grade') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($report['assessment_progress'] as $progress)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $progress['assessment']->title }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $progress['weight'] }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                @if($progress['grade'])
                                    {{ number_format($progress['grade']->grade, 2) }}
                                @else
                                    <span class="text-gray-400">{{ __('lms.not_submitted') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                {{ number_format($report['class_averages'][$progress['assessment']->id] ?? 0, 2) }}
                            </td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                @if($progress['weighted_grade'])
                                    {{ number_format($progress['weighted_grade'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</x-layouts::app>
