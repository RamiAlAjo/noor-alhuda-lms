{{--
    =============================================================================
    ADMIN GRADE APPEALS INDEX VIEW
    =============================================================================

    Purpose: List and manage all grade appeals from students with filtering and stats.

    Route: admin.appeals.index
    Controller: Admin\GradeAppealController@index

    Components:
    - Header with Export CSV button
    - Filters: Status, From Date, To Date
    - Stats cards: Pending, Under Review, Approved, Rejected, Escalated counts
    - Appeals table: ID, Student, Subject, Course, Grades (current→requested), Status, Date, Actions
    - Status color badges
    - Pagination
    - Empty state when no appeals

    Required Data:
    - $appeals: Paginated collection of GradeAppeal models
    - $statuses: Available status options

    Dependencies:
    - route('admin.appeals.export') - Export to CSV
    - route('admin.appeals.show', $appeal) - View appeal details
    - \App\Models\GradeAppeal::pending() - Scope for pending
    - \App\Models\GradeAppeal::underReview() - Scope for under review
    - \App\Models\GradeAppeal::escalated() - Scope for escalated
    - $appeal->student->initials() - Student initials
    - $appeal->student->name - Student name
    - $appeal->enrollment->offering->course->name - Course name

    =============================================================================
--}}
<x-slot name="title">{{ __('lms.grade_appeals') }}</x-slot>

<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.grade_appeals') }}</h1>
        <a href="{{ route('admin.appeals.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <flux:icon name="arrow-down-tray" class="w-5 h-5 mr-2" />
            {{ __('lms.export_csv') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.status') }}</label>
                <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('lms.all_statuses') }}</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.from_date') }}</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.to_date') }}</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex items-end space-x-2">
                <x-button.submit loading-text="{{ __('Filtering...') }}">
                    {{ __('lms.filter') }}
                </x-button.submit>
                <a href="{{ route('admin.appeals.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('lms.clear') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        @php
            $statusCounts = [
                'pending' => \App\Models\GradeAppeal::pending()->count(),
                'under_review' => \App\Models\GradeAppeal::underReview()->count(),
                'approved' => \App\Models\GradeAppeal::where('status', 'approved')->count(),
                'rejected' => \App\Models\GradeAppeal::where('status', 'rejected')->count(),
                'escalated' => \App\Models\GradeAppeal::escalated()->count(),
            ];
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <flux:icon name="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('lms.pending') }}</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $statusCounts['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <flux:icon name="eye" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('lms.under_review') }}</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $statusCounts['under_review'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <flux:icon name="check" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('lms.approved') }}</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $statusCounts['approved'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                    <flux:icon name="x-mark" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('lms.rejected') }}</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $statusCounts['rejected'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <flux:icon name="arrow-up" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('lms.escalated') }}</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-white">{{ $statusCounts['escalated'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appeals List -->
    @if($appeals->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.id') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.student') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.subject') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.course') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.grades') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.submitted') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($appeals as $appeal)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ $appeal->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium mr-2">
                                        {{ $appeal->student?->initials() ?? '?' }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appeal->student?->name ?? __('Unknown') }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $appeal->student?->user_id ?? '-' }}</div>
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
                                <div class="text-sm">
                                    <span class="text-gray-900 dark:text-white">{{ $appeal->current_grade ?? '-' }}%</span>
                                    <span class="text-gray-500 dark:text-gray-400">→</span>
                                    <span class="text-blue-600 dark:text-blue-400">{{ $appeal->requested_grade ?? '-' }}%</span>
                                </div>
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
                                <a href="{{ route('admin.appeals.show', $appeal) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('lms.view') }}
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
            <p class="text-gray-500 dark:text-gray-400">{{ __('lms.no_appeals_admin_description') }}</p>
        </div>
    @endif
</div>
