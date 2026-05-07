<?php
/**
 * Admin Medical Records - Index Page
 *
 * Purpose: View and manage student medical records and profiles
 * Route: admin.medical.index (GET)
 * Controller: App\Http\Controllers\Admin\MedicalController@index
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Search form: Search by name or user ID
 * - Stats cards: Total students count
 * - Medical records table: User ID, name, blood type, emergency contact
 * - View action: Link to detailed medical record
 * - Pagination: Paginated results
 *
 * Required Data Variables:
 * - $students: Paginated collection of User models with medical records
 *
 * Dependencies:
 * - Routes: admin.medical.show
 * - Models: User, UserProfile, MedicalRecord
 * - Helpers: __(), route(), request()
 */
?>
<x-layouts::app :title="__('Medical Records')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Medical Records') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Manage student medical profiles') }}</p>
    </div>

    <!-- Search -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
        <form method="GET" class="flex items-center gap-4">
            <div class="flex-1">
                <flux:input type="text" name="search" placeholder="{{ __('Search by name or user ID...') }}" :value="request('search')" />
            </div>
            <flux:button type="submit" variant="primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                {{ __('Search') }}
            </flux:button>
        </form>
    </div>

    <!-- Stats -->
    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Total Students') }}</p>
                    <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">{{ $students->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('User ID') }}</th>
                        <th class="px-6 py-3">{{ __('Full Name') }}</th>
                        <th class="px-6 py-3">{{ __('Blood Type') }}</th>
                        <th class="px-6 py-3">{{ __('Emergency Contact') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($students as $student)
                    <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                        <td class="px-6 py-4">
                            <span class="font-mono text-neutral-900 dark:text-neutral-100">{{ $student->profile->user_id ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.medical.show', $student) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                {{ $student->full_name }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            @if($student->medicalRecord)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ $student->medicalRecord->blood_type }}
                                </span>
                            @else
                                <span class="text-neutral-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($student->medicalRecord && $student->medicalRecord->emergency_contact_name)
                                <div class="text-neutral-900 dark:text-neutral-100">{{ $student->medicalRecord->emergency_contact_name }}</div>
                                <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ $student->medicalRecord->emergency_contact_phone }}</div>
                            @else
                                <span class="text-neutral-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <flux:button :href="route('admin.medical.show', $student)" size="sm" variant="ghost">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ __('View') }}
                            </flux:button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No students found') }}</h3>
                                <p class="text-neutral-500 dark:text-neutral-400">{{ __('Try adjusting your search criteria') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $students->links() }}
    </div>
</x-layouts::app>
