                    <option value="">{{ __(""Select Relationship"") }}</option>
                    <option value="parent">{{ __("Parent") }}</option>
                    <option value="spouse">{{ __("Spouse") }}</option>
                    <option value="sibling">{{ __("Sibling") }}</option>
                    <option value="friend">{{ __("Friend") }}</option>
                    <option value="other">{{ __("Other") }}</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
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
<label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __(""Blood Type"") }}</label><select name="blood_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">{{ __(""Select Blood Type"") }}</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
</select>
                <flux:input
                    name="emergency_contact_phone"
                    :label="__('Emergency Contact Phone')"
                    placeholder="+1 234 567 8900"
                />
            </div>

            <!-- Emergency Contact -->
            <div class="grid gap-4 md:grid-cols-2 mt-4">
                <flux:input
                    name="emergency_contact_name"
                    :label="__('Emergency Contact Name')"
                    placeholder="{{ __('Contact person name') }}"
                />
<label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __(""Relationship"") }}</label><select name="emergency_contact_relationship" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">{{ __(""Select Relationship"") }}</option>
                    <option value="">{{ __(""Select Blood Type"") }}</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="">{{ __(""Select Relationship"") }}</option>
                    <option value="parent">{{ __("Parent") }}</option>
                    <option value="spouse">{{ __("Spouse") }}</option>
                    <option value="sibling">{{ __("Sibling") }}</option>
                    <option value="friend">{{ __("Friend") }}</option>
                    <option value="other">{{ __("Other") }}</option>
                />
            </div>

            <!-- Medical Conditions -->
            <div class="mt-4">
                <flux:textarea
                    name="chronic_conditions"
                    :label="__('Medical Conditions')"
                    placeholder="{{ __('List any chronic medical conditions') }}"
                    rows="3"
                />
            </div>

            <!-- Current Medications -->
            <div class="mt-4">
                <flux:textarea
                    name="medications"
                    :label="__('Current Medications')"
                    placeholder="{{ __('List any medications you are currently taking') }}"
                    rows="3"
                />
            </div>

            <!-- Submit -->
            <div class="mt-6 flex justify-end">
                <x-button.submit loading-text="Saving...">
                    Save Changes
                </x-button.submit>
            </div>
        </form>
    </div>
</x-layouts::app>

