<?php

use function Laravel\Folio\name;

name('student.medical-leaves.show');

$leaveTypeLabels = [
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
            {{ __('Medical Leave Request') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Status Banner -->
        @switch($medicalLeave->status)
            @case('pending')
                <flux:card class="border-yellow-500/50 bg-yellow-50 dark:bg-yellow-900/20">
                    <div class="flex items-center gap-4">
                        <flux:icon.clock class="size-8 text-yellow-500" />
                        <div>
                            <flux:heading :level="3" size="lg">{{ __('Pending Review') }}</flux:heading>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ __('Your medical leave request is currently under review.') }}</p>
                        </div>
                        @if($medicalLeave->isPending())
                            <div class="ms-auto flex gap-2">
                                <flux:button :href="route('student.medical-leaves.edit', $medicalLeave)" variant="secondary" size="sm">
                                    {{ __('Edit') }}
                                </flux:button>
                                <form method="POST" action="{{ route('student.medical-leaves.destroy', $medicalLeave) }}" onsubmit="return confirm('{{ __('Are you sure you want to cancel this request?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="danger" size="sm">
                                        {{ __('Cancel') }}
                                    </flux:button>
                                </form>
                            </div>
                        @endif
                    </div>
                </flux:card>
                @break
            @case('approved')
                <flux:card class="border-green-500/50 bg-green-50 dark:bg-green-900/20">
                    <div class="flex items-center gap-4">
                        <flux:icon.check-circle class="size-8 text-green-500" />
                        <div>
                            <flux:heading :level="3" size="lg">{{ __('Approved') }}</flux:heading>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ __('Your medical leave request has been approved.') }}</p>
                        </div>
                    </div>
                </flux:card>
                @break
            @case('rejected')
                <flux:card class="border-red-500/50 bg-red-50 dark:bg-red-900/20">
                    <div class="flex items-center gap-4">
                        <flux:icon.x-circle class="size-8 text-red-500" />
                        <div>
                            <flux:heading :level="3" size="lg">{{ __('Rejected') }}</flux:heading>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ __('Your medical leave request was not approved.') }}</p>
                        </div>
                    </div>
                </flux:card>
                @break
        @endswitch

        <!-- Request Details -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
                <flux:heading :level="3" size="lg">{{ __('Request Details') }}</flux:heading>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <flux:label>{{ __('Leave Type') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $leaveTypeLabels[$medicalLeave->leave_type] ?? $medicalLeave->leave_type }}
                    </p>
                </div>

                <div>
                    <flux:label>{{ __('Duration') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $medicalLeave->duration_days }} {{ __('days') }}
                    </p>
                </div>

                <div>
                    <flux:label>{{ __('Start Date') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $medicalLeave->start_date->format('F d, Y') }}
                    </p>
                </div>

                <div>
                    <flux:label>{{ __('End Date') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $medicalLeave->end_date->format('F d, Y') }}
                    </p>
                </div>

                @if($medicalLeave->semester)
                    <div>
                        <flux:label>{{ __('Semester') }}</flux:label>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $medicalLeave->semester->name }}
                        </p>
                    </div>
                @endif

                <div>
                    <flux:label>{{ __('Status') }}</flux:label>
                    @switch($medicalLeave->status)
                        @case('pending')
                            <flux:badge color="yellow">{{ __('Pending') }}</flux:badge>
                            @break
                        @case('approved')
                            <flux:badge color="green">{{ __('Approved') }}</flux:badge>
                            @break
                        @case('rejected')
                            <flux:badge color="red">{{ __('Rejected') }}</flux:badge>
                            @break
                        @case('cancelled')
                            <flux:badge color="zinc">{{ __('Cancelled') }}</flux:badge>
                            @break
                    @endswitch
                </div>
            </div>

            @if($medicalLeave->reason)
                <div class="mt-6">
                    <flux:label>{{ __('Reason') }}</flux:label>
                    <p class="text-zinc-700 dark:text-zinc-300 mt-1 whitespace-pre-wrap">{{ $medicalLeave->reason }}</p>
                </div>
            @endif

            @if($medicalLeave->medical_notes)
                <div class="mt-6">
                    <flux:label>{{ __('Medical Notes') }}</flux:label>
                    <p class="text-zinc-700 dark:text-zinc-300 mt-1 whitespace-pre-wrap">{{ $medicalLeave->medical_notes }}</p>
                </div>
            @endif

            @if($medicalLeave->attachments && count($medicalLeave->attachments) > 0)
                <div class="mt-6">
                    <flux:label>{{ __('Attachments') }}</flux:label>
                    <div class="mt-2 space-y-2">
                        @foreach($medicalLeave->attachments as $attachment)
                            <div class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                <flux:icon.document class="size-5 text-zinc-500" />
                                <span class="text-zinc-700 dark:text-zinc-300">{{ $attachment['name'] ?? basename($attachment['path']) }}</span>
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="ms-auto text-zinc-500 hover:text-zinc-700">
                                    <flux:icon.arrow-down-tray class="size-4" />
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Review Information -->
        @if($medicalLeave->reviewer)
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <flux:heading :level="3" size="lg">{{ __('Review Information') }}</flux:heading>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <flux:label>{{ __('Reviewed By') }}</flux:label>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $medicalLeave->reviewer?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <flux:label>{{ __('Review Date') }}</flux:label>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $medicalLeave->reviewed_at?->format('F d, Y g:i A') ?? '-' }}
                        </p>
                    </div>

                    @if($medicalLeave->review_notes)
                        <div class="col-span-2">
                            <flux:label>{{ __('Review Notes') }}</flux:label>
                            <p class="text-zinc-700 dark:text-zinc-300 mt-1 whitespace-pre-wrap">{{ $medicalLeave->review_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
