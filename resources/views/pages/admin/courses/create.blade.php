{{--
    =============================================================================
    ADMIN COURSE CREATE VIEW
    =============================================================================

    Purpose: Form to create a new course in the system.

    Route: admin.courses.create
    Controller: Admin\CourseController@create

    Components:
    - Header with back button
    - Main form with:
      * Course Code input
      * Department dropdown
      * Course Name input
      * Description textarea
      * Credits, Weekly Hours, Level inputs
      * Active checkbox
      * Submit button (Create Course)
    - Sidebar with:
      * Action buttons (Create, Cancel)
      * Tips for creating courses

    Required Data:
    - $departments: Available departments collection

    Dependencies:
    - route('admin.courses.store') - POST endpoint to create course
    - route('admin.courses.index') - Back to courses list

    =============================================================================
--}}
<x-layouts::app :title="__('Add New Course')">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Add New Course') }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Create a new course in the system') }}</p>
        </div>
        <flux:button :href="route('admin.courses.index')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            {{ __('Back to Courses') }}
        </flux:button>
    </div>

    <form method="POST" action="{{ route('admin.courses.store') }}">
        @csrf

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Course Information') }}</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <flux:input name="code" :label="__('Course Code')" placeholder="e.g., CS101" required />
                            <flux:select name="department_id" :label="__('Department')" required>
                                <flux:select.option value="">{{ __('Select Department') }}</flux:select.option>
                                @foreach($departments as $department)
                                    <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <flux:input name="name" :label="__('Course Name')" placeholder="e.g., Introduction to Computer Science" required />

                        <flux:textarea name="description" :label="__('Description')" placeholder="{{ __('Course description') }}" rows="4" />

                        <div class="grid gap-4 md:grid-cols-3">
                            <flux:input name="credits" type="number" :label="__('Credits')" placeholder="3" required />
                            <flux:input name="hours" type="number" :label="__('Weekly Hours')" placeholder="4" />
                            <flux:select name="level" :label="__('Level')">
                                <flux:select.option value="beginner">{{ __('Beginner') }}</flux:select.option>
                                <flux:select.option value="intermediate">{{ __('Intermediate') }}</flux:select.option>
                                <flux:select.option value="advanced">{{ __('Advanced') }}</flux:select.option>
                            </flux:select>
                        </div>

                        <flux:checkbox name="is_active" :label="__('Active')" checked />
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="mb-4 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Actions') }}</h3>
                    <div class="space-y-3">
                        <flux:button type="submit" variant="primary" class="w-full justify-center">
                            {{ __('Create Course') }}
                        </flux:button>
                        <flux:button :href="route('admin.courses.index')" variant="ghost" class="w-full justify-center">
                            {{ __('Cancel') }}
                        </flux:button>
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <h3 class="mb-4 font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Tips') }}</h3>
                    <ul class="space-y-2 text-sm text-neutral-600 dark:text-neutral-400">
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mt-0.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Use a unique course code') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mt-0.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Add a detailed description') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mt-0.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Set correct credit hours') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</x-layouts::app>
