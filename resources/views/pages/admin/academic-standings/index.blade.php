<?php
/**
 * Admin Academic Standings - Index Page
 *
 * Purpose: Display paginated list of all academic standings with filtering, sorting, and bulk actions
 * Route: admin.academic-standings.index (GET)
 * Controller: App\Http\Controllers\Admin\AcademicStandingController@index
 *
 * Components:
 * - x-app-layout: Main application layout
 * - Filter form: Filter by standing type, active status, student name
 * - Data table: Display standings with student info, GPA, status
 * - Action buttons: Calculate all, Export, Create new
 *
 * Required Data Variables:
 * - $standings: Paginated collection of AcademicStanding models
 * - $standingOptions: Array of standing type options
 *
 * Dependencies:
 * - Routes: admin.academic-standings.calculate-all, admin.academic-standings.export, admin.academic-standings.create, admin.users.show
 * - Helpers: __(), route(), request()
 * - Relationships: $standing->student (User), $standing->student->user_id
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.academic_standings') }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        {{ __('lms.academic_standings') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('lms.academic_standings_description') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.academic-standings.calculate-all') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition"
                            onclick="return confirm('{{ __('lms.calculate_all_warning') }}')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ __('lms.calculate_all') }}
                        </button>
                    </form>
                    <a href="{{ route('admin.academic-standings.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('lms.export') }}
                    </a>
                    <a href="{{ route('admin.academic-standings.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('lms.add_standing') }}
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('admin.academic-standings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.standing') }}</label>
                            <select name="standing" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('lms.all_standings') }}</option>
                                @foreach($standingOptions as $option)
                                    <option value="{{ $option }}" {{ request('standing') == $option ? 'selected' : '' }}>
                                        {{ __(ucwords(str_replace('_', ' ', $option))) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.status') }}</label>
                            <select name="active" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('lms.all') }}</option>
                                <option value="yes" {{ request('active') == 'yes' ? 'selected' : '' }}>{{ __('lms.active') }}</option>
                                <option value="no" {{ request('active') == 'no' ? 'selected' : '' }}>{{ __('lms.inactive') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('lms.student') }}</label>
                            <input type="text" name="student" value="{{ request('student') }}" placeholder="{{ __('lms.search_student') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                {{ __('lms.filter') }}
                            </button>
                            <a href="{{ route('admin.academic-standings.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">
                                {{ __('lms.clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Academic Standings Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.student') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.standing') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.gpa') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.set_date') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('lms.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($standings as $standing)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($standing->student)
                                        <a href="{{ route('admin.users.show', $standing->student) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $standing->student->name }}
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $standing->student->user_id }}</div>
                                        @else
                                        <span class="text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @switch($standing->standing)
                                                @case('good_standing') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                                @case('probation') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @break
                                                @case('suspension') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @break
                                                @case('dismissal') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                                @default bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                            @endswitch
                                        ">
                                            {{ __(ucwords(str_replace('_', ' ', $standing->standing))) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($standing->gpa_at_time, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ ucfirst($standing->standing_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($standing->is_active)
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ __('lms.active') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                {{ __('lms.inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ $standing->set_at?->format('M d, Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.academic-standings.show', $standing) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ __('lms.view') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('lms.no_academic_standings') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($standings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $standings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
