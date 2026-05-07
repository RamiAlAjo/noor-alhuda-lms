<?php
/**
 * Page: Edit Grade Appeal
 *
 * Purpose: Display a form for editing an existing grade appeal.
 * Allows students to update subject, description, justification, requested grade, and add more attachments.
 * Only pending appeals can be edited.
 *
 * Route: student.appeals.edit (GET)
 *
 * Controller: App\Http\Controllers\Student\GradeAppealController@edit
 *
 * Components on this page:
 * - Back navigation link
 * - Form with sections:
 *   - Subject input (required)
 *   - Description textarea (required)
 *   - Student justification textarea (required)
 *   - Requested grade input
 *   - Current attachments display (with download links)
 *   - Add more attachments input
 * - Submit and Cancel buttons
 *
 * Required Data variables:
 * - $appeal: GradeAppeal model instance with relationships
 *
 * Dependencies:
 * - Routes: student.appeals.show, student.appeals.update
 * - Helpers: __(), route(), old(), Storage::url()
 * - Relationships: GradeAppeal->attachments
 * - Flux UI components: flux:button, flux:icon, flux:heading, flux:label, flux:input, flux:textarea, flux:error
 *
 * @package App\Views\Pages\Student\Appeals
 */

use function Laravel\Folio\name;

name('student.appeals.edit');

?>

<?php $layout = 'layouts.app'; ?>
<?php if (app('auth')->check() && app('auth')->user()->role === 'admin'): ?>
<?php $layout = 'layouts.admin'; ?>
<?php elseif (app('auth')->check() && app('auth')->user()->role === 'teacher'): ?>
<?php $layout = 'layouts.app'; ?>
<?php endif; ?>

@extends($layout)

@section('subheader')
    <div class="flex items-center gap-4">
        <flux:button :href="route('student.appeals.show', $appeal)" variant="ghost" size="sm">
            <flux:icon.chevron-left class="size-4" />
        </flux:button>
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">
            {{ __('Edit Grade Appeal') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700 mb-4">
                <flux:heading :level="3" size="lg">{{ __('Update Your Appeal') }}</flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('You can only edit pending appeals.') }}</p>
            </div>

            <form method="POST" action="{{ route('student.appeals.update', $appeal) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Subject -->
                    <div>
                        <flux:label>{{ __('Subject') }}</flux:label>
                        <flux:input name="subject" required value="{{ old('subject', $appeal->subject) }}" />
                        <flux:error name="subject" />
                    </div>

                    <!-- Description -->
                    <div>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea name="description" rows="5" required>{{ old('description', $appeal->description) }}</flux:textarea>
                        <flux:error name="description" />
                    </div>

                    <!-- Student Justification -->
                    <div>
                        <flux:label>{{ __('Your Justification') }}</flux:label>
                        <flux:textarea name="student_justification" rows="5" required>{{ old('student_justification', $appeal->student_justification) }}</flux:textarea>
                        <flux:error name="student_justification" />
                    </div>

                    <!-- Requested Grade -->
                    <div>
                        <flux:label>{{ __('Requested Grade (Optional)') }}</flux:label>
                        <flux:input type="number" name="requested_grade" min="0" max="100" value="{{ old('requested_grade', $appeal->requested_grade) }}" />
                        <flux:error name="requested_grade" />
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Enter the grade you believe you should have received (0-100).') }}
                        </p>
                    </div>

                    <!-- Current Attachments -->
                    @if($appeal->attachments && count($appeal->attachments) > 0)
                        <div>
                            <flux:label>{{ __('Current Attachments') }}</flux:label>
                            <div class="mt-2 space-y-2">
                                @foreach($appeal->attachments as $attachment)
                                    <div class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                        <flux:icon.document class="size-5 text-zinc-500" />
                                        <span class="text-zinc-700 dark:text-zinc-300 flex-1">{{ $attachment['name'] ?? basename($attachment['path']) }}</span>
                                        <a href="{{ Storage::url(ltrim($attachment['path'], '/')) }}" target="_blank" class="text-zinc-500 hover:text-zinc-700">
                                            <flux:icon.arrow-down-tray class="size-4" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add More Attachments -->
                    <div>
                        <flux:label>{{ __('Add More Supporting Documents') }}</flux:label>
                        <flux:input type="file" name="attachments[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Upload additional documents to support your appeal (max 10MB each).') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:button :href="route('student.appeals.show', $appeal)" variant="secondary">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Update Appeal') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
