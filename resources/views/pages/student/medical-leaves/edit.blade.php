<?php

use function Laravel\Folio\name;

name('student.medical-leaves.edit');

$leaveTypes = [
    'sick' => __('Sick Leave'),
    'emergency' => __('Emergency Leave'),
    'hospitalization' => __('Hospitalization'),
    'chronic' => __('Chronic Condition'),
];

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
        <flux:button :href="route('student.medical-leaves.show', $medicalLeave)" variant="ghost" size="sm">
            <flux:icon.chevron-left class="size-4" />
        </flux:button>
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">
            {{ __('Edit Medical Leave Request') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700 mb-4">
                <flux:heading :level="3" size="lg">{{ __('Update Your Request') }}</flux:heading>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ __('You can only edit pending requests.') }}</p>
            </div>

            <form method="POST" action="{{ route('student.medical-leaves.update', $medicalLeave) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Leave Type -->
                    <div>
                        <flux:label>{{ __('Leave Type') }}</flux:label>
                        <flux:select name="leave_type" required>
                            @foreach($leaveTypes as $value => $label)
                                <option value="{{ $value }}" @if(old('leave_type', $medicalLeave->leave_type) == $value) selected @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </flux:select>
                        <flux:error name="leave_type" />
                    </div>

                    <!-- Semester -->
                    <div>
                        <flux:label>{{ __('Semester') }}</flux:label>
                        <flux:select name="semester_id" nullable>
                            <option value="">{{ __('Select Semester (Optional)') }}</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" @if(old('semester_id', $medicalLeave->semester_id) == $semester->id) selected @endif>
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </flux:select>
                        <flux:error name="semester_id" />
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" required value="{{ old('start_date', $medicalLeave->start_date->toDateString()) }}" />
                            <flux:error name="start_date" />
                        </div>
                        <div>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" required value="{{ old('end_date', $medicalLeave->end_date->toDateString()) }}" />
                            <flux:error name="end_date" />
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <flux:label>{{ __('Reason') }}</flux:label>
                        <flux:textarea name="reason" rows="4" required>{{ old('reason', $medicalLeave->reason) }}</flux:textarea>
                        <flux:error name="reason" />
                    </div>

                    <!-- Medical Notes -->
                    <div>
                        <flux:label>{{ __('Medical Notes') }}</flux:label>
                        <flux:textarea name="medical_notes" rows="3">{{ old('medical_notes', $medicalLeave->medical_notes) }}</flux:textarea>
                        <flux:error name="medical_notes" />
                    </div>

                    <!-- Existing Attachments -->
                    @if($medicalLeave->attachments && count($medicalLeave->attachments) > 0)
                        <div>
                            <flux:label>{{ __('Current Attachments') }}</flux:label>
                            <div class="mt-2 space-y-2">
                                @foreach($medicalLeave->attachments as $index => $attachment)
                                    <div class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                        <flux:icon.document class="size-5 text-zinc-500" />
                                        <span class="text-zinc-700 dark:text-zinc-300 flex-1">{{ $attachment['name'] ?? basename($attachment['path']) }}</span>
                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-zinc-500 hover:text-zinc-700">
                                            <flux:icon.arrow-down-tray class="size-4" />
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Add More Attachments -->
                    <div>
                        <flux:label>{{ __('Add More Documents') }}</flux:label>
                        <flux:input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Upload additional medical certificates or documents (max 10MB each)') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:button :href="route('student.medical-leaves.show', $medicalLeave)" variant="secondary">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Update Request') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
