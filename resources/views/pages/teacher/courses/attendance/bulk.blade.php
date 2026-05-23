<x-layouts::app :title="__('Bulk Attendance')">
    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 rounded-xl p-3">
                        <flux:icon.clipboard-document-list class="w-8 h-8" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            {{ __('Bulk Attendance') }}
                        </h1>
                        <p class="mt-1 text-indigo-100">
                            {{ __('Mark attendance for multiple dates') }}
                        </p>
                    </div>
                </div>
                <flux:button :href="route('teacher.courses.attendance', $section)" variant="ghost" class="!text-white hover:!bg-white/20">
                    {{ __('Back to Attendance') }}
                    <flux:icon.arrow-left class="w-4 h-4 ml-2" />
                </flux:button>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <flux:icon.information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                <div>
                    <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ __('How to use bulk attendance:') }}</h3>
                    <ul class="mt-2 text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>• {{ __('Select the dates you want to mark attendance for') }}</li>
                        <li>• {{ __('Choose attendance status for each student on each date') }}</li>
                        <li>• {{ __('Use "Mark All Present/Absent" buttons for quick marking') }}</li>
                        <li>• {{ __('Review and save all attendance data at once') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <form action="{{ route('teacher.courses.attendance.bulk.store', $section) }}" method="POST" id="bulkAttendanceForm">
            @csrf

            <!-- Date Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Select Dates') }}</h2>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Quick Date Selection -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Quick Select') }}</h3>
                        <div class="space-y-2">
                            <button type="button" class="quick-date-btn w-full text-left px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700" data-dates="today">
                                {{ __('Today') }}
                            </button>
                            <button type="button" class="quick-date-btn w-full text-left px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700" data-dates="this-week">
                                {{ __('This Week') }}
                            </button>
                            <button type="button" class="quick-date-btn w-full text-left px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700" data-dates="last-week">
                                {{ __('Last Week') }}
                            </button>
                        </div>
                    </div>

                    <!-- Custom Date Selection -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Add Custom Date') }}</h3>
                        <div class="space-y-2">
                            <flux:input type="date" id="customDate" class="w-full" />
                            <flux:button type="button" id="addDateBtn" variant="outline" size="sm" class="w-full">
                                {{ __('Add Date') }}
                            </flux:button>
                        </div>
                    </div>

                    <!-- Selected Dates -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Selected Dates') }}</h3>
                        <div id="selectedDates" class="space-y-1 min-h-20 border border-gray-200 dark:border-gray-600 rounded-md p-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No dates selected') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs for selected dates -->
                <div id="dateInputs"></div>
            </div>

            @if($section->enrollments->where('status', 'approved')->count() > 0)
            <!-- Attendance Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Mark Attendance') }}</h2>
                        <div class="flex items-center gap-2">
                            <button type="button" id="markAllPresent" class="px-3 py-1 text-sm bg-green-600 text-white rounded-md hover:bg-green-700">
                                {{ __('Mark All Present') }}
                            </button>
                            <button type="button" id="markAllAbsent" class="px-3 py-1 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">
                                {{ __('Mark All Absent') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Student') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider" id="dateHeaders">
                                    <!-- Date headers will be inserted here by JavaScript -->
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($section->enrollments->where('status', 'approved') as $enrollment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                    {{ strtoupper(substr($enrollment->student->first_name ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $enrollment->student->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $enrollment->student->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4" id="attendanceCells_{{ $enrollment->student_id }}">
                                    <!-- Attendance cells will be inserted here by JavaScript -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Review attendance data before saving') }}
                        </p>
                        <x-button.submit loading-text="Saving..." variant="primary" id="saveAttendanceBtn" disabled>
                            Save Attendance
                        </x-button.submit>
                    </div>
                </div>
            </div>
            @else
            <!-- No students message -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <flux:icon.users class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ __('No enrolled students') }}
                </h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ __('There are no approved enrollments for this course section.') }}
                </p>
            </div>
            @endif
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let selectedDates = [];

            // Quick date selection
            document.querySelectorAll('.quick-date-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.dates;
                    let dates = [];

                    switch(type) {
                        case 'today':
                            dates = [new Date().toISOString().split('T')[0]];
                            break;
                        case 'this-week':
                            const today = new Date();
                            const startOfWeek = new Date(today);
                            startOfWeek.setDate(today.getDate() - today.getDay());
                            dates = [];
                            for (let i = 0; i < 7; i++) {
                                const date = new Date(startOfWeek);
                                date.setDate(startOfWeek.getDate() + i);
                                dates.push(date.toISOString().split('T')[0]);
                            }
                            break;
                        case 'last-week':
                            const lastWeek = new Date();
                            lastWeek.setDate(lastWeek.getDate() - 7);
                            const startOfLastWeek = new Date(lastWeek);
                            startOfLastWeek.setDate(lastWeek.getDate() - lastWeek.getDay());
                            dates = [];
                            for (let i = 0; i < 7; i++) {
                                const date = new Date(startOfLastWeek);
                                date.setDate(startOfLastWeek.getDate() + i);
                                dates.push(date.toISOString().split('T')[0]);
                            }
                            break;
                    }

                    dates.forEach(date => addDate(date));
                });
            });

            // Add custom date
            document.getElementById('addDateBtn').addEventListener('click', function() {
                const dateInput = document.getElementById('customDate');
                const date = dateInput.value;
                if (date) {
                    addDate(date);
                    dateInput.value = '';
                }
            });

            // Enter key for custom date
            document.getElementById('customDate').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('addDateBtn').click();
                }
            });

            function addDate(date) {
                if (!selectedDates.includes(date)) {
                    selectedDates.push(date);
                    updateDateDisplay();
                    updateAttendanceTable();
                }
            }

            function removeDate(date) {
                selectedDates = selectedDates.filter(d => d !== date);
                updateDateDisplay();
                updateAttendanceTable();
            }

            function updateDateDisplay() {
                const container = document.getElementById('selectedDates');
                const inputsContainer = document.getElementById('dateInputs');

                if (selectedDates.length === 0) {
                    container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400">{{ __("No dates selected") }}</p>';
                    inputsContainer.innerHTML = '';
                    return;
                }

                container.innerHTML = '';
                inputsContainer.innerHTML = '';

                selectedDates.sort().forEach(date => {
                    // Date badge
                    const badge = document.createElement('div');
                    badge.className = 'flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 px-2 py-1 rounded-md text-sm';
                    badge.innerHTML = `
                        <span>${formatDate(date)}</span>
                        <button type="button" class="remove-date text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200" data-date="${date}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    container.appendChild(badge);

                    // Hidden input
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'dates[]';
                    input.value = date;
                    inputsContainer.appendChild(input);
                });

                // Add event listeners to remove buttons
                document.querySelectorAll('.remove-date').forEach(btn => {
                    btn.addEventListener('click', function() {
                        removeDate(this.dataset.date);
                    });
                });
            }

            function updateAttendanceTable() {
                const headersContainer = document.getElementById('dateHeaders');
                headersContainer.innerHTML = '';

                if (selectedDates.length === 0) {
                    document.getElementById('saveAttendanceBtn').disabled = true;
                    return;
                }

                document.getElementById('saveAttendanceBtn').disabled = false;

                // Add date headers
                selectedDates.forEach(date => {
                    const th = document.createElement('th');
                    th.className = 'px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider';
                    th.textContent = formatDateShort(date);
                    headersContainer.appendChild(th);
                });

                // Add attendance cells for each student
                @foreach($section->enrollments->where('status', 'approved') as $enrollment)
                const studentCells = document.getElementById('attendanceCells_{{ $enrollment->student_id }}');
                studentCells.innerHTML = '';

                selectedDates.forEach(date => {
                    const cell = document.createElement('td');
                    cell.className = 'px-4 py-3 text-center';
                    cell.innerHTML = `
                        <select name="attendance[${date}][{{ $enrollment->student_id }}]" class="attendance-select rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                            <option value="present">{{ __('Present') }}</option>
                            <option value="absent">{{ __('Absent') }}</option>
                            <option value="late">{{ __('Late') }}</option>
                            <option value="excused">{{ __('Excused') }}</option>
                        </select>
                    `;
                    studentCells.appendChild(cell);
                });
                @endforeach
            }

            // Mark all buttons
            document.getElementById('markAllPresent').addEventListener('click', function() {
                document.querySelectorAll('.attendance-select').forEach(select => {
                    select.value = 'present';
                });
            });

            document.getElementById('markAllAbsent').addEventListener('click', function() {
                document.querySelectorAll('.attendance-select').forEach(select => {
                    select.value = 'absent';
                });
            });

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            function formatDateShort(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric'
                });
            }
        });
    </script>
</x-layouts::app>