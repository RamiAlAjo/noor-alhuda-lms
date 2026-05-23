<?php
/**
 * Page: Teacher Excused Absence Details
 *
 * Purpose: Display detailed information about a specific excused absence request.
 * Allows teachers to review, approve, or reject pending requests.
 * Shows student info, absence dates, reason, and review forms.
 *
 * Route: teacher.excused-absences.show (GET)
 *
 * Controller: App\Http\Controllers\Teacher\ExcusedAbsenceController@show
 *
 * Components on this page:
 * - x-layouts::app: Main application layout wrapper
 * - Back navigation link
 * - Status banner (pending/approved/rejected)
 * - Request details card (student, course, dates, type, reason)
 * - Review form (approve/reject) - only shown for pending requests
 * - Review information card - shown for reviewed requests
 *
 * Required Data variables:
 * - $excusedAbsence: ExcusedAbsence model instance with relationships
 *
 * Dependencies:
 * - Routes: teacher.excused-absences.index, teacher.excused-absences.approve, teacher.excused-absences.reject
 * - Helpers: __(), route()
 * - Relationships: ExcusedAbsence->student (User), ExcusedAbsence->courseOffering->course, ExcusedAbsence->reviewer (User)
 * - Methods: ExcusedAbsence->isPending()
 *
 * @package App\Views\Pages\Teacher\ExcusedAbsences
 */

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

<x-layouts::app :title="__('Excused Absence Request')">
    <!-- Header -->
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('teacher.excused-absences.index') }}" class="text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Excused Absence Request') }}</h1>
    </div>

    <!-- Status Banner -->
    @switch($excusedAbsence->status)
        @case('pending')
            <div class="mb-6 rounded-xl border border-yellow-500/50 bg-yellow-50 p-6 dark:bg-yellow-900/20">
                <div class="flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">{{ __('Pending Review') }}</h3>
                        <p class="text-yellow-700 dark:text-yellow-300">{{ __('This excuse request is waiting for your review.') }}</p>
                    </div>
                </div>
            </div>
            @break
        @case('approved')
            <div class="mb-6 rounded-xl border border-green-500/50 bg-green-50 p-6 dark:bg-green-900/20">
                <div class="flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">{{ __('Approved') }}</h3>
                        <p class="text-green-700 dark:text-green-300">{{ __('This excused absence request has been approved.') }}</p>
                    </div>
                </div>
            </div>
            @break
        @case('rejected')
            <div class="mb-6 rounded-xl border border-red-500/50 bg-red-50 p-6 dark:bg-red-900/20">
                <div class="flex items-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800 dark:text-red-200">{{ __('Rejected') }}</h3>
                        <p class="text-red-700 dark:text-red-300">{{ __('This excused absence request was not approved.') }}</p>
                    </div>
                </div>
            </div>
            @break
    @endswitch

    <!-- Request Details -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Request Details') }}</h3>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Student') }}</p>
                <p class="font-medium text-neutral-900 dark:text-neutral-100">
                    {{ $excusedAbsence->student?->name ?? __('Unknown Student') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Course') }}</p>
                <p class="font-medium text-neutral-900 dark:text-neutral-100">
                    {{ $excusedAbsence->courseOffering?->course?->name ?? __('Unknown Course') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Status') }}</p>
                @switch($excusedAbsence->status)
                    @case('pending')
                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            {{ __('Pending') }}
                        </span>
                        @break
                    @case('approved')
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                            {{ __('Approved') }}
                        </span>
                        @break
                    @case('rejected')
                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                            {{ __('Rejected') }}
                        </span>
                        @break
                @endswitch
            </div>

            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Submitted') }}</p>
                <p class="font-medium text-neutral-900 dark:text-neutral-100">
                    {{ $excusedAbsence->created_at->format('F d, Y g:i A') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Absence Date') }}</p>
                <p class="font-medium text-neutral-900 dark:text-neutral-100">
                    {{ $excusedAbsence->absence_date->format('F d, Y') }}
                </p>
            </div>

            @if($excusedAbsence->end_date)
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('End Date') }}</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">
                        {{ $excusedAbsence->end_date->format('F d, Y') }}
                    </p>
                </div>
            @endif

            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Absence Type') }}</p>
                <p class="font-medium text-neutral-900 dark:text-neutral-100">
                    {{ $absenceTypeLabels[$excusedAbsence->absence_type] ?? $excusedAbsence->absence_type }}
                </p>
            </div>

            <div>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Reason Category') }}</p>
                <p class="font-medium text-neutral-900 dark:text-neutral-100">
                    {{ $reasonTypeLabels[$excusedAbsence->reason_type] ?? $excusedAbsence->reason_type }}
                </p>
            </div>
        </div>

        @if($excusedAbsence->reason)
            <div class="mt-6">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Explanation') }}</p>
                <p class="mt-1 text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap">{{ $excusedAbsence->reason }}</p>
            </div>
        @endif
    </div>

    <!-- Review Actions (only for pending requests) -->
    @if($excusedAbsence->isPending())
        <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Review Request') }}</h3>

            <div class="space-y-6">
                <!-- Approve Form -->
                <form method="POST" action="{{ route('teacher.excused-absences.approve', $excusedAbsence) }}">
                    @csrf
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Review Notes (Optional)') }}</label>
                            <input type="text" name="review_notes" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800" placeholder="{{ __('Add notes about your decision...') }}" />
                        </div>
                        <x-button.submit loading-text="Approving..." variant="primary" class="bg-green-600 hover:bg-green-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('Approve') }}
                        </x-button.submit>
                    </div>
                </form>

                <hr class="border-neutral-200 dark:border-neutral-700" />

                <!-- Reject Form -->
                <form method="POST" action="{{ route('teacher.excused-absences.reject', $excusedAbsence) }}">
                    @csrf
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">{{ __('Rejection Reason (Required)') }}</label>
                            <textarea name="review_notes" class="w-full rounded-lg border-neutral-300 dark:border-neutral-600 dark:bg-neutral-800" rows="3" placeholder="{{ __('Please provide a reason for rejection...') }}" required></textarea>
                        </div>
                        <x-button.submit loading-text="Rejecting..." variant="danger">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ __('Reject') }}
                        </x-button.submit>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Review Information -->
    @if($excusedAbsence->reviewer)
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Review Information') }}</h3>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Reviewed By') }}</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">
                        {{ $excusedAbsence->reviewer?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Review Date') }}</p>
                    <p class="font-medium text-neutral-900 dark:text-neutral-100">
                        {{ $excusedAbsence->reviewed_at?->format('F d, Y g:i A') ?? '-' }}
                    </p>
                </div>

                @if($excusedAbsence->review_notes)
                    <div class="col-span-2">
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Review Notes') }}</p>
                        <p class="mt-1 text-neutral-700 dark:text-neutral-300 whitespace-pre-wrap">{{ $excusedAbsence->review_notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-layouts::app>
