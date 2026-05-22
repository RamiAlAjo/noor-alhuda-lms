<?php
/**
 * Admin Medical Records - Show Page
 *
 * Purpose: Display detailed medical record for a student
 * Route: admin.medical.show (GET)
 * Controller: App\Http\Controllers\Admin\MedicalController@show
 *
 * Components:
 * - x-layouts::app: Main application layout
 * - Student info header
 * - Medical record details in cards
 * - Edit form for medical record
 *
 * Required Data Variables:
 * - $student: User model with loaded medicalRecord
 *
 * Dependencies:
 * - Routes: admin.medical.index, admin.medical.destroy
 * - Models: User, MedicalRecord
 * - Helpers: __(), route(), old()
 */
?>
<x-layouts::app :title="__('Medical Record - ') . $student->full_name">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $student->full_name }}</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Student Medical Record') }}</p>
        </div>
        <flux:button :href="route('admin.medical.index')" variant="ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('Back to List') }}
        </flux:button>
    </div>

    <!-- Student Info -->
    <div class="mb-6 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                <span class="text-2xl font-bold">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ $student->full_name }}</h2>
                <p class="text-neutral-500 dark:text-neutral-400">{{ $student->email }}</p>
                @if($student->profile)
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">ID: {{ $student->profile->user_id ?? 'N/A' }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Medical Record Form -->
    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
        <h3 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Medical Information') }}</h3>

        <form method="POST" action="{{ route('admin.medical.update', $student) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Medical Info -->
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Blood Type') }}</label>
                    <select name="blood_type" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">
                        <option value="">-- {{ __('Select') }} --</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                            <option value="{{ $type }}" {{ old('blood_type', $student->medicalRecord->blood_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Medical Conditions -->
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Allergies') }}</label>
                <textarea name="allergies" rows="3" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">{{ old('allergies', $student->medicalRecord->allergies ?? '') }}</textarea>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Medical Conditions') }}</label>
                <textarea name="medical_conditions" rows="3" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">{{ old('medical_conditions', $student->medicalRecord->medical_conditions ?? '') }}</textarea>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Current Medications') }}</label>
                <textarea name="current_medications" rows="3" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">{{ old('current_medications', $student->medicalRecord->current_medications ?? '') }}</textarea>
            </div>

            <!-- Emergency Contact -->
            <div class="border-t border-neutral-200 pt-4 dark:border-neutral-700">
                <h4 class="mb-4 text-md font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Emergency Contact') }}</h4>
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Contact Name') }}</label>
                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $student->medicalRecord->emergency_contact_name ?? '') }}" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Phone') }}</label>
                        <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $student->medicalRecord->emergency_contact_phone ?? '') }}" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Relationship') }}</label>
                        <input type="text" name="emergency_contact_relation" value="{{ old('emergency_contact_relation', $student->medicalRecord->emergency_contact_relation ?? '') }}" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white" />
                    </div>
                </div>
            </div>

            <!-- Doctor Info -->
            <div class="border-t border-neutral-200 pt-4 dark:border-neutral-700">
                <h4 class="mb-4 text-md font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Primary Doctor') }}</h4>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Doctor Name') }}</label>
                        <input type="text" name="doctor_name" value="{{ old('doctor_name', $student->medicalRecord->doctor_name ?? '') }}" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Doctor Phone') }}</label>
                        <input type="text" name="doctor_phone" value="{{ old('doctor_phone', $student->medicalRecord->doctor_phone ?? '') }}" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white" />
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700 dark:text-neutral-300">{{ __('Notes') }}</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-neutral-900 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white">{{ old('notes', $student->medicalRecord->notes ?? '') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-between border-t border-neutral-200 pt-4 dark:border-neutral-700">
                <flux:button :href="route('admin.medical.index')" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>

                <x-button.submit loading-text="Saving...">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </x-button.submit>
            </div>
        </form>
    </div>
</x-layouts::app>
