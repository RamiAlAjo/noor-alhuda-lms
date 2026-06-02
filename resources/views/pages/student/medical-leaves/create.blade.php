<?php

use function Laravel\Folio\name;

name('student.medical-leaves.create');

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
        <flux:button :href="route('student.medical-leaves.index')" variant="ghost" size="sm">
            <flux:icon.chevron-left class="size-4" />
        </flux:button>
        <h2 class="text-xl font-semibold text-zinc-800 dark:text-zinc-200">
            {{ __('Request Medical Leave') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <flux:card>
            <form method="POST" action="{{ route('student.medical-leaves.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Leave Type -->
                    <div>
                        <flux:label>{{ __('Leave Type') }}</flux:label>
                        <select name="leave_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @foreach($leaveTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <flux:error name="leave_type" />
                    </div>

                    <!-- Semester -->
                    <div>
                        <flux:label>{{ __('Semester') }}</flux:label>
                        <select name="semester_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">{{ __('Select Semester (Optional)') }}</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                            @endforeach
                        </select>
                        <flux:error name="semester_id" />
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <flux:label>{{ __('Start Date') }}</flux:label>
                            <flux:input type="date" name="start_date" required min="{{ now()->toDateString() }}" />
                            <flux:error name="start_date" />
                        </div>
                        <div>
                            <flux:label>{{ __('End Date') }}</flux:label>
                            <flux:input type="date" name="end_date" required min="{{ now()->toDateString() }}" />
                            <flux:error name="end_date" />
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <flux:label>{{ __('Reason') }}</flux:label>
                        <flux:textarea name="reason" rows="4" required :placeholder="__('Please provide details about your medical condition and why you need this leave...')" />
                        <flux:error name="reason" />
                    </div>

                    <!-- Medical Notes -->
                    <div>
                        <flux:label>{{ __('Medical Notes') }}</flux:label>
                        <flux:textarea name="medical_notes" rows="3" :placeholder="__('Any additional medical information (optional)...')" />
                        <flux:error name="medical_notes" />
                    </div>

                    <!-- Attachments -->
                    <div>
                        <flux:label>{{ __('Medical Documents') }}</flux:label>
                        <flux:input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Upload medical certificates, prescriptions, or other relevant documents (max 10MB each)') }}
                        </p>
                        <flux:error name="attachments.*" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:button :href="route('student.medical-leaves.index')" variant="secondary">
                        {{ __('Cancel') }}
                    </flux:button>
                    <x-button.submit loading-text="Submitting..." variant="primary">
                        Submit Request
                    </x-button.submit>
                </div>
            </form>
        </flux:card>
    </div>
