{{--
    =============================================================================
    ADMIN COURSE EDIT VIEW
    =============================================================================

    Purpose: Form to edit an existing course's information.

    Route: admin.courses.edit
    Controller: Admin\CourseController@edit

    Components:
    - Header with course info and back button
    - Main form with:
      * Course Code input (pre-filled)
      * Department dropdown (pre-selected)
      * Course Name input (pre-filled)
      * Description textarea (pre-filled)
      * Credits, Weekly Hours, Level inputs (pre-filled)
      * Active checkbox (pre-checked)
      * Submit button (Update Course)
    - Sidebar with:
      * Action buttons (Update, Cancel)
      * Course Details (Created, Updated dates)

    Required Data:
    - $course: Course model being edited
    - $departments: Available departments collection

    Dependencies:
    - route('admin.courses.update', $course) - PUT endpoint to update course
    - route('admin.courses.index') - Back to courses list
    - old() - Laravel old input helper

    =============================================================================
--}}
<x-layouts::app :title="__('Edit Course')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Edit Course') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ $course->code }} - {{ $course->name }}</p>
        </div>
        <flux:button :href="route('admin.courses.index')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Courses') }}
        </flux:button>
    </div>

    <form method="POST" action="{{ route('admin.courses.update', $course) }}">
        @csrf
        @method('PUT')

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                <div class="font-semibold">{{ __('Please fix the following errors:') }}</div>
                <ul class="mt-2 list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Information') }}</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:input name="code" :label="__('Course Code')" :value="old('code', $course->code)" placeholder="e.g., CS101" required />
                            <flux:select name="department_id" :label="__('Department')" required>
                                <flux:select.option value="">{{ __('Select Department') }}</flux:select.option>
                                @foreach($departments as $department)
                                    <flux:select.option value="{{ $department->id }}" :selected="$course->department_id == $department->id">{{ $department->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <flux:input name="name" :label="__('Course Name')" :value="old('name', $course->name)" placeholder="e.g., Introduction to Computer Science" required />

                        <div>
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Description') }}</label>
                            <textarea name="description"
                                      class="block w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 placeholder-neutral-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder-neutral-500"
                                      rows="4"
                                      placeholder="{{ __('Course description') }}">{{ old('description', $course->description) }}</textarea>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <flux:input name="credits" type="number" :label="__('Credits')" :value="old('credits', $course->credits)" placeholder="3" required />
                            <flux:input name="theory_hours" type="number" :label="__('Theory Hours')" :value="old('theory_hours', $course->theory_hours)" placeholder="3" />
                            <flux:input name="lab_hours" type="number" :label="__('Lab Hours')" :value="old('lab_hours', $course->lab_hours)" placeholder="0" />
                            <flux:select name="year_level" :label="__('Year Level')">
                                <flux:select.option value="">{{ __('Select Level') }}</flux:select.option>
                                <flux:select.option value="1" :selected="old('year_level', $course->year_level) == 1">{{ __('Year 1') }}</flux:select.option>
                                <flux:select.option value="2" :selected="old('year_level', $course->year_level) == 2">{{ __('Year 2') }}</flux:select.option>
                                <flux:select.option value="3" :selected="old('year_level', $course->year_level) == 3">{{ __('Year 3') }}</flux:select.option>
                                <flux:select.option value="4" :selected="old('year_level', $course->year_level) == 4">{{ __('Year 4') }}</flux:select.option>
                            </flux:select>
                        </div>

                        <input type="hidden" name="is_active" value="0">
                        <flux:checkbox name="is_active" :label="__('Active')" value="1" :checked="$course->is_active" />
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="mb-4 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Actions') }}</h3>
                    <div class="space-y-3">
                        <x-button.submit loading-text="Updating..." variant="primary" class="w-full justify-center">
                            Update Course
                        </x-button.submit>
                        <flux:button :href="route('admin.courses.index')" variant="ghost" class="w-full justify-center">
                            {{ __('Cancel') }}
                        </flux:button>
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="mb-4 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Details') }}</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Created') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $course->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500 dark:text-neutral-400">{{ __('Updated') }}</dt>
                            <dd class="text-neutral-900 dark:text-neutral-100">{{ $course->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </form>
</x-layouts::app>
