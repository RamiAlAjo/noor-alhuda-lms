<?php
/**
 * Page: Student Grade Appeals Index
 *
 * Purpose: Display a list of student's grade appeals with filtering capabilities.
 * Allows students to view, create, edit, and withdraw their grade appeals.
 *
 * Route: student.appeals.index (GET)
 *
 * Controller: App\Http\Controllers\Student\GradeAppealController@index
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Header with New Appeal button
 * - Filter form with status and course dropdowns
 * - Data table displaying appeals with subject, course, grades, status, submission date
 * - Action links for view, edit, and withdraw (for pending appeals)
 * - Pagination links
 * - Empty state when no appeals exist
 *
 * Required Data variables:
 * - $appeals: Collection of GradeAppeal objects (paginated)
 * - $courses: Collection of Course objects for filtering
 *
 * Dependencies:
 * - Routes: student.appeals.create, student.appeals.show, student.appeals.edit, student.appeals.withdraw
 * - Helpers: __(), route(), request()
 * - Relationships: GradeAppeal->enrollment->offering->course, GradeAppeal->assessment->courseOffering->course
 * - Methods: GradeAppeal->isPending()
 * - Flux UI components: flux:icon
 *
 * @package App\Views\Pages\Student\Appeals
 */
?>
<x-layouts::app :title="__('lms.my_grade_appeals')">

<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.my_grade_appeals') }}</h1>
        <a href="{{ route('student.appeals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <flux:icon name="plus" class="w-5 h-5 mr-2" />
            {{ __('lms.new_appeal') }}
        </a>
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
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.course') }}</label>
                <select name="course" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('lms.all_courses') }}</option>
                    @foreach($courses as $course)
                        @if($course && $course->id)
                            <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <x-button.submit loading-text="Filtering..." variant="secondary">
                    {{ __('lms.filter') }}
                </x-button.submit>
            </div>
        </form>
    </div>

    <!-- Appeals List -->
    @if($appeals->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.subject') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.course') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.current_grade') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.requested_grade') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.submitted') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($appeals as $appeal)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appeal->subject }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ $appeal->enrollment?->offering?->course?->name ?? $appeal->assessment?->courseOffering?->course?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $appeal->current_grade ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $appeal->requested_grade ?? '-' }}</div>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $appeal->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('student.appeals.show', $appeal) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                    {{ __('lms.view') }}
                                </a>
                                @if($appeal->isPending())
                                    <a href="{{ route('student.appeals.edit', $appeal) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300 mr-3">
                                        {{ __('lms.edit') }}
                                    </a>
                                    <form action="{{ route('student.appeals.withdraw', $appeal) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('lms.confirm_withdraw_appeal') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            {{ __('lms.withdraw') }}
                                        </button>
                                    </form>
                                @endif
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
            <p class="text-gray-600 dark:text-gray-300 mb-4">{{ __('lms.no_appeals_description') }}</p>
            <a href="{{ route('student.appeals.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <flux:icon name="plus" class="w-5 h-5 mr-2" />
                {{ __('lms.submit_appeal') }}
            </a>
        </div>
    @endif
</div>
</x-layouts::app>
