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

                        <flux:textarea name="description" :label="__('Description')" :value="old('description', $course->description)" placeholder="{{ __('Course description') }}" rows="4" />

                        <div class="grid gap-4 md:grid-cols-3">
                            <flux:input name="credits" type="number" :label="__('Credits')" :value="old('credits', $course->credits)" placeholder="3" required />
                            <flux:input name="hours" type="number" :label="__('Weekly Hours')" :value="old('hours', $course->hours)" placeholder="4" />
                            <flux:select name="level" :label="__('Level')">
                                <flux:select.option value="beginner" :selected="$course->level == 'beginner'">{{ __('Beginner') }}</flux:select.option>
                                <flux:select.option value="intermediate" :selected="$course->level == 'intermediate'">{{ __('Intermediate') }}</flux:select.option>
                                <flux:select.option value="advanced" :selected="$course->level == 'advanced'">{{ __('Advanced') }}</flux:select.option>
                            </flux:select>
                        </div>

                        <flux:checkbox name="is_active" :label="__('Active')" :checked="$course->is_active" />
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="mb-4 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Actions') }}</h3>
                    <div class="space-y-3">
                        <flux:button type="submit" variant="primary" class="w-full justify-center">
                            {{ __('Update Course') }}
                        </flux:button>
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
