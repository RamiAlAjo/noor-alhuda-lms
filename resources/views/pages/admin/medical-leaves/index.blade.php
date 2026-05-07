<?php
/**
 * Page: Medical Leaves Index
 *
 * Purpose: Display a paginated list of all medical leave requests with filtering capabilities.
 * Allows admins to search, filter by status/type/date, and export leave records.
 *
 * Route: admin.medical-leaves.index (GET)
 *
 * Controller: App\Http\Controllers\Admin\MedicalLeaveController@index
 *
 * Components on this page:
 * - x-app-layout: Main application layout wrapper
 * - Filter form with status, leave_type, student, from_date, to_date inputs
 * - Data table displaying leave requests with student info, dates, duration, status
 * - Pagination links
 *
 * Required Data variables:
 * - $leaves: Collection of MedicalLeave objects (paginated)
 * - $statuses: Array of available status values
 * - $leaveTypes: Array of available leave type values
 *
 * Dependencies:
 * - Routes: admin.medical-leaves.show, admin.medical-leaves.export, admin.users.show
 * - Helpers: __(), route(), request()
 * - Relationships: MedicalLeave->student (User), MedicalLeave->status
 *
 * @package App\Views\Pages\Admin\MedicalLeaves
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.medical_leaves') }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ __('lms.medical_leaves') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('lms.medical_leaves_description') }}
                    </p>
                </div>
                <a href="{{ route('admin.medical-leaves.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    {{ __('lms.export') }}
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.medical-leaves.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.status') }}</label>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('lms.all_statuses') }}</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ __(ucfirst($status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.leave_type') }}</label>
                            <select name="leave_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('lms.all_types') }}</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type }}" {{ request('leave_type') == $type ? 'selected' : '' }}>
                                        {{ __($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.student') }}</label>
                            <input type="text" name="student" value="{{ request('student') }}" placeholder="{{ __('lms.search_student') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.from_date') }}</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.to_date') }}</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:col-span-5 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                {{ __('lms.filter') }}
                            </button>
                            <a href="{{ route('admin.medical-leaves.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">
                                {{ __('lms.clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Medical Leaves Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.student') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.leave_type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.start_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.end_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.duration') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.submitted') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($leaves as $leave)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.users.show', $leave->student) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $leave->student?->name ?? __('Unknown') }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $leave->student?->user_id ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ __($leave->leave_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $leave->start_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $leave->end_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $leave->duration_days }} {{ __('lms.days') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @switch($leave->status)
                                                @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                                @case('approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                                @case('rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                                @case('withdrawn') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @break
                                            @endswitch
                                        ">
                                            {{ __(ucfirst($leave->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $leave->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.medical-leaves.show', $leave) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ __('lms.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('lms.no_medical_leaves') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($leaves->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $leaves->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
