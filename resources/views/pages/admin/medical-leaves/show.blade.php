<?php
/**
 * Page: Medical Leave Details
 *
 * Purpose: Display detailed information about a specific medical leave request.
 * Allows admins to review, approve, or reject pending leave requests.
 * Shows student info, leave dates, reason, doctor notes, and review section.
 *
 * Route: admin.medical-leaves.show (GET)
 *
 * Controller: App\Http\Controllers\Admin\MedicalLeaveController@show
 *
 * Components on this page:
 * - x-app-layout: Main application layout wrapper
 * - Back navigation link
 * - Medical leave details card (student, type, dates, reason, attachments)
 * - Review form (approve/reject) - only shown for pending leaves
 * - Review details card - shown for reviewed leaves
 *
 * Required Data variables:
 * - $medicalLeave: MedicalLeave model instance with relationships
 *
 * Dependencies:
 * - Routes: admin.medical-leaves.index, admin.medical-leaves.approve, admin.medical-leaves.reject, admin.users.show
 * - Helpers: __(), route()
 * - Relationships: MedicalLeave->student (User), MedicalLeave->reviewer (User), MedicalLeave->isPending(), MedicalLeave->isReviewed()
 * - Methods: MedicalLeave->isPending(), MedicalLeave->isReviewed()
 *
 * @package App\Views\Pages\Admin\MedicalLeaves
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.medical_leave_details') }}</x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.medical-leaves.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_leaves') }}
                </a>
            </div>

            <!-- Medical Leave Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ __('lms.medical_leave_details') }}
                        </h2>
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @switch($medicalLeave->status)
                                @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                @case('approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                @case('rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                @case('withdrawn') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @break
                            @endswitch
                        ">
                            {{ __(ucfirst($medicalLeave->status)) }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.student') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <a href="{{ route('admin.users.show', $medicalLeave->student) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $medicalLeave->student?->name ?? __('Unknown') }}
                                </a>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.student_id') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $medicalLeave->student?->user_id ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.leave_type') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ __($medicalLeave->leave_type) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.reason') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $medicalLeave->reason }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.start_date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $medicalLeave->start_date->format('M d, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.end_date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $medicalLeave->end_date->format('M d, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.duration_days') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $medicalLeave->duration_days }} {{ __('lms.days') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.submitted') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $medicalLeave->created_at->format('M d, Y H:i') }}</dd>
                        </div>

                        @if($medicalLeave->doctor_note)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.doctor_note') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $medicalLeave->doctor_note }}</dd>
                        </div>
                        @endif

                        @if($medicalLeave->attachments)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.attachments') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                <!-- Add attachment links here if needed -->
                                {{ $medicalLeave->attachments }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Review Section (only for pending) -->
            @if($medicalLeave->isPending())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ __('lms.review_leave') }}
                        </h2>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Approve Form -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('lms.approve') }}</h3>
                            <form method="POST" action="{{ route('admin.medical-leaves.approve', $medicalLeave) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="affects_attendance" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('lms.affects_attendance') }}</span>
                                    </label>
                                </div>
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="requires_makeup" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('lms.requires_makeup') }}</span>
                                    </label>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.review_notes') }}</label>
                                    <textarea name="review_notes" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="{{ __('lms.optional_notes') }}"></textarea>
                                </div>
                                 <x-button.submit loading-text="{{ __('Approving...') }}" class="w-full">
                                     {{ __('lms.approve_leave') }}
                                 </x-button.submit>
                            </form>
                        </div>

                        <!-- Reject Form -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('lms.reject') }}</h3>
                            <form method="POST" action="{{ route('admin.medical-leaves.reject', $medicalLeave) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.rejection_reason') }} *</label>
                                    <textarea name="review_notes" rows="6" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="{{ __('lms.enter_rejection_reason') }}"></textarea>
                                </div>
                                 <x-button.submit variant="danger" loading-text="{{ __('Rejecting...') }}" class="w-full">
                                     {{ __('lms.reject_leave') }}
                                 </x-button.submit>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Review Details (if reviewed) -->
            @if($medicalLeave->isReviewed())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ __('lms.review_details') }}
                        </h2>
                    </div>

                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.reviewed_by') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($medicalLeave->reviewer)
                                        {{ $medicalLeave->reviewer->name }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.reviewed_at') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    @if($medicalLeave->reviewed_at)
                                        {{ $medicalLeave->reviewed_at->format('M d, Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>

                            @if($medicalLeave->review_notes)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.review_notes') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $medicalLeave->review_notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
