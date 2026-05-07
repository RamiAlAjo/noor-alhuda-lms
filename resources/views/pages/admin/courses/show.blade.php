{{--
    =============================================================================
    ADMIN COURSE DETAILS VIEW
    =============================================================================

    Purpose: Display detailed information about a specific course including
    its sections, enrolled students, and related majors.

    Route: admin.courses.show
    Controller: Admin\CourseController@show

    Components:
    - Header with course name, code, and action buttons (Edit, Back)
    - Course Information section (code, department, credits, description)
    - Related Majors section (if course has associated majors)
    - Sections table with: Section number, Teacher, Schedule, Room, Students count
    - Add Section button
    - Delete section functionality
    - Sidebar with Quick Stats (Total Sections, Enrolled, Available Seats)

    Required Data:
    - $course: Course model with loaded relationships

    Dependencies:
    - route('admin.courses.edit', $course) - Edit course
    - route('admin.courses.index') - Back to courses list
    - route('admin.courses.sections.create', $course) - Add new section
    - route('admin.sections.destroy', $section) - Delete section
    - full_name($section->teacher) - Get teacher's full name
    - $course->majors - Course majors relationship
    - $course->sections - Course sections relationship
    - $section->enrollments - Section enrollments

    =============================================================================
--}}
<x-layouts::app :title="$course->name">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $course->name }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $course->code }} - {{ $course->department?->name }}</p>
        </div>
        <div class="flex gap-3">
            <flux:button :href="route('admin.courses.edit', $course)" variant="primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                {{ __('Edit') }}
            </flux:button>
            <flux:button :href="route('admin.courses.index')" variant="ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                {{ __('Back to Courses') }}
            </flux:button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Course Info -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Information') }}</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Course Code') }}</dt>
                            <dd>
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $course->code }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Department') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $course->department?->name }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Credits') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $course->credits }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-neutral-100 dark:border-neutral-700">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Course Name (Arabic)') }}</dt>
                            <dd class="font-medium text-neutral-900 dark:text-neutral-100">{{ $course->name_ar ?? '-' }}</dd>
                        </div>
                        <div class="col-span-2 flex justify-between py-2">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Description') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $course->description ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Majors -->
            @if($course->majors->count() > 0)
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Related Majors') }}</h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($course->majors as $major)
                            <span class="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1 text-sm font-medium text-neutral-800 dark:bg-neutral-700 dark:text-neutral-300">
                                {{ $major->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Sections -->
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Sections') }}</h2>
                    <flux:button :href="route('admin.courses.sections.create', $course)" size="sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Add Section') }}
                    </flux:button>
                </div>
                <div class="overflow-x-auto">
                    @if($course->sections->count() > 0)
                        <table class="w-full text-left text-sm">
                            <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                                <tr>
                                    <th class="px-6 py-3">{{ __('Section') }}</th>
                                    <th class="px-6 py-3">{{ __('Teacher') }}</th>
                                    <th class="px-6 py-3">{{ __('Schedule') }}</th>
                                    <th class="px-6 py-3">{{ __('Room') }}</th>
                                    <th class="px-6 py-3">{{ __('Students') }}</th>
                                    <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                @foreach($course->sections as $section)
                                <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                                    <td class="px-6 py-4 font-medium text-neutral-900 dark:text-neutral-100">{{ __('Section') }} {{ $section->section_name }}</td>
                                    <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ full_name($section->teacher) }}</td>
                                    <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $section->schedule ?? '-' }}</td>
                                    <td class="px-6 py-4 text-neutral-500 dark:text-neutral-400">{{ $section->room ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $section->enrollments->count() }}/{{ $section->capacity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </flux:button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="flex flex-col items-center justify-center py-12">
                            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No sections yet') }}</h3>
                            <p class="text-neutral-500 dark:text-neutral-400">{{ __('Add a section to this course') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quick Stats') }}</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('Total Sections') }}</span>
                        <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $course->sections->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('Total Enrolled') }}</span>
                        <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $course->sections->sum(fn($s) => $s->enrollments->count()) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-neutral-500 dark:text-neutral-400">{{ __('Available Seats') }}</span>
                        <span class="text-xl font-bold text-neutral-900 dark:text-neutral-100">{{ $course->sections->sum('capacity') - $course->sections->sum(fn($s) => $s->enrollments->count()) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
