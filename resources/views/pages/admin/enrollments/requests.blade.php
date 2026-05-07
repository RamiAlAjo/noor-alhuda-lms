{{--
    =============================================================================
    ADMIN PENDING ENROLLMENT REQUESTS VIEW
    =============================================================================

    Purpose: Review and approve/reject pending student enrollment requests.

    Route: admin.enrollments.requests
    Controller: Admin\EnrollmentController@requests

    Components:
    - Header with back button
    - Bulk actions bar (Select All, Bulk Approve, Bulk Reject)
    - Requests table with: Checkbox, Student info, Course, Section, Date, Actions
    - Individual Approve/Reject buttons for each request
    - JavaScript for select all functionality
    - Pagination
    - Empty state when no pending requests

    Required Data:
    - $enrollments: Paginated collection of pending Enrollment models

    Dependencies:
    - route('admin.enrollments.index') - Back to all enrollments
    - route('admin.enrollments.bulk-approve') - Bulk approve endpoint
    - route('admin.enrollments.bulk-reject') - Bulk reject endpoint
    - route('admin.enrollments.approve', $enrollment) - Single approve
    - route('admin.enrollments.reject', $enrollment) - Single reject
    - $enrollment->student->full_name - Student's full name
    - $enrollment->student->user_id - Student's user ID
    - $enrollment->courseSection->course->name - Course name

    =============================================================================
--}}
<x-layouts::app :title="__('Pending Enrollment Requests')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Pending Enrollment Requests') }}</h1>
            <flux:button :href="route('admin.enrollments.index')" variant="ghost">
                {{ __('Back to All') }}
            </flux:button>
        </div>

        <!-- Bulk Actions -->
        <div class="rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <form method="POST" action="{{ route('admin.enrollments.bulk-approve') }}" class="flex items-center gap-4">
                @csrf
                <flux:checkbox name="select_all" id="selectAll" :label="__('Select All')" />
                <flux:button type="submit" variant="primary">{{ __('Bulk Approve') }}</flux:button>
                <flux:button type="submit" formaction="{{ route('admin.enrollments.bulk-reject') }}" variant="danger">{{ __('Bulk Reject') }}</flux:button>
            </form>
        </div>

        <!-- Requests Table -->
        <div class="rounded-xl border border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('Select') }}</th>
                            <th class="px-4 py-3">{{ __('Student') }}</th>
                            <th class="px-4 py-3">{{ __('Course') }}</th>
                            <th class="px-4 py-3">{{ __('Section') }}</th>
                            <th class="px-4 py-3">{{ __('Requested Date') }}</th>
                            <th class="px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($enrollments as $enrollment)
                        <tr>
                            <td class="px-4 py-3">
                                <input type="checkbox" name="enrollments[]" value="{{ $enrollment->id }}" class="enrollment-checkbox rounded border-neutral-300">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ $enrollment->student?->full_name ?? __('Unknown') }}</div>
                                <div class="text-xs text-neutral-500">{{ $enrollment->student?->user_id ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $enrollment->courseSection?->course?->name ?? __('Unknown Course') }}</td>
                            <td class="px-4 py-3">{{ __('Section') }} {{ $enrollment->courseSection?->section_name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $enrollment->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('admin.enrollments.approve', $enrollment) }}">
                                        @csrf
                                        <flux:button type="submit" size="sm" variant="primary">{{ __('Approve') }}</flux:button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.enrollments.reject', $enrollment) }}">
                                        @csrf
                                        <flux:button type="submit" size="sm" variant="danger">{{ __('Reject') }}</flux:button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                {{ __('No pending requests') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $enrollments->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.enrollment-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
    @endpush
</x-layouts::app>
