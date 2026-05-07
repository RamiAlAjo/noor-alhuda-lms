<x-app-layout>
    <x-slot name="title">{{ __('lms.bulk_enrollment') }} - {{ __('lms.admin') }}</x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.enrollments.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_enrollments') }}
                </a>
            </div>

            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('lms.bulk_enrollment') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('lms.bulk_enrollment_description') }}
                </p>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.enrollments.bulk-create') }}" class="border-blue-500 text-blue-600 dark:text-blue-400 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('lms.select_students') }}
                    </a>
                    <a href="{{ route('admin.enrollments.import-csv') }}" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('lms.csv_import') }}
                    </a>
                </nav>
            </div>

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900 border border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-200 rounded">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('errors'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 rounded">
                    <ul class="list-disc list-inside">
                        @foreach(session('errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.enrollments.store-bulk') }}">
                @csrf

                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- Students Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('lms.select_students') }}
                                </h2>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="selectAllStudents()" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ __('lms.select_all') }}
                                    </button>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <button type="button" onclick="deselectAllStudents()" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ __('lms.deselect_all') }}
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <input type="text" id="studentSearch" placeholder="{{ __('lms.search_students') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                            </div>
                        </div>
                        <div class="p-4 max-h-96 overflow-y-auto">
                            @if($students->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                                    {{ __('lms.no_students_found') }}
                                </p>
                            @else
                                <div class="space-y-2" id="studentList">
                                    @foreach($students as $student)
                                        <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer student-item">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 student-checkbox">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $student->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $student->email }} - {{ $student->profile?->student_id ?? '' }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                            <span id="selectedStudentCount" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('lms.students_selected', ['count' => 0]) }}
                            </span>
                        </div>
                    </div>

                    <!-- Course Offerings Selection -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('lms.select_courses') }}
                                </h2>
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="selectAllOfferings()" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ __('lms.select_all') }}
                                    </button>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <button type="button" onclick="deselectAllOfferings()" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ __('lms.deselect_all') }}
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <input type="text" id="offeringSearch" placeholder="{{ __('lms.search_courses') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                            </div>
                        </div>
                        <div class="p-4 max-h-96 overflow-y-auto">
                            @if($offerings->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                                    {{ __('lms.no_course_offerings_found') }}
                                </p>
                            @else
                                <div class="space-y-2" id="offeringList">
                                    @foreach($offerings as $offering)
                                        <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer offering-item">
                                            <input type="checkbox" name="offering_ids[]" value="{{ $offering->id }}" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 offering-checkbox">
                                            <div class="ml-3 flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $offering->course->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $offering->course?->code ?? '' }} - {{ $offering->teacher?->name ?? 'N/A' }}
                                                </div>
                                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                                    {{ $offering->semester->name ?? '' }} | {{ $offering->enrolled_count }}/{{ $offering->capacity }} {{ __('lms.enrolled') }}
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                            <span id="selectedOfferingCount" class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('lms.courses_selected', ['count' => 0]) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('lms.enrollment_options') }}
                    </h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('lms.status') }}
                            </label>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <option value="approved">{{ __('lms.approved') }}</option>
                                <option value="pending">{{ __('lms.pending') }}</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="skip_prerequisites" class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('lms.skip_prerequisites_check') }}
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="skip_capacity_check" class="rounded border-gray-300 dark:border-gray-600 text-blue-600">
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('lms.skip_capacity_check') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition">
                        {{ __('lms.create_enrollments') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Student search
        document.getElementById('studentSearch').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('.student-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(search) ? '' : 'none';
            });
        });

        // Offering search
        document.getElementById('offeringSearch').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            document.querySelectorAll('.offering-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(search) ? '' : 'none';
            });
        });

        // Update counts
        function updateCounts() {
            const studentCount = document.querySelectorAll('.student-checkbox:checked').length;
            const offeringCount = document.querySelectorAll('.offering-checkbox:checked').length;
            document.getElementById('selectedStudentCount').textContent = '{{ __('lms.students_selected', ['count' => 'COUNT']) }}'.replace('COUNT', studentCount);
            document.getElementById('selectedOfferingCount').textContent = '{{ __('lms.courses_selected', ['count' => 'COUNT']) }}'.replace('COUNT', offeringCount);
        }

        document.querySelectorAll('.student-checkbox, .offering-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCounts);
        });

        function selectAllStudents() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = true);
            updateCounts();
        }

        function deselectAllStudents() {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
            updateCounts();
        }

        function selectAllOfferings() {
            document.querySelectorAll('.offering-checkbox').forEach(cb => cb.checked = true);
            updateCounts();
        }

        function deselectAllOfferings() {
            document.querySelectorAll('.offering-checkbox').forEach(cb => cb.checked = false);
            updateCounts();
        }
    </script>
    @endpush
</x-app-layout>
