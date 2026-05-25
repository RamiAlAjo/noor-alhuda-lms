<x-layouts::app :title="__('Students')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Students') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $section->course?->name ?? __('Course') }} - {{ __('Section') }} {{ $section->section_name }}</p>
        </div>
        <flux:button :href="route('teacher.courses.show', $section)" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Course') }}
        </flux:button>
    </div>

    <!-- Search & Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <flux:input
                    type="text"
                    id="studentSearch"
                    placeholder="{{ __('Search students by name or email...') }}"
                    class="w-full"
                />
            </div>

            <!-- Filters -->
            <div class="flex gap-2">
                <select id="statusFilter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="approved">{{ __('Approved') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="rejected">{{ __('Rejected') }}</option>
                </select>

                <select id="sortBy" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                    <option value="name">{{ __('Sort by Name') }}</option>
                    <option value="email">{{ __('Sort by Email') }}</option>
                    <option value="enrolled_at">{{ __('Sort by Enrollment Date') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Bulk Actions (when students are selected) -->
    <div id="bulk-actions" class="mb-6 hidden rounded-xl border border-neutral-200 bg-blue-50 p-4 dark:border-neutral-700 dark:bg-blue-900/20">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                <span id="selected-count">0</span> {{ __('students selected') }}
            </span>
            <div class="flex gap-2">
                <flux:button size="sm" variant="outline" onclick="clearSelection()">
                    {{ __('Clear Selection') }}
                </flux:button>
                <flux:button size="sm" variant="primary" onclick="bulkMessage()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    {{ __('Message Selected') }}
                </flux:button>
                <flux:button size="sm" variant="outline" onclick="bulkViewGrades()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('View Grades') }}
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-emerald-900/20 dark:to-teal-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Enrolled') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100" id="totalCount">{{ $enrollments->count() }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Active') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100" id="activeCount">{{ $enrollments->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-5 shadow-sm transition-all hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-amber-900/20 dark:to-orange-900/20"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Pending') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $section->enrollments->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <flux:input
                        type="text"
                        id="studentSearch"
                        placeholder="{{ __('Search students by name or email...') }}"
                        :value="request('search')"
                        class="w-full"
                    />
                </div>

                <!-- Filters -->
                <div class="flex gap-2">
                    <select id="statusFilter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                    </select>

                    <select id="sortBy" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                        <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>{{ __('Sort by Name') }}</option>
                        <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>{{ __('Sort by Email') }}</option>
                        <option value="enrolled_at" {{ request('sort') === 'enrolled_at' ? 'selected' : '' }}>{{ __('Sort by Enrollment Date') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Enrolled Students') }}</h2>
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                    {{ $enrollments->count() }} {{ __('students') }}
                </span>
            </div>
        </div>
        @if($enrollments->isEmpty())
            <div class="p-12 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-neutral-100 mx-auto dark:bg-neutral-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No students enrolled') }}</h3>
                <p class="mt-2 text-neutral-600 dark:text-neutral-400">{{ __('Students will appear here once they enroll in this course') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900">
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                <input type="checkbox" id="select-all" class="rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-600">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                {{ __('Student') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                {{ __('Student ID') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                {{ __('Email') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                {{ __('Enrollment Date') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach($enrollments as $enrollment)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="student-checkbox rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500 dark:border-neutral-600" value="{{ $enrollment->student_id }}" onchange="updateBulkActions()">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-bold text-white">
                                            {{ substr($enrollment->student->profile?->first_name ?? 'S', 0, 1) }}{{ substr($enrollment->student->profile?->last_name ?? '', 0, 1) }}
                                        </div>
                                        <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $enrollment->student?->fullName ?? __('Unknown') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $enrollment->student?->id ?? '-' }}</td>
                                <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $enrollment->student?->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-neutral-600 dark:text-neutral-400">{{ $enrollment->created_at->format('Y-m-d') }}</td>
                                 <td class="px-6 py-4 text-right">
                                     <flux:button size="sm" variant="subtle" :href="route('teacher.students.show', $enrollment->student_id)">
                                         {{ __('View Profile') }}
                                     </flux:button>
                                 </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        // Select all functionality
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });

        // Update bulk actions visibility
        function updateBulkActions() {
            const checkboxes = document.querySelectorAll('.student-checkbox:checked');
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            const selectAll = document.getElementById('select-all');

            if (checkboxes.length > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = checkboxes.length;
            } else {
                bulkActions.classList.add('hidden');
                selectAll.checked = false;
            }
        }

        // Clear selection
        function clearSelection() {
            document.querySelectorAll('.student-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateBulkActions();
        }

        // Bulk message selected students
        function bulkMessage() {
            const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);

            if (selectedStudents.length === 0) {
                alert('{{ __("Please select at least one student") }}');
                return;
            }

            // Redirect to messages with selected students
            const url = '{{ route("messages.index") }}?students=' + selectedStudents.join(',');
            window.location.href = url;
        }

        // Bulk view grades for selected students
        function bulkViewGrades() {
            const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);

            if (selectedStudents.length === 0) {
                alert('{{ __("Please select at least one student") }}');
                return;
            }

            if (selectedStudents.length === 1) {
                // Single student - go to individual grades
                window.location.href = '{{ route("teacher.courses.grades", $section) }}?student=' + selectedStudents[0];
            } else {
                // Multiple students - show bulk grades view
                window.location.href = '{{ route("teacher.courses.grades", $section) }}?students=' + selectedStudents.join(',');
            }
        }
    </script>
</x-layouts::app>
