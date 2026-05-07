<?php
/**
 * Teacher Grade Appeals - Index Page
 *
 * Purpose: View and manage grade appeals submitted by students for teacher's courses
 * Route: teacher.appeals.index (GET)
 * Controller: App\Http\Controllers\Teacher\GradeAppealController@index
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Filter form: Filter by status
 * - Appeals table: List of appeals with student, subject, course, grades, status
 * - Status badges: Color-coded status indicators
 * - Pagination: Paginated results
 *
 * Required Data Variables:
 * - $appeals: Paginated collection of GradeAppeal models
 *
 * Dependencies:
 * - Routes: teacher.appeals.show
 * - Models: GradeAppeal, User, Enrollment, CourseOffering, Course, Assessment
 * - Helpers: __(), route(), Str::limit(), request()
 * - Constants: GradeAppeal::STATUS_*
 */
?>
<x-layouts::app :title="__('lms.grade_appeals')">

<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.grade_appeals') }}</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.status') }}</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('lms.all_statuses') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('lms.pending') }}</option>
                    <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>{{ __('lms.under_review') }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('lms.approved') }}</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('lms.rejected') }}</option>
                    <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>{{ __('lms.escalated') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    {{ __('lms.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Appeals List -->
    @if($appeals->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.student') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.subject') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.course') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.current_grade') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.requested_grade') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.submitted') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($appeals as $appeal)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium mr-2">
                                        {{ $appeal->student->initials() }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appeal->student?->name ?? __('Unknown') }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $appeal->student->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($appeal->subject, 30) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($appeal->enrollment?->offering?->course?->name ?? $appeal->assessment?->courseOffering?->course?->name ?? '-', 25) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $appeal->current_grade ?? '-' }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $appeal->requested_grade ?? '-' }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'under_review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'escalated' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$appeal->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ __( 'lms.' . $appeal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $appeal->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('teacher.appeals.show', $appeal) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('lms.review') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $appeals->links() }}
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
            <flux:icon name="document-text" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('lms.no_appeals') }}</h3>
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.no_appeals_teacher_description') }}</p>
        </div>
    @endif
</div>
</x-layouts::app>
