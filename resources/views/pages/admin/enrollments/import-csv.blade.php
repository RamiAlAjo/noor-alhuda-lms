<x-app-layout>
    <x-slot name="title">{{ __('lms.csv_import') }} - {{ __('lms.admin') }}</x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.enrollments.bulk-create') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    {{ __('lms.back_to_bulk_enrollment') }}
                </a>
            </div>

            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('lms.csv_import') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('lms.csv_import_description') }}
                </p>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.enrollments.bulk-create') }}" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('lms.select_students') }}
                    </a>
                    <a href="{{ route('admin.enrollments.import-csv') }}" class="border-blue-500 text-blue-600 dark:text-blue-400 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
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

            @if(session('import_errors'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 rounded">
                    <p class="font-medium mb-2">{{ __('lms.import_errors') }}:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Instructions -->
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">
                    {{ __('lms.csv_format_instructions') }}
                </h3>
                <ul class="text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                    <li>{{ __('lms.csv_format_instruction1') }}</li>
                    <li>{{ __('lms.csv_format_instruction2') }}</li>
                    <li>{{ __('lms.csv_format_instruction3') }}</li>
                </ul>
                <div class="mt-3">
                    <a href="{{ route('admin.enrollments.template') }}" class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ __('lms.download_template') }}
                    </a>
                </div>
            </div>

            <!-- Expected Format -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ __('lms.expected_format') }}
                    </h3>
                </div>
                <div class="p-4">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300">student_id</th>
                                <th class="px-4 py-2 text-left text-gray-600 dark:text-gray-300">course_code</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400">12345</td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400">CS101</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400">student@example.com</td>
                                <td class="px-4 py-2 text-gray-500 dark:text-gray-400">MATH201</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upload Form -->
            <form method="POST" action="{{ route('admin.enrollments.process-csv-import') }}" enctype="multipart/form-data">
                @csrf

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('lms.semester') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="semester_id" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">{{ __('lms.select_semester') }}</option>
                            @foreach($semesters as $semester)
                                <option value="{{ $semester->id }}" {{ $semester->is_current ? 'selected' : '' }}>
                                    {{ $semester->name }} - {{ $semester->academicYear?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('lms.csv_file') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label for="csv_file" class="relative cursor-pointer rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500">
                                        <span>{{ __('lms.upload_file') }}</span>
                                        <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt" required class="sr-only">
                                    </label>
                                    <p class="pl-1">{{ __('lms.or_drag_and_drop') }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    CSV up to 10MB
                                </p>
                            </div>
                        </div>
                    </div>

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

                <div class="mt-6 flex justify-end">
                    <x-button.submit loading-text="{{ __('Importing...') }}">
                        {{ __('lms.import_enrollments') }}
                    </x-button.submit>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
