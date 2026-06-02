<?php

use function Laravel\Folio\name;

name('student.excused-absences.create');

$absenceTypes = [
    'single_day' => __('Single Day Absence'),
    'multiple_days' => __('Multiple Days Absence'),
    'late_arrival' => __('Late Arrival'),
    'early_departure' => __('Early Departure'),
];

$reasonTypes = [
    'personal' => __('Personal Reasons'),
    'family_emergency' => __('Family Emergency'),
    'religious' => __('Religious Observance'),
    'medical_appointment' => __('Medical Appointment'),
    'other' => __('Other'),
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
        <flux:button :href="route('student.excused-absences.index')" variant="ghost" size="sm">
            <flux:icon.chevron-left class="size-4" />
        </flux:button>
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">
            {{ __('Request Excused Absence') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <flux:card>
            <form method="POST" action="{{ route('student.excused-absences.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Course -->
                    <div>
                        <flux:label>{{ __('Course') }}</flux:label>
                        <select name="course_offering_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">{{ __('Select a Course') }}</option>
                            @foreach($enrollments as $enrollment)
                                <option value="{{ $enrollment->offering?->id }}" @if($preselectedCourse == $enrollment->offering?->id) selected @endif>
                                    {{ $enrollment->offering?->course?->name ?? __('Unknown') }} - {{ $enrollment->offering?->semester?->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        <flux:error name="course_offering_id" />
                    </div>

                    <!-- Absence Type -->
                    <div>
                        <flux:label>{{ __('Absence Type') }}</flux:label>
                        <select name="absence_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach($absenceTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <flux:error name="absence_type" />
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>{{ __('Absence Date') }}</flux:label>
                            <flux:input type="date" name="absence_date" required max="{{ now()->toDateString() }}" />
                            <flux:error name="absence_date" />
                        </div>
                        <div>
                            <flux:label>{{ __('End Date (Optional)') }}</flux:label>
                            <flux:input type="date" name="end_date" />
                            <flux:error name="end_date" />
                            <p class="mt-1 text-xs text-zinc-500">{{ __('Required for multiple days absence') }}</p>
                        </div>
                    </div>

                    <!-- Reason Type -->
                    <div>
                        <flux:label>{{ __('Reason Category') }}</flux:label>
                        <select name="reason_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach($reasonTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <flux:error name="reason_type" />
                    </div>

                    <!-- Reason -->
                    <div>
                        <flux:label>{{ __('Explanation') }}</flux:label>
                        <flux:textarea name="reason" rows="4" required :placeholder="__('Please explain the reason for your absence...')" />
                        <flux:error name="reason" />
                    </div>

                    <!-- Attachments -->
                    <div>
                        <flux:label>{{ __('Supporting Documents') }}</flux:label>
                        <flux:input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Upload supporting documents such as medical certificates, appointment letters, etc. (max 10MB each)') }}
                        </p>
                        <flux:error name="attachments.*" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:button :href="route('student.excused-absences.index')" variant="secondary">
                        {{ __('Cancel') }}
                    </flux:button>
                    <x-button.submit loading-text="Submitting..." variant="primary">
                        Submit Request
                    </x-button.submit>
                </div>
            </form>
        </flux:card>
    </div>
