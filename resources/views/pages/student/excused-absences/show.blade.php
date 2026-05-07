<?php

use function Laravel\Folio\name;

name('student.excused-absences.show');

$absenceTypeLabels = [
    'single_day' => __('Single Day'),
    'multiple_days' => __('Multiple Days'),
    'late_arrival' => __('Late Arrival'),
    'early_departure' => __('Early Departure'),
];

$reasonTypeLabels = [
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
            {{ __('Excused Absence Request') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Status Banner -->
        @switch($excusedAbsence->status)
            @case('pending')
                <flux:card class="border-yellow-500/50 bg-yellow-50 dark:bg-yellow-900/20">
                    <div class="flex items-center gap-4">
                        <flux:icon.clock class="size-8 text-yellow-500" />
                        <div>
                            <flux:heading :level="3" size="lg">{{ __('Pending Review') }}</flux:heading>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ __('Your excused absence request is currently under review.') }}</p>
                        </div>
                        @if($excusedAbsence->isPending())
                            <div class="ms-auto">
                                <form method="POST" action="{{ route('student.excused-absences.destroy', $excusedAbsence) }}" onsubmit="return confirm('{{ __('Are you sure you want to cancel this request?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="danger" size="sm">
                                        {{ __('Cancel Request') }}
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
                            <p class="text-zinc-600 dark:text-zinc-400">{{ __('Your excused absence request has been approved.') }}</p>
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
                            <p class="text-zinc-600 dark:text-zinc-400">{{ __('Your excused absence request was not approved.') }}</p>
                        </div>
                    </div>
                </flux:card>
                @break
        @endswitch

        <!-- Request Details -->
        <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
            <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700 mb-4">
                <flux:heading :level="3" size="lg">{{ __('Request Details') }}</flux:heading>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <flux:label>{{ __('Course') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $excusedAbsence->courseOffering?->course?->name ?? __('Unknown Course') }}
                    </p>
                </div>

                <div>
                    <flux:label>{{ __('Status') }}</flux:label>
                    @switch($excusedAbsence->status)
                        @case('pending')
                            <flux:badge color="yellow">{{ __('Pending') }}</flux:badge>
                            @break
                        @case('approved')
                            <flux:badge color="green">{{ __('Approved') }}</flux:badge>
                            @break
                        @case('rejected')
                            <flux:badge color="red">{{ __('Rejected') }}</flux:badge>
                            @break
                    @endswitch
                </div>

                <div>
                    <flux:label>{{ __('Absence Date') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $excusedAbsence->absence_date->format('F d, Y') }}
                    </p>
                </div>

                @if($excusedAbsence->end_date)
                    <div>
                        <flux:label>{{ __('End Date') }}</flux:label>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $excusedAbsence->end_date->format('F d, Y') }}
                        </p>
                    </div>
                @endif

                <div>
                    <flux:label>{{ __('Absence Type') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $absenceTypeLabels[$excusedAbsence->absence_type] ?? $excusedAbsence->absence_type }}
                    </p>
                </div>

                <div>
                    <flux:label>{{ __('Reason Category') }}</flux:label>
                    <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                        {{ $reasonTypeLabels[$excusedAbsence->reason_type] ?? $excusedAbsence->reason_type }}
                    </p>
                </div>
            </div>

            @if($excusedAbsence->reason)
                <div class="mt-6">
                    <flux:label>{{ __('Explanation') }}</flux:label>
                    <p class="text-zinc-700 dark:text-zinc-300 mt-1 whitespace-pre-wrap">{{ $excusedAbsence->reason }}</p>
                </div>
            @endif

            @if($excusedAbsence->attachments && count($excusedAbsence->attachments) > 0)
                <div class="mt-6">
                    <flux:label>{{ __('Attachments') }}</flux:label>
                    <div class="mt-2 space-y-2">
                        @foreach($excusedAbsence->attachments as $attachment)
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
        @if($excusedAbsence->reviewer)
            <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                <div class="border-b border-zinc-200 pb-4 dark:border-zinc-700 mb-4">
                    <flux:heading :level="3" size="lg">{{ __('Review Information') }}</flux:heading>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <flux:label>{{ __('Reviewed By') }}</flux:label>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $excusedAbsence->reviewer?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <flux:label>{{ __('Review Date') }}</flux:label>
                        <p class="text-zinc-900 dark:text-zinc-100 font-medium">
                            {{ $excusedAbsence->reviewed_at?->format('F d, Y g:i A') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
