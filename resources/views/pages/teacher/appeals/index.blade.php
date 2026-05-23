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
<x-layouts::app :title="__('Grade Appeals')">

<!-- Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Grade Appeals') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Review and manage student grade appeal requests') }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <flux:button href="{{ route('teacher.dashboard') }}" variant="outline">
                {{ __('Back to Dashboard') }}
            </flux:button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="mb-6 grid gap-4 md:grid-cols-4">
    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pending Review') }}</p>
                <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $appeals->where('status', 'pending')->count() + $appeals->where('status', 'under_review')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Approved') }}</p>
                <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $appeals->where('status', 'approved')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Rejected') }}</p>
                <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $appeals->where('status', 'rejected')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l3-3m0 0l3 3m-3-3v6m4-13a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Escalated') }}</p>
                <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $appeals->where('status', 'escalated')->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="mb-6 rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">{{ __('Search Appeals') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by student name, subject, or course...') }}"
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2 focus:border-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">{{ __('Status') }}</label>
            <select name="status" class="w-full rounded-lg border border-neutral-300 px-3 py-2 focus:border-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>{{ __('Under Review') }}</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                <option value="escalated" {{ request('status') === 'escalated' ? 'selected' : '' }}>{{ __('Escalated') }}</option>
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">{{ __('Sort By') }}</label>
            <select name="sort" class="w-full rounded-lg border border-neutral-300 px-3 py-2 focus:border-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100">
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                <option value="student" {{ request('sort') === 'student' ? 'selected' : '' }}>{{ __('Student Name') }}</option>
                <option value="course" {{ request('sort') === 'course' ? 'selected' : '' }}>{{ __('Course') }}</option>
            </select>
        </div>
        <div class="flex gap-2">
            <x-button.submit loading-text="{{ __('Applying...') }}">
                {{ __('Apply Filters') }}
            </x-button.submit>
            <a href="{{ route('teacher.appeals.index') }}" class="px-4 py-2 bg-neutral-200 text-neutral-800 rounded-lg hover:bg-neutral-300 dark:bg-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-600 transition">
                {{ __('Clear') }}
            </a>
        </div>
    </div>
</div>

    <!-- Bulk Actions Bar -->
    @if($appeals->count() > 0)
    <div class="mb-4 rounded-lg border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="select-all" class="rounded border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700">
                <label for="select-all" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Select All') }}</label>
                <span id="selected-count" class="text-sm text-neutral-500 dark:text-neutral-400">0 selected</span>
            </div>
            <div class="flex gap-2" id="bulk-actions" style="display: none;">
                <button type="button" id="bulk-approve" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                    {{ __('Approve Selected') }}
                </button>
                <button type="button" id="bulk-reject" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition">
                    {{ __('Reject Selected') }}
                </button>
                <button type="button" id="bulk-escalate" class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition">
                    {{ __('Escalate Selected') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Appeals List -->
    @if($appeals->count() > 0)
        <div class="rounded-lg border border-neutral-200 bg-white shadow-sm overflow-hidden dark:border-neutral-700 dark:bg-neutral-800">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                    <thead class="bg-neutral-50 dark:bg-neutral-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider w-12">
                                <input type="checkbox" id="header-checkbox" class="rounded border-neutral-300 dark:border-neutral-600">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Student') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Subject') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Course') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Current Grade') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Requested Grade') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Priority') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Submitted') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-neutral-800 divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($appeals as $appeal)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="appeal-checkbox rounded border-neutral-300 dark:border-neutral-600 dark:bg-neutral-700" value="{{ $appeal->id }}">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white text-sm font-medium mr-3">
                                            {{ $appeal->student->initials() }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ $appeal->student?->name ?? __('Unknown') }}</div>
                                            <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $appeal->student->user_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ Str::limit($appeal->subject, 40) }}</div>
                                    @if(strlen($appeal->subject) > 40)
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">{{ Str::limit($appeal->reason, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-neutral-600 dark:text-neutral-400">
                                        {{ Str::limit($appeal->enrollment?->offering?->course?->name ?? $appeal->assessment?->courseOffering?->course?->name ?? '-', 30) }}
                                    </div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-500 mt-1">
                                        {{ $appeal->enrollment?->offering?->section_name ?? $appeal->assessment?->courseOffering?->section_name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-neutral-900 dark:text-white">{{ $appeal->current_grade ?? '-' }}%</div>
                                    @if($appeal->grade && $appeal->grade->assessment)
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ $appeal->grade->assessment->title }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-green-600 dark:text-green-400">{{ $appeal->requested_grade ?? '-' }}%</div>
                                    @if($appeal->current_grade && $appeal->requested_grade)
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                            +{{ $appeal->requested_grade - $appeal->current_grade }}%
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['color' => 'yellow', 'icon' => 'clock'],
                                            'under_review' => ['color' => 'blue', 'icon' => 'magnifying-glass'],
                                            'approved' => ['color' => 'green', 'icon' => 'check-circle'],
                                            'rejected' => ['color' => 'red', 'icon' => 'x-circle'],
                                            'escalated' => ['color' => 'purple', 'icon' => 'arrow-up-circle'],
                                        ];
                                        $config = $statusConfig[$appeal->status] ?? ['color' => 'gray', 'icon' => 'question-mark-circle'];
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-{{ $config['color'] }}-100 dark:bg-{{ $config['color'] }}-900">
                                            <svg class="h-3 w-3 text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($config['icon'] === 'clock')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @elseif($config['icon'] === 'magnifying-glass')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                                                @elseif($config['icon'] === 'check-circle')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @elseif($config['icon'] === 'x-circle')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                @elseif($config['icon'] === 'arrow-up-circle')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"></path>
                                                @endif
                                            </svg>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 dark:bg-{{ $config['color'] }}-900 dark:text-{{ $config['color'] }}-200">
                                            {{ __( 'lms.' . $appeal->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @php
                                        $daysSince = now()->diffInDays($appeal->created_at);
                                        $priority = $daysSince > 7 ? 'high' : ($daysSince > 3 ? 'medium' : 'low');
                                        $priorityConfig = [
                                            'high' => ['color' => 'red', 'label' => 'High'],
                                            'medium' => ['color' => 'yellow', 'label' => 'Medium'],
                                            'low' => ['color' => 'green', 'label' => 'Low'],
                                        ];
                                        $pConfig = $priorityConfig[$priority];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $pConfig['color'] }}-100 text-{{ $pConfig['color'] }}-800 dark:bg-{{ $pConfig['color'] }}-900 dark:text-{{ $pConfig['color'] }}-200">
                                        {{ $pConfig['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                    <div>{{ $appeal->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs">{{ $appeal->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('teacher.appeals.show', $appeal) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                            {{ __('Review') }}
                                        </a>
                                        @if(in_array($appeal->status, ['pending', 'under_review']))
                                        <div class="relative">
                                            <select onchange="quickAction(this, {{ $appeal->id }})" class="text-xs border border-neutral-300 rounded px-2 py-1 focus:border-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700">
                                                <option value="">{{ __('Quick Action') }}</option>
                                                <option value="review">{{ __('Mark as Under Review') }}</option>
                                                <option value="approve">{{ __('Approve') }}</option>
                                                <option value="reject">{{ __('Reject') }}</option>
                                                <option value="escalate">{{ __('Escalate') }}</option>
                                            </select>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-neutral-200 px-4 py-3 dark:border-neutral-700">
            {{ $appeals->links() }}
        </div>
    @else
        <div class="rounded-lg border border-neutral-200 bg-white p-12 text-center shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="mb-4">
                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-2">{{ __('No grade appeals found') }}</h3>
            <p class="text-neutral-500 dark:text-neutral-400 mb-4">{{ __('Grade appeals from your students will appear here for review') }}</p>
            <flux:button href="{{ route('teacher.dashboard') }}" variant="primary">
                {{ __('Back to Dashboard') }}
            </flux:button>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const headerCheckbox = document.getElementById('header-checkbox');
    const appealCheckboxes = document.querySelectorAll('.appeal-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const bulkApproveBtn = document.getElementById('bulk-approve');
    const bulkRejectBtn = document.getElementById('bulk-reject');
    const bulkEscalateBtn = document.getElementById('bulk-escalate');

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.appeal-checkbox:checked');
        const count = checkedBoxes.length;

        selectedCount.textContent = count + ' selected';

        if (count > 0) {
            bulkActions.style.display = 'block';
        } else {
            bulkActions.style.display = 'none';
        }
    }

    function toggleAllCheckboxes(checked) {
        appealCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateBulkActions();
    }

    selectAllCheckbox.addEventListener('change', function() {
        toggleAllCheckboxes(this.checked);
    });

    headerCheckbox.addEventListener('change', function() {
        toggleAllCheckboxes(this.checked);
        selectAllCheckbox.checked = this.checked;
    });

    appealCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(appealCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(appealCheckboxes).some(cb => cb.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;

            headerCheckbox.checked = allChecked;
            headerCheckbox.indeterminate = someChecked && !allChecked;

            updateBulkActions();
        });
    });

    // Bulk actions
    bulkApproveBtn.addEventListener('click', function() {
        performBulkAction('approve');
    });

    bulkRejectBtn.addEventListener('click', function() {
        performBulkAction('reject');
    });

    bulkEscalateBtn.addEventListener('click', function() {
        performBulkAction('escalate');
    });

    function performBulkAction(action) {
        const selectedIds = Array.from(document.querySelectorAll('.appeal-checkbox:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) return;

        if (confirm(`Are you sure you want to ${action} ${selectedIds.length} appeal(s)?`)) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/teacher/appeals/bulk-${action}`;

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            // Add appeal IDs
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'appeal_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    }
});

function quickAction(select, appealId) {
    const action = select.value;
    if (!action) return;

    const actions = {
        'review': 'Mark as under review?',
        'approve': 'Approve this appeal?',
        'reject': 'Reject this appeal?',
        'escalate': 'Escalate this appeal to admin?'
    };

    if (confirm(actions[action])) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/teacher/appeals/${appealId}/${action}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        document.body.appendChild(form);
        form.submit();
    } else {
        select.value = '';
    }
}
</script>
</x-layouts::app>
