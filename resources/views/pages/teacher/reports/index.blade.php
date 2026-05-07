<?php
/**
 * Page: Teacher Class Performance Report
 *
 * Purpose: Display class performance reports for a selected course offering.
 * Shows summary statistics, grade distribution, assessment stats, and student performance.
 *
 * Route: teacher.reports.index (GET)
 *
 * Controller: App\Http\Controllers\Teacher\ReportController@index
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Course selection form
 * - Report summary cards (total students, class average, highest/lowest score)
 * - Grade distribution display
 * - Assessment statistics table
 * - Student performance table with grades
 * - Export CSV button
 *
 * Required Data variables:
 * - $offerings: Collection of CourseOffering objects for the teacher
 * - $offeringId: Currently selected offering ID
 * - $report: Report data array (optional)
 *
 * Dependencies:
 * - Routes: teacher.reports.export-class-performance
 * - Helpers: __(), route(), number_format()
 * - Relationships: CourseOffering->course
 *
 * @package App\Views\Pages\Teacher\Reports
 */
?>
<x-layouts::app :title="__('lms.teacher_reports')">

<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.teacher_reports') }}</h1>

    <!-- Course Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('lms.select_course') }}</h2>
        <form method="GET" action="{{ route('teacher.reports.index') }}" class="flex gap-4">
            <select name="offering_id" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">{{ __('lms.select_a_course') }}</option>
                @foreach($offerings as $offering)
                    <option value="{{ $offering->id }}" {{ $offeringId == $offering->id ? 'selected' : '' }}>
                        {{ $offering->course?->name ?? __('Course') }} - {{ $offering->section_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                {{ __('lms.generate_report') }}
            </button>
        </form>
    </div>

    @if($report)
    <!-- Report Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('lms.class_performance') }}: {{ $report['offering']->course?->name ?? __('Course') }}
            </h2>
            <a href="{{ route('teacher.reports.export-class-performance', $offeringId) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                {{ __('lms.export_csv') }}
            </a>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <p class="text-sm text-blue-600 dark:text-blue-400">{{ __('lms.total_students') }}</p>
                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $report['total_students'] }}</p>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <p class="text-sm text-green-600 dark:text-green-400">{{ __('lms.class_average') }}</p>
                <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ number_format($report['class_average'], 2) }}%</p>
            </div>
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <p class="text-sm text-purple-600 dark:text-purple-400">{{ __('lms.highest_score') }}</p>
                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100">{{ number_format($report['highest_score'], 2) }}%</p>
            </div>
            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                <p class="text-sm text-orange-600 dark:text-orange-400">{{ __('lms.lowest_score') }}</p>
                <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ number_format($report['lowest_score'], 2) }}%</p>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">{{ __('lms.grade_distribution') }}</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($report['grade_distribution'] as $grade => $count)
                    @if($count > 0)
                    <div class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded">
                        <span class="font-medium">{{ $grade }}:</span> {{ $count }}
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Assessment Stats -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">{{ __('lms.assessment_statistics') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.assessment') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.count') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.average') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.min') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.max') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($report['assessment_stats'] as $stat)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $stat['assessment']->title }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $stat['count'] }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ number_format($stat['average'], 2) }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ number_format($stat['min'], 2) }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ number_format($stat['max'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Student Performance -->
        <div>
            <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">{{ __('lms.student_performance') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.student') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.average') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.completed') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('lms.letter_grade') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($report['student_performance'] as $performance)
                        <tr>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $performance['student']->name }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $performance['weighted_average'] ? number_format($performance['weighted_average'], 2) . '%' : 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $performance['completed_assessments'] }}/{{ $performance['total_assessments'] }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                @if($performance['weighted_average'])
                                    @if($performance['weighted_average'] >= 90) A
                                    @elseif($performance['weighted_average'] >= 80) B
                                    @elseif($performance['weighted_average'] >= 70) C
                                    @elseif($performance['weighted_average'] >= 60) D
                                    @else F
                                    @endif
                                @else N/A
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
