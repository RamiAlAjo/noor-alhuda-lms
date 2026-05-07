<x-layouts::app :title="__('Academic Management')">
    <!-- Main Header -->
    <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-8 text-white shadow-2xl dark:from-indigo-900 dark:via-purple-900 dark:to-pink-900">
        <!-- Decorative Blur Elements -->
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
                    <h1 class="text-4xl font-bold tracking-tight text-white">{{ __('Academic Management') }}</h1>
                    <p class="mt-1 text-lg font-medium text-indigo-100 dark:text-indigo-200">{{ __('Overview of faculties, departments, majors, and course offerings') }}</p>
                </div>
            </div>
            <div class="hidden items-center gap-6 md:flex">
                <a href="{{ route('admin.academic.years') }}" class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm hover:bg-white/20 transition">
                    <div class="text-2xl font-bold text-white">{{ $academicYears->count() }}</div>
                    <div class="text-xs font-semibold text-indigo-200">{{ __('Years') }}</div>
                </a>
                <a href="{{ route('admin.academic.faculties') }}" class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm hover:bg-white/20 transition">
                    <div class="text-2xl font-bold text-white">{{ $faculties->count() }}</div>
                    <div class="text-xs font-semibold text-indigo-200">{{ __('Faculties') }}</div>
                </a>
                <a href="{{ route('admin.academic.departments') }}" class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm hover:bg-white/20 transition">
                    <div class="text-2xl font-bold text-white">{{ $departments->count() }}</div>
                    <div class="text-xs font-semibold text-indigo-200">{{ __('Depts') }}</div>
                </a>
                <a href="{{ route('admin.academic.majors') }}" class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm hover:bg-white/20 transition">
                    <div class="text-2xl font-bold text-white">{{ $majors->count() }}</div>
                    <div class="text-xs font-semibold text-indigo-200">{{ __('Majors') }}</div>
                </a>
                <a href="{{ route('admin.academic.offerings') }}" class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-sm hover:bg-white/20 transition">
                    <div class="text-2xl font-bold text-white">{{ $courseOfferings->count() }}</div>
                    <div class="text-xs font-semibold text-indigo-200">{{ __('Offerings') }}</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Academic Years Card -->
        <a href="{{ route('admin.academic.years') }}" class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-indigo-100 opacity-50 transition-transform group-hover:scale-150 dark:bg-indigo-900/30"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $academicYears->count() }}</div>
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Academic Years') }}</div>
                </div>
                <div class="mt-2 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                    {{ $academicYears->where('is_active', true)->count() }} {{ __('Active') }}
                </div>
            </div>
        </a>

        <!-- Total Enrollments Card -->
        <a href="{{ route('admin.enrollments.index') }}" class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-amber-100 opacity-50 transition-transform group-hover:scale-150 dark:bg-amber-900/30"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $totalEnrollments ?? 0 }}</div>
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Total Enrollments') }}</div>
                </div>
                <div class="mt-2 text-sm font-medium text-amber-600 dark:text-amber-400">
                    {{ $activeEnrollments ?? 0 }} {{ __('Active') }}
                </div>
            </div>
        </a>

        <!-- Faculties Card -->
        <a href="{{ route('admin.academic.faculties') }}" class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-purple-100 opacity-50 transition-transform group-hover:scale-150 dark:bg-purple-900/30"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $faculties->count() }}</div>
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Faculties') }}</div>
                </div>
                <div class="mt-2 text-sm font-medium text-purple-600 dark:text-purple-400">
                    {{ $faculties->sum(fn($f) => $f->departments->count()) }} {{ __('Departments') }}
                </div>
            </div>
        </a>

        <!-- Departments Card -->
        <a href="{{ route('admin.academic.departments') }}" class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-pink-100 opacity-50 transition-transform group-hover:scale-150 dark:bg-pink-900/30"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-100 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $departments->count() }}</div>
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Departments') }}</div>
                </div>
                <div class="mt-2 text-sm font-medium text-pink-600 dark:text-pink-400">
                    {{ $departments->sum(fn($d) => $d->majors->count()) }} {{ __('Majors') }}
                </div>
            </div>
        </a>

        <!-- Majors Card -->
        <a href="{{ route('admin.academic.majors') }}" class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-emerald-100 opacity-50 transition-transform group-hover:scale-150 dark:bg-emerald-900/30"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="mt-4">
                    <div class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $majors->count() }}</div>
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Majors') }}</div>
                </div>
                <div class="mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                    {{ $courseOfferings->count() }} {{ __('Course Offerings') }}
                </div>
            </div>
        </a>

        <!-- Active Semester Card -->
        @if($activeSemester)
        <a href="{{ route('admin.academic.offerings') }}" class="group relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm transition-all hover:shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute right-0 top-0 h-24 w-24 -translate-y-8 translate-x-8 rounded-full bg-cyan-100 opacity-50 transition-transform group-hover:scale-150 dark:bg-cyan-900/30"></div>
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-100 text-cyan-600 dark:bg-cyan-900/30 dark:text-cyan-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="mt-4">
                    <div class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $activeSemester->name }}</div>
                    <div class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ __('Active Semester') }}</div>
                </div>
                <div class="mt-2 text-sm font-medium text-cyan-600 dark:text-cyan-400">
                    {{ $activeSemester->academicYear->name ?? '' }}
                </div>
            </div>
        </a>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Academic Years & Semesters -->
        <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Academic Years & Semesters') }}</h2>
                <a href="{{ route('admin.academic.years') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    {{ __('View All') }} →
                </a>
            </div>
            <div class="p-6">
                @forelse($academicYears->take(5) as $year)
                    <div class="mb-4 rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $year->is_active ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700 dark:text-neutral-400' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ $year->name }}</div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $year->start_year }} - {{ $year->end_year }}</div>
                                </div>
                            </div>
                            @if($year->is_active)
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ __('Active') }}</span>
                            @endif
                        </div>
                        @if($year->semesters->count() > 0)
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($year->semesters as $semester)
                                    <span class="rounded-full bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ $semester->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('No academic years found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Course Offerings by Semester -->
        <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800 lg:col-span-2">
            <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Offerings by Semester') }}</h2>
                <a href="{{ route('admin.academic.offerings') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    {{ __('View All Courses') }} →
                </a>
            </div>
            <div class="p-6">
                @php
                    $offeringsBySemester = $courseOfferings->groupBy(fn($o) => ($o->semester?->academicYear?->name ?? __('Unknown')) . ' - ' . ($o->semester?->name ?? __('Unknown')));
                @endphp

                @forelse($offeringsBySemester as $semesterName => $semesterOfferings)
                    <div class="mb-6 last:mb-0">
                        <div class="mb-3 flex items-center gap-2">
                            <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                {{ $semesterName }}
                            </span>
                            <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ $semesterOfferings->count() }} {{ __('courses') }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($semesterOfferings as $offering)
                                <a href="{{ route('admin.courses.show', $offering->course) }}" class="flex items-center gap-3 rounded-xl border border-neutral-200 p-3 transition hover:border-indigo-300 hover:bg-indigo-50 dark:border-neutral-700 dark:hover:border-indigo-600 dark:hover:bg-indigo-900/20">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="truncate text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ $offering->course->code ?? '' }}</div>
                                        <div class="truncate text-xs text-neutral-500 dark:text-neutral-400">{{ $offering->course->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-medium text-neutral-900 dark:text-neutral-100">{{ $offering->section_name }}</div>
                                        <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ $offering->enrollments->count() }}/{{ $offering->capacity }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('No course offerings found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Faculties with Departments -->
        <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Faculties & Departments') }}</h2>
                <a href="{{ route('admin.academic.faculties') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    {{ __('View All') }} →
                </a>
            </div>
            <div class="p-6">
                @forelse($faculties->take(4) as $faculty)
                    <div class="mb-4 rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ $faculty->name }}</div>
                                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ $faculty->departments->count() }} {{ __('departments') }}
                                </div>
                            </div>
                        </div>
                        @if($faculty->departments->count() > 0)
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($faculty->departments->take(3) as $dept)
                                    <span class="rounded-full bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ $dept->name }}
                                    </span>
                                @endforeach
                                @if($faculty->departments->count() > 3)
                                    <span class="rounded-full bg-neutral-100 px-2 py-1 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                        +{{ $faculty->departments->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-neutral-500 dark:text-neutral-400">{{ __('No faculties found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Majors -->
        <div class="rounded-2xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Majors by Degree') }}</h2>
                <a href="{{ route('admin.academic.majors') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    {{ __('View All') }} →
                </a>
            </div>
            <div class="p-6">
                @php
                    $bachelorMajors = $majors->where('degree', 'bachelor');
                    $masterMajors = $majors->where('degree', 'master');
                    $phdMajors = $majors->where('degree', 'phd');
                @endphp

                <div class="space-y-4">
                    @if($bachelorMajors->count() > 0)
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ __('Bachelor') }}</span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $bachelorMajors->count() }} {{ __('programs') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($bachelorMajors->take(5) as $major)
                                    <span class="rounded-lg bg-neutral-100 px-3 py-1.5 text-sm font-medium text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ $major->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($masterMajors->count() > 0)
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">{{ __('Master') }}</span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $masterMajors->count() }} {{ __('programs') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($masterMajors->take(5) as $major)
                                    <span class="rounded-lg bg-neutral-100 px-3 py-1.5 text-sm font-medium text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ $major->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($phdMajors->count() > 0)
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">{{ __('PhD') }}</span>
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">{{ $phdMajors->count() }} {{ __('programs') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($phdMajors->take(5) as $major)
                                    <span class="rounded-lg bg-neutral-100 px-3 py-1.5 text-sm font-medium text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300">
                                        {{ $major->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($majors->count() == 0)
                        <div class="py-8 text-center">
                            <p class="text-neutral-500 dark:text-neutral-400">{{ __('No majors found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quick Actions') }}</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.academic.years') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-100 px-4 py-2.5 text-sm font-medium text-indigo-700 transition hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Add Academic Year') }}
            </a>
            <a href="{{ route('admin.academic.faculties') }}" class="inline-flex items-center gap-2 rounded-xl bg-purple-100 px-4 py-2.5 text-sm font-medium text-purple-700 transition hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:hover:bg-purple-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Add Faculty') }}
            </a>
            <a href="{{ route('admin.academic.departments') }}" class="inline-flex items-center gap-2 rounded-xl bg-pink-100 px-4 py-2.5 text-sm font-medium text-pink-700 transition hover:bg-pink-200 dark:bg-pink-900/30 dark:text-pink-300 dark:hover:bg-pink-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Add Department') }}
            </a>
            <a href="{{ route('admin.academic.majors') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-100 px-4 py-2.5 text-sm font-medium text-emerald-700 transition hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Add Major') }}
            </a>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-100 px-4 py-2.5 text-sm font-medium text-amber-700 transition hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Add Course') }}
            </a>
        </div>
    </div>
</x-layouts::app>
