{{--
    =============================================================================
    TEACHER ATTENDANCE CALENDAR VIEW
    =============================================================================

    Purpose: Display attendance data in a calendar format for a course section.

    Route: teacher.courses.attendance.calendar
    Controller: Teacher\CourseController@attendanceCalendar

    Components:
    - Month navigation (previous/next month)
    - Calendar grid showing attendance for each day
    - Student attendance status for each day
    - Legend showing attendance status colors
    - Summary statistics

    Required Data:
    - $section: CourseSection model
    - $attendanceRecords: Grouped attendance records by date and student
    - $currentMonth: Start of current month
    - $endOfMonth: End of current month

    =============================================================================
--}}
<x-layouts::app :title="__('Attendance Calendar')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Attendance Calendar') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $section->course?->name ?? __('Course') }} - {{ __('Section') }} {{ $section->sectionNumber }}</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:button :href="route('teacher.courses.attendance', $section)" variant="outline">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                {{ __('Daily View') }}
            </flux:button>
            <flux:button :href="route('teacher.courses.show', $section)" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                {{ __('Back to Course') }}
            </flux:button>
        </div>
    </div>

    <!-- Month Navigation -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <button type="button" class="rounded-lg border border-neutral-300 p-2 hover:bg-neutral-50 dark:border-neutral-600 dark:hover:bg-neutral-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                {{ $currentMonth->format('F Y') }}
            </h2>
            <button type="button" class="rounded-lg border border-neutral-300 p-2 hover:bg-neutral-50 dark:border-neutral-600 dark:hover:bg-neutral-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Legend -->
        <div class="flex items-center gap-4 text-sm">
            <div class="text-xs">
                <span class="font-medium">{{ __('Format') }}:</span>
                <span class="text-green-600">{{ __('Present') }}</span> /
                <span class="text-red-600">{{ __('Absent') }}</span> /
                <span class="text-yellow-600">{{ __('Excused') }}</span> /
                <span class="text-blue-600">{{ __('Late') }}</span>
            </div>
            <div class="text-xs">
                <span class="font-medium">{{ __('Rate') }}:</span>
                <span class="bg-green-100 text-green-800 px-1 rounded text-xs">80%+</span>
                <span class="bg-yellow-100 text-yellow-800 px-1 rounded text-xs">60-79%</span>
                <span class="bg-red-100 text-red-800 px-1 rounded text-xs"><60%</span>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Monthly Attendance Summary') }}</h3>
            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">{{ __('Daily attendance counts and rates for the month') }}</p>
        </div>

        <div class="p-6">
            <!-- Days of week header -->
            <div class="mb-4 grid grid-cols-7 gap-1">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="p-2 text-center text-sm font-medium text-neutral-500 dark:text-neutral-400">
                    {{ __($day) }}
                </div>
                @endforeach
            </div>

            <!-- Calendar days -->
            <div class="grid grid-cols-7 gap-1">
                @foreach($calendarData as $dayData)
                    @php
                        $daySummary = $attendanceSummaries->get($dayData['dateString'], collect());
                        $presentCount = $daySummary->where('status', 'present')->sum('count');
                        $absentCount = $daySummary->where('status', 'absent')->sum('count');
                        $excusedCount = $daySummary->where('status', 'excused')->sum('count');
                        $lateCount = $daySummary->where('status', 'late')->sum('count');
                        $totalMarked = $presentCount + $absentCount + $excusedCount + $lateCount;
                        $dayRate = $enrolledCount > 0 ? round(($presentCount / $enrolledCount) * 100) : 0;
                    @endphp

                    <div class="min-h-[100px] rounded-lg border border-neutral-200 p-2 dark:border-neutral-700 {{ $dayData['isCurrentMonth'] ? 'bg-white dark:bg-neutral-800' : 'bg-neutral-50 dark:bg-neutral-900' }} {{ $dayData['isToday'] ? 'ring-2 ring-indigo-500' : '' }}">
                        <div class="mb-1 text-sm font-medium {{ $dayData['isCurrentMonth'] ? 'text-neutral-900 dark:text-neutral-100' : 'text-neutral-400 dark:text-neutral-500' }}">
                            {{ $dayData['day'] }}
                        </div>

                        @if($dayData['isCurrentMonth'])
                            @if($totalMarked > 0)
                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-green-600 font-medium">{{ $presentCount }}</span>
                                        <span class="text-red-600">{{ $absentCount }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-yellow-600">{{ $excusedCount }}</span>
                                        <span class="text-blue-600">{{ $lateCount }}</span>
                                    </div>
                                    <div class="text-center mt-1">
                                        <span class="inline-block px-1 py-0.5 rounded text-xs font-medium
                                            @if($dayRate >= 80) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($dayRate >= 60) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @endif">
                                            {{ $dayRate }}%
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="text-xs text-neutral-400 dark:text-neutral-500 text-center">
                                    {{ __('No data') }}
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    @if($enrolledCount > 0)
    <div class="mt-6 grid gap-4 md:grid-cols-4">
        @php
            $totalDays = $currentMonth->daysInMonth;
            $totalPossibleAttendances = $enrolledCount * $totalDays;

            // Calculate totals from attendance summaries
            $presentCount = 0;
            $absentCount = 0;
            $excusedCount = 0;
            $lateCount = 0;

            foreach ($attendanceSummaries as $date => $daySummary) {
                $presentCount += $daySummary->where('status', 'present')->sum('count');
                $absentCount += $daySummary->where('status', 'absent')->sum('count');
                $excusedCount += $daySummary->where('status', 'excused')->sum('count');
                $lateCount += $daySummary->where('status', 'late')->sum('count');
            }

            $attendanceRate = $totalPossibleAttendances > 0 ? round(($presentCount / $totalPossibleAttendances) * 100, 1) : 0;
        @endphp

        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Present Days') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $presentCount }}</p>
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
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Absent Days') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $absentCount }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Excused Days') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $excusedCount }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Attendance Rate') }}</p>
                    <p class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $attendanceRate }}%</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-layouts::app>