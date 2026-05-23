<?php
/**
 * Admin Announcements - Create Page
 *
 * Purpose: Form to create a new system-wide or targeted announcement
 * Route: admin.announcements.create (GET)
 * Controller: App\Http\Controllers\Admin\AnnouncementController@create
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Create form: Title, content, target audience, target section, publish option
 * - Flux UI components: Input, textarea, select, checkbox
 *
 * Required Data Variables:
 * - $sections: Collection of CourseSection models
 *
 * Dependencies:
 * - Routes: admin.announcements.index, admin.announcements.store
 * - Models: CourseSection, Course
 * - Helpers: __(), route(), old()
 */
?>
<x-layouts::app :title="__('Create Announcement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Create Announcement') }}</h1>
            <flux:button :href="route('admin.announcements.index')" variant="ghost">
                {{ __('Back to Announcements') }}
            </flux:button>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <form method="POST" action="{{ route('admin.announcements.store') }}" class="flex flex-col gap-4">
                @csrf

                <flux:input name="title" :label="__('Title')" required />

                <flux:textarea name="content" :label="__('Content')" rows="5" required />

                <flux:select name="target_type" :label="__('Target Audience')">
                    <option value="global">{{ __('All Users') }}</option>
                    <option value="faculty">{{ __('Specific Faculty') }}</option>
                    <option value="department">{{ __('Specific Department') }}</option>
                    <option value="course">{{ __('Specific Course') }}</option>
                    <option value="section">{{ __('Specific Section') }}</option>
                </flux:select>

                <flux:select name="target_offering_id" :label="__('Target Section (Optional)')">
                    <flux:select.option value="">{{ __('Select section...') }}</flux:select.option>
                    @foreach($sections as $section)
                        <flux:select.option value="{{ $section->id }}">{{ $section->course?->name ?? __('Unknown') }} - {{ $section->section_name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" id="is_published" value="1" class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" />
                    <label for="is_published" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Publish immediately') }}</label>
                </div>

                <div class="flex gap-2">
                    <x-button.submit loading-text="{{ __('Creating...') }}">{{ __('Create Announcement') }}</x-button.submit>
                    <flux:button :href="route('admin.announcements.index')" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
