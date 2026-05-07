{{--
    =============================================================================
    ADMIN USERS BULK IMPORT VIEW
    =============================================================================

    Purpose: Form to bulk import users from CSV or Excel file with role assignment.

    Route: admin.users.import
    Controller: Admin\UserController@import

    Components:
    - Hero header with gradient background
    - Main form with:
      * Role selection (radio buttons for Admin/Teacher/Student)
      * File drop zone for CSV/Excel upload
      * Submit button (Import Users)
      * Back button
    - Sidebar sections:
      * File Format Guide with required/optional columns
      * Download Template button
      * Quick Tips (UTF-8 encoding, date format, etc.)

    JavaScript:
    - File input change handler to show selected file name
    - Drag and drop visual feedback

    Required Data:
    - $roles: Available roles collection

    Dependencies:
    - route('admin.users.process-import') - POST endpoint to process import
    - route('admin.users.index') - Back to users list
    - route('admin.users.export') - Download template
    - accept=".csv,.xlsx,.xls" - Accepted file types

    CSV Format:
    Required: email, password, first_name, middle_name, last_name, family_name, phone, gender, date_of_birth
    Optional: nationality, personal_email, emergency_contact_name, emergency_contact_relationship, address, city, country, postal_code
    Teacher-specific: department_id, years_of_experience
    Student-specific: major_id

    =============================================================================
--}}
<x-layouts::app :title="__('Bulk Import Users')">
    <!-- Hero Header -->
    <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-600 px-8 py-10 shadow-2xl">
        <!-- Decorative elements -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute right-1/3 top-1/2 h-20 w-20 rounded-full bg-pink-500/20 blur-xl"></div>

        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="hidden shrink-0 sm:flex">
                    <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                </div>
                <div class="text-white">
                    <h1 class="text-3xl font-bold tracking-tight">{{ __('Bulk Import Users') }}</h1>
                    <p class="mt-1 max-w-xl text-indigo-100">{{ __('Import multiple users from CSV or Excel file. Automatically generate user IDs and assign roles.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-12">
        <!-- Main Form -->
        <div class="lg:col-span-8">
            <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                <!-- Form Header -->
                <div class="relative overflow-hidden bg-gradient-to-r from-slate-50 to-neutral-50 px-6 py-5 dark:from-neutral-800 dark:to-neutral-700">
                    <div class="absolute inset-0 bg-grid-slate-100 dark:bg-grid-slate-700/50" style="background-image: linear-gradient(to right, rgb(148 163 184 / 0.1) 1px, transparent 1px), linear-gradient(to bottom, rgb(148 163 184 / 0.1) 1px, transparent 1px); background-size: 24px 24px;"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-neutral-900 dark:text-neutral-100">{{ __('Upload File') }}</h2>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Select a CSV or Excel file to import users') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('admin.users.process-import') }}" enctype="multipart/form-data" class="space-y-8" id="import-form">
                        @csrf

                        <!-- Role Selection -->
                        <div>
                            <label class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ __('Select Role for Imported Users') }} <span class="text-red-500">*</span></label>
                            <p class="mb-3 text-xs text-neutral-500 dark:text-neutral-400">{{ __('Choose the role that will be assigned to all imported users') }}</p>
                            <div class="grid grid-cols-3 gap-4">
                                @foreach($roles as $role)
                                <label class="group cursor-pointer">
                                    <input type="radio" name="role" value="{{ $role->name }}" class="peer sr-only" required>
                                    <div class="relative overflow-hidden rounded-xl border-2 border-neutral-200 p-5 transition-all hover:border-indigo-300 hover:shadow-md dark:border-neutral-600 peer-checked:border-indigo-500 peer-checked:ring-2 peer-checked:ring-indigo-500/20 peer-checked:shadow-lg peer-checked:shadow-indigo-500/10">
                                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 opacity-0 transition-opacity peer-checked:opacity-100"></div>
                                        <div class="relative text-center">
                                            @if($role->name === 'admin')
                                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-red-100 text-red-600 transition-transform group-hover:scale-110 dark:bg-red-900/30 dark:text-red-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                            </div>
                                            <span class="block text-sm font-bold text-neutral-700 dark:text-neutral-300">{{ __('Admin') }}</span>
                                            <span class="mt-1 block text-xs text-neutral-500 dark:text-neutral-400">{{ __('Full system access') }}</span>
                                            @elseif($role->name === 'teacher')
                                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-purple-100 text-purple-600 transition-transform group-hover:scale-110 dark:bg-purple-900/30 dark:text-purple-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                </svg>
                                            </div>
                                            <span class="block text-sm font-bold text-neutral-700 dark:text-neutral-300">{{ __('Teacher') }}</span>
                                            <span class="mt-1 block text-xs text-neutral-500 dark:text-neutral-400">{{ __('Course management') }}</span>
                                            @else
                                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-blue-100 text-blue-600 transition-transform group-hover:scale-110 dark:bg-blue-900/30 dark:text-blue-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <span class="block text-sm font-bold text-neutral-700 dark:text-neutral-300">{{ __('Student') }}</span>
                                            <span class="mt-1 block text-xs text-neutral-500 dark:text-neutral-400">{{ __('Course enrollment') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('role')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <!-- File Drop Zone -->
                        <div>
                            <label class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">{{ __('Choose File (CSV/Excel)') }} <span class="text-red-500">*</span></label>
                            <p class="mb-3 text-xs text-neutral-500 dark:text-neutral-400">{{ __('Supported formats: CSV, XLSX. Maximum file size: 10MB') }}</p>

                            <div class="mt-2">
                                <div id="drop-zone" class="group relative cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed border-neutral-300 bg-gradient-to-br from-neutral-50 to-slate-50 p-8 transition-all hover:border-indigo-500 hover:shadow-xl hover:shadow-indigo-500/10 dark:border-neutral-600 dark:from-neutral-800 dark:to-neutral-700 dark:hover:border-indigo-400">
                                    <input type="file" name="file" id="file-input" class="absolute inset-0 z-10 cursor-pointer opacity-0" accept=".csv,.xlsx,.xls" required>

                                    <!-- Default State -->
                                    <div id="drop-default" class="flex flex-col items-center justify-center text-center">
                                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600 transition-all group-hover:scale-110 group-hover:shadow-lg dark:from-indigo-900/50 dark:to-purple-900/50 dark:text-indigo-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </div>
                                        <p class="mb-1 text-lg font-bold text-neutral-700 dark:text-neutral-200">{{ __('Drop your file here') }}</p>
                                        <p class="mb-4 text-neutral-500 dark:text-neutral-400">{{ __('or click to browse') }}</p>
                                        <div class="flex items-center gap-2 rounded-full bg-neutral-100 px-4 py-2 text-xs font-medium text-neutral-600 dark:bg-neutral-700 dark:text-neutral-300">
                                            <span class="rounded bg-indigo-100 px-2 py-1 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400">CSV</span>
                                            <span class="rounded bg-green-100 px-2 py-1 text-green-600 dark:bg-green-900/50 dark:text-green-400">XLSX</span>
                                            <span class="text-neutral-400">•</span>
                                            <span class="text-neutral-500">{{ __('lms.max_file_size') }}</span>
                                        </div>
                                    </div>

                                    <!-- File Selected State -->
                                    <div id="drop-file" class="hidden flex-col items-center justify-center text-center">
                                        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 text-green-600 dark:from-green-900/50 dark:to-emerald-900/50 dark:text-green-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p id="file-name" class="mb-1 text-lg font-bold text-neutral-700 dark:text-neutral-200"></p>
                                        <p class="mb-2 text-sm text-green-600 dark:text-green-400">{{ __('Ready for import!') }}</p>
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700 dark:bg-green-900/50 dark:text-green-400">{{ __('Click to change file') }}</span>
                                    </div>
                                </div>
                            </div>
                            @error('file')
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between border-t border-neutral-200 pt-6 dark:border-neutral-700">
                            <flux:button :href="route('admin.users.index')" variant="subtle">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                {{ __('Back to Users') }}
                            </flux:button>
                            <flux:button type="submit" variant="primary" class="shadow-lg shadow-indigo-500/25">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                {{ __('Import Users') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6 lg:col-span-4">
            <!-- File Format Guide -->
            <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('File Format Guide') }}
                    </h2>
                </div>
                <div class="p-6">
                    <div class="mb-5 rounded-xl bg-gradient-to-br from-indigo-50 to-purple-50 p-4 dark:from-indigo-900/20 dark:to-purple-900/20">
                        <h3 class="mb-3 font-bold text-indigo-900 dark:text-indigo-200">{{ __('Required CSV Columns') }}</h3>
                        <code class="block whitespace-pre-wrap rounded-lg bg-white p-3 text-sm font-mono text-indigo-800 shadow-sm dark:bg-neutral-900 dark:text-indigo-300">email, password, first_name, middle_name, last_name, family_name, phone, gender, date_of_birth</code>
                        <h3 class="mb-3 mt-4 font-bold text-indigo-900 dark:text-indigo-200">{{ __('Optional CSV Columns') }}</h3>
                        <code class="block whitespace-pre-wrap rounded-lg bg-white p-3 text-sm font-mono text-indigo-800 shadow-sm dark:bg-neutral-900 dark:text-indigo-300">nationality, personal_email, emergency_contact_name, emergency_contact_relationship, address, city, country, postal_code</code>
                        <p class="mt-3 text-xs text-neutral-500 dark:text-neutral-400">{{ __('For Teachers: department_id, years_of_experience') }}</p>
                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ __('For Students: major_id') }}</p>
                    </div>

                    <div class="space-y-4">
                        <h3 class="font-bold text-neutral-900 dark:text-neutral-100">{{ __('Instructions') }}</h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('First row must contain column headers') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Email must be unique for each user') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Password will be applied to all users') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('User IDs will be auto-generated') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Download Template -->
            <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                <div class="p-6">
                    <div class="mb-4 flex items-center gap-4 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 p-4 dark:from-amber-900/20 dark:to-orange-900/20">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-neutral-900 dark:text-neutral-100">{{ __('Need a template?') }}</p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ __('Download our CSV template') }}</p>
                        </div>
                    </div>
                    <flux:button :href="route('admin.users.export')" class="w-full justify-center border-2 border-dashed" variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Download Template') }}
                    </flux:button>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="overflow-hidden rounded-xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 shadow-lg dark:border-amber-800/30 dark:from-amber-900/20 dark:to-orange-900/20">
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-3">
                    <h3 class="flex items-center gap-2 font-bold text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        {{ __('Quick Tips') }}
                    </h3>
                </div>
                <div class="p-5">
                    <ul class="space-y-2 text-sm text-amber-800 dark:text-amber-200">
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 text-lg">•</span>
                            <span>{{ __('Use UTF-8 encoding for Arabic characters') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 text-lg">•</span>
                            <span>{{ __('Date format: YYYY-MM-DD') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 text-lg">•</span>
                            <span>{{ __('Phone without country code') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 text-lg">•</span>
                            <span>{{ __('Gender: male or female') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file-input');
            const dropDefault = document.getElementById('drop-default');
            const dropFile = document.getElementById('drop-file');
            const fileName = document.getElementById('file-name');

            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const sizeKB = (file.size / 1024).toFixed(1);
                    fileName.textContent = file.name + ' (' + sizeKB + ' KB)';
                    dropDefault.classList.add('hidden');
                    dropDefault.classList.remove('flex');
                    dropFile.classList.remove('hidden');
                    dropFile.classList.add('flex');
                }
            });
        });
    </script>
</x-layouts::app>
