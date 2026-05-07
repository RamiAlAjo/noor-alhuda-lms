<?php
/**
 * Admin Announcements - Edit Page
 *
 * Purpose: Form to edit an existing announcement
 * Route: admin.announcements.edit (GET)
 * Controller: App\Http\Controllers\Admin\AnnouncementController@edit
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Edit form: Pre-filled form with existing announcement data
 * - Flux UI components: Input, textarea, select, checkbox
 *
 * Required Data Variables:
 * - $announcement: Announcement model instance
 * - $sections: Collection of CourseSection models
 *
 * Dependencies:
 * - Routes: admin.announcements.index, admin.announcements.update
 * - Models: Announcement, CourseSection, Course
 * - Helpers: __(), route(), old()
 */
?>
<x-layouts::app :title="__('Edit Announcement')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Edit Announcement') }}</h1>
            <flux:button :href="route('admin.announcements.index')" variant="ghost">
                {{ __('Back to Announcements') }}
            </flux:button>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <flux:input name="title" :label="__('Title')" :value="old('title', $announcement->title)" required />

                <flux:textarea name="content" :label="__('Content')" rows="5" required>{{ old('content', $announcement->content) }}</flux:textarea>

                <flux:select name="target_type" :label="__('Target Audience')">
                    <option value="global" {{ $announcement->target_type === 'global' ? 'selected' : '' }}>{{ __('All Users') }}</option>
                    <option value="faculty" {{ $announcement->target_type === 'faculty' ? 'selected' : '' }}>{{ __('Specific Faculty') }}</option>
                    <option value="department" {{ $announcement->target_type === 'department' ? 'selected' : '' }}>{{ __('Specific Department') }}</option>
                    <option value="course" {{ $announcement->target_type === 'course' ? 'selected' : '' }}>{{ __('Specific Course') }}</option>
                    <option value="section" {{ $announcement->target_type === 'section' ? 'selected' : '' }}>{{ __('Specific Section') }}</option>
                </flux:select>

                <flux:select name="target_offering_id" :label="__('Target Section')">
                    <flux:select.option value="">{{ __('Select section...') }}</flux:select.option>
                    @foreach($sections as $section)
                        @if($announcement->target_offering_id == $section->id)
                            <flux:select.option value="{{ $section->id }}" selected>{{ $section->course?->name ?? __('Unknown') }} - {{ $section->section_name }}</flux:select.option>
                        @else
                            <flux:select.option value="{{ $section->id }}">{{ $section->course?->name ?? __('Unknown') }} - {{ $section->section_name }}</flux:select.option>
                        @endif
                    @endforeach
                </flux:select>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" id="is_published" value="1" class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500" {{ $announcement->is_published ? 'checked' : '' }} />
                    <label for="is_published" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Publish immediately') }}</label>
                </div>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">{{ __('Update Announcement') }}</flux:button>
                    <flux:button :href="route('admin.announcements.index')" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
