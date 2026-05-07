<?php
/**
 * Admin Academic Standings - Show Page
 *
 * Purpose: Display detailed view of a single academic standing record
 * Route: admin.academic-standings.show (GET)
 * Controller: App\Http\Controllers\Admin\AcademicStandingController@show
 *
 * Components:
 * - x-app-layout: Main application layout
 * - Detail definition list: Display all standing fields
 * - Status badge: Show if standing is active
 * - Deactivate action: Button to deactivate the standing
 *
 * Required Data Variables:
 * - $academicStanding: AcademicStanding model instance
 *
 * Dependencies:
 * - Routes: admin.academic-standings.index, admin.users.show, admin.academic-standings.deactivate
 * - Helpers: __(), route(), number_format()
 * - Relationships: $academicStanding->student (User), $academicStanding->semester, $academicStanding->setter (User)
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.academic_standing_details') }}</x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('admin.academic-standings.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_standings') }}
                </a>
            </div>

            <!-- Academic Standing Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            {{ __('lms.academic_standing_details') }}
                        </h2>
                        @if($academicStanding->is_active)
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ __('lms.active') }}
                            </span>
                        @else
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                {{ __('lms.inactive') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.student') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($academicStanding->student)
                                <a href="{{ route('admin.users.show', $academicStanding->student) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $academicStanding->student?->name ?? __('Unknown Student') }}
                                </a>
                                @else
                                <span class="text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.student_id') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $academicStanding->student?->user_id ?? '-' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.standing') }}</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @switch($academicStanding->standing)
                                        @case('good_standing') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                        @case('probation') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                        @case('suspension') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @break
                                        @case('dismissal') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @endswitch
                                ">
                                    {{ __(ucwords(str_replace('_', ' ', $academicStanding->standing))) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.gpa') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($academicStanding->gpa_at_time, 2) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.cumulative_gpa') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ number_format($academicStanding->cumulative_gpa, 2) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.type') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($academicStanding->standing_type) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.start_date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $academicStanding->start_date?->format('M d, Y') ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.end_date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $academicStanding->end_date?->format('M d, Y') ?? __('lms.no_end_date') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.set_by') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $academicStanding->setter?->name ?? '-' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.set_at') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $academicStanding->set_at?->format('M d, Y H:i') ?? '-' }}
                            </dd>
                        </div>

                        @if($academicStanding->reason)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.reason') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $academicStanding->reason }}</dd>
                        </div>
                        @endif

                        @if($academicStanding->notes)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.notes') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $academicStanding->notes }}</dd>
                        </div>
                        @endif

                        @if($academicStanding->semester)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('lms.semester') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $academicStanding->semester?->name ?? '-' }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Actions -->
                @if($academicStanding->is_active)
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <form method="POST" action="{{ route('admin.academic-standings.deactivate', $academicStanding) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                onclick="return confirm('{{ __('lms.deactivate_warning') }}')">
                                {{ __('lms.deactivate') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

