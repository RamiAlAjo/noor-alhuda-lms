<?php
/**
 * Teacher Discussions - Create Forum Page
 *
 * Purpose: Form to create a new discussion forum for a course
 * Route: teacher.discussions.create-forum (GET)
 * Controller: App\Http\Controllers\Teacher\DiscussionController@createForum
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Create form: Course, title, title (Arabic), description, description (Arabic), lock option
 * - Flux UI components
 *
 * Required Data Variables:
 * - $offerings: Collection of CourseOffering models for the teacher
 * - $offeringId: Pre-selected offering ID (optional)
 *
 * Dependencies:
 * - Routes: teacher.discussions.index, teacher.discussions.store-forum
 * - Models: CourseOffering, Course
 * - Helpers: __(), route(), old(), error()
 */
?>
<x-layouts::app :title="__('Create Forum')">

<div class="p-6 space-y-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('teacher.discussions.index') }}" class="mr-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <flux:icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('lms.create_forum') }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('teacher.discussions.store-forum') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="course_offering_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('lms.course') }} <span class="text-red-500">*</span>
                </label>
                <select name="course_offering_id" id="course_offering_id" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('lms.select_course') }}</option>
                    @foreach($offerings as $offering)
                        <option value="{{ $offering->id }}" {{ $offeringId == $offering->id ? 'selected' : '' }}>
                            {{ $offering->course?->code ?? '' }} - {{ $offering->course?->name ?? 'Course' }}
                        </option>
                    @endforeach
                </select>
                @error('course_offering_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('lms.title') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.forum_title_placeholder') }}">
                @error('title')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="title_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('lms.title_ar') }}
                </label>
                <input type="text" name="title_ar" id="title_ar"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.title_ar_placeholder') }}">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('lms.description') }}
                </label>
                <textarea name="description" id="description" rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.forum_description_placeholder') }}"></textarea>
            </div>

            <div>
                <label for="description_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('lms.description_ar') }}
                </label>
                <textarea name="description_ar" id="description_ar" rows="4"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="{{ __('lms.description_ar_placeholder') }}"></textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_locked" id="is_locked" value="1"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_locked" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ __('lms.lock_forum') }}
                </label>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('teacher.discussions.index') }}"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    {{ __('lms.cancel') }}
                </a>
                <x-button.submit loading-text="Creating..." variant="primary">
                    {{ __('lms.create_forum') }}
                </x-button.submit>
            </div>
        </form>
    </div>
</x-layouts::app>
