<x-layouts::app :title="__('Course Offerings')">
    <!-- Header -->
    <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-8 text-white shadow-2xl dark:from-indigo-900 dark:via-purple-900 dark:to-pink-900">
        <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-16 h-48 w-48 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-white">{{ __('Course Offerings') }}</h1>
                    <p class="mt-1 text-lg font-medium text-indigo-100 dark:text-indigo-200">{{ __('Manage course sections and teacher assignments by semester') }}</p>
                </div>
            </div>
            <div class="hidden items-center gap-6 md:flex">
                @php
                    $totalOfferings = $semesters->sum(fn($s) => $s->offerings->count());
                    $totalTeachers = $semesters->flatMap(fn($s) => $s->offerings)->pluck('teacher_id')->unique()->count();
                @endphp
                <div class="rounded-xl bg-white/10 px-6 py-3 backdrop-blur-sm">
                    <div class="text-3xl font-bold text-white">{{ $totalOfferings ?? $semesters->flatMap(fn($s) => $s->offerings)->count() }}</div>
                    <div class="text-sm font-semibold text-indigo-200">{{ __('Total Offerings') }}</div>
                </div>
                <div class="h-14 w-px bg-white/20"></div>
                <div class="rounded-xl bg-white/10 px-6 py-3 backdrop-blur-sm">
                    <div class="text-3xl font-bold text-white">{{ $totalEnrolled ?? 0 }}</div>
                    <div class="text-sm font-semibold text-indigo-200">{{ __('Enrolled') }}</div>
                </div>
                <div class="h-14 w-px bg-white/20"></div>
                <div class="rounded-xl bg-white/10 px-6 py-3 backdrop-blur-sm">
                    <div class="text-3xl font-bold text-white">{{ $totalCapacity ?? 0 }}</div>
                    <div class="text-sm font-semibold text-indigo-200">{{ __('Capacity') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Offering Button -->
    <div class="mb-6 flex items-center justify-end">
        <flux:button variant="primary" onclick="document.getElementById('create-offering-modal').showModal()">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('Add Offering') }}
        </flux:button>
    </div>

    <!-- Course Offerings by Semester -->
    @forelse($semesters as $semester)
        <div class="mb-8 rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-neutral-900 dark:text-neutral-100">
                            {{ $semester->academicYear->name ?? 'N/A' }} - {{ $semester->name }}
                        </h2>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $semester->offerings->count() }} {{ __('courses') }} ·
                            {{ $semester->offerings->sum(fn($o) => $o->enrollments->count()) }} {{ __('students enrolled') }}
                        </p>
                    </div>
                </div>
                @if($semester->offerings->count() > 0)
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                        {{ __('Active') }}
                    </span>
                @endif
            </div>

            @if($semester->offerings->count() > 0)
                <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-neutral-300 dark:scrollbar-thumb-neutral-600 scrollbar-track-transparent">
                    <table class="w-full min-w-[800px]">
                        <thead class="bg-neutral-50 dark:bg-neutral-900">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Course') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Section') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Teacher') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Enrollment') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            @foreach($semester->offerings as $offering)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50 cursor-pointer transition-colors" onclick="openEditModal({{ $offering->id }})">
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                                    {{ $offering->course->code ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                                    {{ $offering->course->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <span class="inline-flex items-center rounded-full bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                            {{ $offering->section_name }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        @if($offering->teacher)
                                            <div class="flex items-center gap-2">
                                                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400 text-xs">
                                                    {{ strtoupper(substr($offering->teacher?->name ?? 'T', 0, 1)) }}
                                                </div>
                                                <div class="text-sm text-neutral-900 dark:text-neutral-100">
                                                    {{ $offering->teacher?->name ?? __('Unassigned') }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-red-500">{{ __('Unassigned') }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="h-2 w-12 rounded-full bg-neutral-200 dark:bg-neutral-700">
                                                <div class="h-2 rounded-full {{ $offering->enrollments->count() >= $offering->capacity ? 'bg-red-500' : 'bg-indigo-500' }}"
                                                     style="width: {{ $offering->capacity ? min(100, ($offering->enrollments->count() / $offering->capacity) * 100) : 0 }}%"></div>
                                            </div>
                                            <span class="text-sm text-neutral-600 dark:text-neutral-400">
                                                {{ $offering->enrollments->count() }}/{{ $offering->capacity }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4" onclick="event.stopPropagation();">
                                        <button onclick="openEditModal({{ $offering->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-4 text-neutral-500 dark:text-neutral-400">{{ __('No course offerings for this semester') }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="flex flex-col items-center justify-center rounded-2xl border border-neutral-200 bg-white py-16 dark:border-neutral-700 dark:bg-neutral-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('No semesters found') }}</h3>
            <p class="mt-2 text-neutral-500 dark:text-neutral-400">{{ __('Create academic years and semesters first to add course offerings') }}</p>
            <a href="{{ route('admin.academic.years') }}" class="mt-4 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">
                {{ __('Manage Academic Years') }}
            </a>
        </div>
    @endforelse

    <!-- Create Offering Modal -->
    <dialog id="create-offering-modal" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md" style="z-index: 9999;">
        <div class="overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ __('Create New Offering') }}</h2>
                    <p class="text-sm text-indigo-100">{{ __('Add a new course offering for a semester') }}</p>
                </div>
                <button onclick="document.getElementById('create-offering-modal').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.academic.offerings.store') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Course') }}</label>
                    <select name="course_id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">{{ __('Select Course') }}</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Semester') }}</label>
                    <select name="semester_id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">{{ __('Select Semester') }}</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->academicYear->name ?? 'N/A' }} - {{ $semester->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Teacher') }}</label>
                    <select name="teacher_id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">{{ __('Select Teacher') }}</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Section') }}</label>
                        <input type="text" name="section_name" required placeholder="e.g., A" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Capacity') }}</label>
                        <input type="number" name="capacity" required min="1" placeholder="e.g., 30" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Schedule') }}</label>
                    <input type="text" name="schedule" placeholder="e.g., Mon/Wed 10:00-12:00" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Room') }}</label>
                    <input type="text" name="room" placeholder="e.g., Room 101" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('create-offering-modal').close()" class="flex-1 justify-center px-4 py-2 text-neutral-600 hover:bg-neutral-100 rounded-lg">{{ __('Cancel') }}</button>
                <x-button.submit loading-text="{{ __('Creating...') }}" class="flex-1">{{ __('Create Offering') }}</x-button.submit>
            </div>
        </form>
    </dialog>

    <!-- Edit Offering Modal (Dynamic - populated via JavaScript) -->
    <dialog id="edit-offering-modal" class="rounded-xl shadow-2xl backdrop:bg-black/60 p-0 w-full max-w-md" style="z-index: 9999;">
        <div class="overflow-hidden bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 px-6 py-5">
            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full bg-white/10"></div>
            <div class="relative flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-white">{{ __('Edit Offering') }}</h2>
                    <p class="text-sm text-orange-100">{{ __('Update course offering details') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('edit-offering-modal').close()" class="rounded-full bg-white/20 p-2 text-white transition hover:bg-white/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <form method="POST" id="edit-offering-form" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Course') }}</label>
                    <div id="edit-course-name" class="w-full rounded-xl border border-neutral-300 bg-neutral-100 px-4 py-2.5 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                        -
                    </div>
                    <input type="hidden" name="course_id" id="edit-course-id" value="" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Semester') }}</label>
                    <div id="edit-semester-name" class="w-full rounded-xl border border-neutral-300 bg-neutral-100 px-4 py-2.5 text-neutral-900 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                        -
                    </div>
                    <input type="hidden" name="semester_id" id="edit-semester-id" value="" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Teacher') }}</label>
                    <select name="teacher_id" id="edit-teacher-id" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
                        <option value="">{{ __('Select Teacher') }}</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Section') }}</label>
                        <input type="text" name="section_name" id="edit-section-name" required class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Capacity') }}</label>
                        <input type="number" name="capacity" id="edit-capacity" required min="1" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Schedule') }}</label>
                    <input type="text" name="schedule" id="edit-schedule" placeholder="e.g., Mon/Wed 10:00-12:00" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Room') }}</label>
                    <input type="text" name="room" id="edit-room" placeholder="e.g., Room 101" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                    <label for="edit-is-active" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Active') }}</label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="button" onclick="document.getElementById('edit-offering-modal').close()" class="flex-1 justify-center px-4 py-2 text-neutral-600 hover:bg-neutral-100 rounded-lg">{{ __('Cancel') }}</button>
                <x-button.submit loading-text="{{ __('Saving...') }}" class="flex-1">{{ __('Save Changes') }}</x-button.submit>
            </div>
        </form>
    </dialog>

    <script>
        // Store offering data for JavaScript access (check if already defined)
        if (typeof offeringsData === 'undefined') {
            const offeringsData = {};
            @foreach($semesters as $semester)
                @foreach($semester->offerings as $offering)
                    offeringsData[{{ $offering->id }}] = {
                        id: {{ $offering->id }},
                        course_id: {{ $offering->course_id ?? 'null' }},
                        course_name: '{{ $offering->course->code ?? "N/A" }} - {{ $offering->course->name ?? "N/A" }}',
                        semester_id: {{ $offering->semester_id ?? 'null' }},
                        semester_name: '{{ $semester->academicYear->name ?? "N/A" }} - {{ $semester->name }}',
                        teacher_id: {{ $offering->teacher_id ?? 'null' }},
                        section_name: '{{ $offering->section_name }}',
                        capacity: {{ $offering->capacity ?? 0 }},
                        schedule: '{{ $offering->schedule ?? "" }}',
                        room: '{{ $offering->room ?? "" }}',
                        is_active: {{ $offering->is_active ? 'true' : 'false' }},
                        update_url: '{{ route("admin.academic.offerings.update", $offering->id) }}'
                    };
                @endforeach
            @endforeach
        }

        function openEditModal(offeringId) {
            const data = offeringsData[offeringId];
            if (!data) return;

            // Populate form fields
            document.getElementById('edit-course-name').textContent = data.course_name;
            document.getElementById('edit-course-id').value = data.course_id;
            document.getElementById('edit-semester-name').textContent = data.semester_name;
            document.getElementById('edit-semester-id').value = data.semester_id;
            document.getElementById('edit-teacher-id').value = data.teacher_id || '';
            document.getElementById('edit-section-name').value = data.section_name;
            document.getElementById('edit-capacity').value = data.capacity;
            document.getElementById('edit-schedule').value = data.schedule;
            document.getElementById('edit-room').value = data.room;
            document.getElementById('edit-is-active').checked = data.is_active;

            // Set form action
            document.getElementById('edit-offering-form').action = data.update_url;

            // Open modal
            document.getElementById('edit-offering-modal').showModal();
        }
    </script>
</x-layouts::app>
