<x-layouts::app :title="__('Edit Medical Profile')">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ __('Edit Medical Profile') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ __('Update your medical information') }}</p>
    </div>

    <!-- Medical Information Form -->
    <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
        <div class="border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Personal Medical Information') }}</h2>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Keep your medical information up to date') }}</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('student.medical.update') }}" class="p-6">
            @csrf
            @method('PUT')

            <!-- Blood Type -->
            <div class="grid gap-4 md:grid-cols-2">
                <flux:select name="blood_type" :label="__('Blood Type')">
                    <flux:select.option value="">{{ __('Select Blood Type') }}</flux:select.option>
                    <flux:select.option value="A+">A+</flux:select.option>
                    <flux:select.option value="A-">A-</flux:select.option>
                    <flux:select.option value="B+">B+</flux:select.option>
                    <flux:select.option value="B-">B-</flux:select.option>
                    <flux:select.option value="AB+">AB+</flux:select.option>
                    <flux:select.option value="AB-">AB-</flux:select.option>
                    <flux:select.option value="O+">O+</flux:select.option>
                    <flux:select.option value="O-">O-</flux:select.option>
                </flux:select>
                <flux:input
                    name="emergency_contact_phone"
                    :label="__('Emergency Contact Phone')"
                    placeholder="+1 234 567 8900"
                    value="{{ old('emergency_contact_phone', $user->medicalRecord?->emergency_contact_phone) }}"
                />
            </div>

            <!-- Emergency Contact -->
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <flux:input
                    name="emergency_contact_name"
                    :label="__('Emergency Contact Name')"
                    placeholder="{{ __('Contact person name') }}"
                    value="{{ old('emergency_contact_name', $user->medicalRecord?->emergency_contact_name) }}"
                />
                <flux:select name="emergency_contact_relationship" :label="__('Relationship')">
                    <flux:select.option value="">{{ __('Select Relationship') }}</flux:select.option>
                    <flux:select.option value="parent" {{ old('emergency_contact_relationship', $user->medicalRecord?->emergency_contact_relationship) == 'parent' ? 'selected' : '' }}>{{ __('Parent') }}</flux:select.option>
                    <flux:select.option value="spouse" {{ old('emergency_contact_relationship', $user->medicalRecord?->emergency_contact_relationship) == 'spouse' ? 'selected' : '' }}>{{ __('Spouse') }}</flux:select.option>
                    <flux:select.option value="sibling" {{ old('emergency_contact_relationship', $user->medicalRecord?->emergency_contact_relationship) == 'sibling' ? 'selected' : '' }}>{{ __('Sibling') }}</flux:select.option>
                    <flux:select.option value="friend" {{ old('emergency_contact_relationship', $user->medicalRecord?->emergency_contact_relationship) == 'friend' ? 'selected' : '' }}>{{ __('Friend') }}</flux:select.option>
                    <flux:select.option value="other" {{ old('emergency_contact_relationship', $user->medicalRecord?->emergency_contact_relationship) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</flux:select.option>
                </flux:select>
            </div>

            <!-- Allergies -->
            <div class="mt-4">
                <flux:textarea
                    name="allergies"
                    :label="__('Allergies')"
                    placeholder="{{ __('List any allergies (medications, food, environmental)') }}"
                    rows="3"
                >{{ old('allergies', $user->medicalRecord?->allergies) }}</flux:textarea>
            </div>

            <!-- Medical Conditions -->
            <div class="mt-4">
                <flux:textarea
                    name="chronic_conditions"
                    :label="__('Medical Conditions')"
                    placeholder="{{ __('List any chronic medical conditions') }}"
                    rows="3"
                >{{ old('chronic_conditions', $user->medicalRecord?->chronic_conditions) }}</flux:textarea>
            </div>

            <!-- Current Medications -->
            <div class="mt-4">
                <flux:textarea
                    name="current_medications"
                    :label="__('Current Medications')"
                    placeholder="{{ __('List any medications you are currently taking') }}"
                    rows="3"
                >{{ old('current_medications', $user->medicalRecord?->current_medications) }}</flux:textarea>
            </div>

            <!-- Submit -->
            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('student.medical.profile') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition">
                    {{ __('Cancel') }}
                </a>
                <flux:button type="submit" variant="primary">
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
