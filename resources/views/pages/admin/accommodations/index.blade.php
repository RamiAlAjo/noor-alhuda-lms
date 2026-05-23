<?php
/**
 * Admin Accommodations - Index Page
 *
 * Purpose: Manage accommodation types and student accommodations with tabs for types and student assignments
 * Route: admin.accommodations.index (GET)
 * Controller: App\Http\Controllers\Admin\AccommodationController@index
 *
 * Components:
 * - x-app-layout: Main application layout
 * - Tab navigation: Types tab and Student Accommodations tab
 * - Accommodation Types table: CRUD operations for accommodation types
 * - Student Accommodations table: Filter and display student accommodation assignments
 * - Modal form: Create/edit accommodation type
 * - Export button: Export student accommodations to CSV
 * - JavaScript: Modal handling for create/edit type
 *
 * Required Data Variables:
 * - $tab: Current active tab ('types' or 'students')
 * - $types: Collection of AccommodationType models (when tab is 'types')
 * - $accommodations: Paginated collection of StudentAccommodation models (when tab is 'students')
 *
 * Dependencies:
 * - Routes: admin.accommodations.create-student, admin.accommodations.destroy-type, admin.accommodations.export, admin.accommodations.show-student, admin.accommodations.edit-student
 * - Models: AccommodationType, StudentAccommodation, User
 * - Helpers: __(), route(), Str::limit(), request()
 * - Static Methods: AccommodationType::getCategories(), StudentAccommodation::getStatuses()
 */
?>
<x-app-layout>
    <x-slot name="title">{{ __('lms.accommodations') }} - {{ __('lms.admin') }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('lms.accommodations_management') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('lms.manage_accommodations_description') }}
                    </p>
                </div>
                @if($tab === 'students')
                    <a href="{{ route('admin.accommodations.create-student') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('lms.assign_accommodation') }}
                    </a>
                @endif
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.accommodations.index', ['tab' => 'types']) }}" class="{{ $tab === 'types' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('lms.accommodation_types') }}
                    </a>
                    <a href="{{ route('admin.accommodations.index', ['tab' => 'students']) }}" class="{{ $tab === 'students' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('lms.student_accommodations') }}
                    </a>
                </nav>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($tab === 'types')
                <!-- Accommodation Types Tab -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('lms.accommodation_types') }}
                        </h2>
                        <button type="button" onclick="openCreateTypeModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('lms.add_type') }}
                        </button>
                    </div>

                    @if($types->isEmpty())
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('lms.no_accommodation_types') }}
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.name') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.code') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.category') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.students') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($types as $type)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $type->name }}
                                            </div>
                                            @if($type->description)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($type->description, 50) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $type->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ App\Models\AccommodationType::getCategories()[$type->category] ?? $type->category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $type->student_accommodations_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($type->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    {{ __('lms.active') }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                    {{ __('lms.inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button onclick="openEditTypeModal({{ $type->id }}, '{{ addslashes($type->name) }}', '{{ $type->code }}', '{{ $type->category }}', '{{ addslashes($type->description ?? '') }}', {{ $type->requires_documentation ? 'true' : 'false' }}, {{ $type->is_active ? 'true' : 'false' }})" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">
                                                {{ __('lms.edit') }}
                                            </button>
                                            <form method="POST" action="{{ route('admin.accommodations.destroy-type', $type) }}" class="inline" onsubmit="return confirm('{{ __('lms.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">
                                                    {{ __('lms.delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @else
                <!-- Student Accommodations Tab -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4">
                    <form method="GET" class="flex flex-wrap gap-4">
                        <input type="hidden" name="tab" value="students">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('lms.status') }}
                            </label>
                            <select name="status" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <option value="">{{ __('lms.all_statuses') }}</option>
                                @foreach(App\Models\StudentAccommodation::getStatuses() as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('lms.search') }}
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('lms.search_students') }}" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                        <div class="flex items-end">
                             <x-button.submit loading-text="{{ __('Filtering...') }}">
                                 {{ __('lms.filter') }}
                             </x-button.submit>
                        </div>
                    </form>
                </div>

                <div class="flex justify-end mb-4">
                    <a href="{{ route('admin.accommodations.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('lms.export_csv') }}
                    </a>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    @if($accommodations->isEmpty())
                        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                            {{ __('lms.no_student_accommodations') }}
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.student') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.accommodation') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.validity') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.status') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('lms.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($accommodations as $accommodation)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $accommodation->student?->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $accommodation->student?->email ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ $accommodation->accommodationType?->name ?? 'Unknown' }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ App\Models\AccommodationType::getCategories()[$accommodation->accommodationType?->category] ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @if($accommodation->start_date || $accommodation->end_date)
                                                {{ $accommodation->start_date?->format('M d, Y') ?? '∞' }} - {{ $accommodation->end_date?->format('M d, Y') ?? '∞' }}
                                            @else
                                                {{ __('lms.indefinite') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'active' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                                    'expired' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
                                                    'suspended' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                                                    'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$accommodation->status] ?? '' }}">
                                                {{ App\Models\StudentAccommodation::getStatuses()[$accommodation->status] ?? $accommodation->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('admin.accommodations.show-student', $accommodation) }}" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">
                                                {{ __('lms.view') }}
                                            </a>
                                            <a href="{{ route('admin.accommodations.edit-student', $accommodation) }}" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">
                                                {{ __('lms.edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($accommodations->hasPages())
                            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                                {{ $accommodations->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Type Modal -->
    <div id="typeModal" class="fixed inset-0 bg-gray-600/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 id="typeModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('lms.add_accommodation_type') }}
                    </h3>
                </div>
                <form id="typeForm" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('lms.name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="typeName" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('lms.code') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" id="typeCode" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('lms.category') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="category" id="typeCategory" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                @foreach(App\Models\AccommodationType::getCategories() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('lms.description') }}
                            </label>
                            <textarea name="description" id="typeDescription" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"></textarea>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="requires_documentation" id="typeRequiresDoc" class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('lms.requires_documentation') }}
                                </span>
                            </label>
                        </div>
                        <div id="isActiveContainer">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" id="typeIsActive" checked class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('lms.active') }}
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex justify-end space-x-3">
                        <button type="button" onclick="closeTypeModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            {{ __('lms.cancel') }}
                        </button>
                         <x-button.submit loading-text="{{ __('Saving...') }}">
                             {{ __('lms.save') }}
                         </x-button.submit>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let editingTypeId = null;

        function openCreateTypeModal() {
            editingTypeId = null;
            document.getElementById('typeModalTitle').textContent = '{{ __('lms.add_accommodation_type') }}';
            document.getElementById('typeForm').action = '{{ route('admin.accommodations.store-type') }}';
            document.getElementById('typeForm').innerHTML += '<input type="hidden" name="_method" value="POST">';
            document.getElementById('typeName').value = '';
            document.getElementById('typeCode').value = '';
            document.getElementById('typeCategory').value = 'timing';
            document.getElementById('typeDescription').value = '';
            document.getElementById('typeRequiresDoc').checked = false;
            document.getElementById('typeIsActive').checked = true;
            document.getElementById('isActiveContainer').classList.add('hidden');
            document.getElementById('typeModal').classList.remove('hidden');
        }

        function openEditTypeModal(id, name, code, category, description, requiresDoc, isActive) {
            editingTypeId = id;
            document.getElementById('typeModalTitle').textContent = '{{ __('lms.edit_accommodation_type') }}';
            document.getElementById('typeForm').action = '{{ route('admin.accommodations.update-type', ['accommodation_type' => 'ID']) }}'.replace('ID', id);
            document.getElementById('typeForm').innerHTML += '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('typeName').value = name;
            document.getElementById('typeCode').value = code;
            document.getElementById('typeCategory').value = category;
            document.getElementById('typeDescription').value = description;
            document.getElementById('typeRequiresDoc').checked = requiresDoc;
            document.getElementById('typeIsActive').checked = isActive;
            document.getElementById('isActiveContainer').classList.remove('hidden');
            document.getElementById('typeModal').classList.remove('hidden');
        }

        function closeTypeModal() {
            document.getElementById('typeModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
