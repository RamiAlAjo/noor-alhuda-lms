{{--
    =============================================================================
    ADMIN DASHBOARD VIEW
    =============================================================================

    Purpose: Main dashboard for admin users showing overview statistics,
    enrollment trends, revenue charts, and quick access links.

    Route: admin.dashboard
    Controller: Admin\DashboardController@index

    Components:
    - Hero section with key stats (students, teachers, courses, pending enrollments)
    - Charts section (enrollment trends, revenue, user distribution, top courses)
    - Quick actions (add user, add course, manage enrollments, view reports)
    - Recent enrollments table
    - System status panel
    - Quick links panel

    Required Data:
    - $stats: Array with total_students, total_teachers, total_courses, pending_enrollments
    - $enrollmentChartData: Array with labels and total for enrollment chart
    - $revenueChartData: Array with labels and data for revenue chart
    - $courseEnrollmentData: Array with labels and data for course chart
    - $userRolesData: Array with students, teachers, admins counts
    - $recent_enrollments: Collection of recent enrollment records

    =============================================================================
--}}
<x-layouts::app :title="__('Admin Dashboard')">

    <!-- Hero Section -->
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">{{ __('Welcome back, Admin!') }}</h1>
                <p class="mt-2 text-indigo-100">{{ __('Manage your institution from here') }}</p>
            </div>
            <div class="hidden text-8xl opacity-20">🎓</div>
        </div>

        <!-- Quick Stats in Hero -->
        <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-indigo-100">{{ __('Total Students') }}</p>
                <p class="text-2xl font-bold">{{ $stats['total_students'] }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-indigo-100">{{ __('Total Teachers') }}</p>
                <p class="text-2xl font-bold">{{ $stats['total_teachers'] }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-indigo-100">{{ __('Total Courses') }}</p>
                <p class="text-2xl font-bold">{{ $stats['total_courses'] }}</p>
            </div>
            <div class="rounded-xl bg-white/20 backdrop-blur-sm p-4">
                <p class="text-sm text-indigo-100">{{ __('Pending') }}</p>
                <p class="text-2xl font-bold">{{ $stats['pending_enrollments'] }}</p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="mb-6 grid gap-6 lg:grid-cols-2">
        <!-- Enrollment Trends Chart -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Enrollment Trends') }}</h2>
            <div class="relative" style="height: 250px;">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Monthly Revenue') }}</h2>
            <div class="relative" style="height: 250px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        <!-- User Roles Distribution -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('User Distribution') }}</h2>
            <div class="relative" style="height: 250px;">
                <canvas id="userRolesChart"></canvas>
            </div>
        </div>

        <!-- Top Courses -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 lg:col-span-2">
            <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Top Courses by Enrollment') }}</h2>
            <div class="relative" style="height: 250px;">
                <canvas id="courseEnrollmentChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <a href="{{ route('admin.users.create') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-blue-900/20 dark:to-indigo-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Add User') }}</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Create new user') }}</p>
            </div>
        </a>

        <a href="{{ route('admin.courses.create') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-green-900/20 dark:to-emerald-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Add Course') }}</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Create new course') }}</p>
            </div>
        </a>

        <a href="{{ route('admin.enrollments.requests') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-50 to-orange-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-yellow-900/20 dark:to-orange-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Enrollments') }}</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ __('Manage requests') }}</p>
            </div>
        </a>

        <a href="{{ route('admin.reports.dashboard') }}" class="group relative overflow-hidden rounded-xl border border-neutral-200 bg-white p-6 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 opacity-0 transition-opacity group-hover:opacity-100 dark:from-purple-900/20 dark:to-pink-900/20"></div>
            <div class="relative">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Reports') }}</h3>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ __('View analytics') }}</p>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Recent Enrollments -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Recent Enrollments') }}</h2>
                <flux:button size="sm" variant="subtle" :href="route('admin.enrollments.index')">{{ __('View All') }}</flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-neutral-50 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('Student') }}</th>
                            <th class="px-4 py-3">{{ __('Course') }}</th>
                            <th class="px-4 py-3">{{ __('Section') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse($recent_enrollments as $enrollment)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-xs font-medium text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300">
                                        {{ substr($enrollment->student?->first_name ?? 'S', 0, 1) }}{{ substr($enrollment->student?->last_name ?? '', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $enrollment->student?->full_name ?? __('Unknown') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $enrollment->courseOffering?->course?->name }}</td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $enrollment->courseOffering?->section_name }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    @if($enrollment->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($enrollment->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ __($enrollment->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-neutral-500 dark:text-neutral-400">{{ $enrollment->created_at->format('M d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-neutral-300 dark:text-neutral-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="mt-2">{{ __('No recent enrollments') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity & Quick Links -->
        <div class="space-y-6">
            <!-- System Status -->
            <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('System Status') }}</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 items-center rounded-full bg-green-500"></span>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Database') }}</span>
                        </div>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('Connected') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 items-center rounded-full bg-green-500"></span>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Cache') }}</span>
                        </div>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('Active') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 items-center rounded-full bg-green-500"></span>
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">{{ __('Queue') }}</span>
                        </div>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('Running') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-neutral-100">{{ __('Quick Links') }}</h2>
                <div class="space-y-2">
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 rounded-lg p-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Settings') }}
                    </a>
                    <a href="{{ route('admin.announcements.create') }}" class="flex items-center gap-3 rounded-lg p-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        {{ __('Announcements') }}
                    </a>
                    <a href="{{ route('admin.users.import') }}" class="flex items-center gap-3 rounded-lg p-2 text-neutral-600 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ __('Import Users') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // Wait for both DOMContentLoaded and window load to ensure everything is ready
    function initDashboardCharts() {
        console.log('Charts loading...');

        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded, trying to load...');
            // Try to load it dynamically
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
            script.onload = function() {
                console.log('Chart.js loaded dynamically');
                initializeCharts();
            };
            script.onerror = function() {
                console.error('Failed to load Chart.js');
            };
            document.head.appendChild(script);
            return;
        }

        initializeCharts();
    }

    // Try DOMContentLoaded first
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCharts);
    } else {
        // DOM already loaded, try immediately
        initDashboardCharts();
        // Also try on window load as backup
        window.addEventListener('load', initDashboardCharts);
    }

    // Backup: try after a short delay
    setTimeout(initDashboardCharts, 500);

    function initializeCharts() {
        console.log('Initializing charts...');

        // Chart data from server
        const enrollmentLabels = <?php echo json_encode($enrollmentChartData['labels']); ?>;
        const enrollmentData = <?php echo json_encode($enrollmentChartData['total']); ?>;
        const revenueLabels = <?php echo json_encode($revenueChartData['labels']); ?>;
        const revenueData = <?php echo json_encode($revenueChartData['data']); ?>;
        const courseLabels = <?php echo json_encode($courseEnrollmentData['labels']); ?>;
        const courseData = <?php echo json_encode($courseEnrollmentData['data']); ?>;

        // Common chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false
        };

        // Enrollment Trends Chart
        const enrollmentCtx = document.getElementById('enrollmentChart');
        console.log('Enrollment chart element:', enrollmentCtx);
        if (enrollmentCtx && enrollmentLabels.length > 0) {
            try {
                new Chart(enrollmentCtx, {
                    type: 'line',
                    data: {
                        labels: enrollmentLabels,
                        datasets: [{
                            label: 'Enrollments',
                            data: enrollmentData,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: commonOptions
                });
                console.log('Enrollment chart created');
            } catch(e) {
                console.error('Enrollment chart error:', e);
            }
        }

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx && revenueLabels.length > 0) {
            try {
                new Chart(revenueCtx, {
                    type: 'bar',
                    data: {
                        labels: revenueLabels,
                        datasets: [{
                            label: 'Revenue ($ / JOD)',
                            data: revenueData,
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderRadius: 8
                        }]
                    },
                    options: commonOptions
                });
            } catch(e) {
                console.error('Revenue chart error:', e);
            }
        }

        // User Roles Chart
        const userRolesCtx = document.getElementById('userRolesChart');
        if (userRolesCtx) {
            try {
                new Chart(userRolesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Students', 'Teachers', 'Admins'],
                        datasets: [{
                            data: <?php echo json_encode([$userRolesData['students'], $userRolesData['teachers'], $userRolesData['admins']]); ?>,
                            backgroundColor: ['#6366f1', '#22c55e', '#f59e0b']
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            } catch(e) {
                console.error('User roles chart error:', e);
            }
        }

        // Course Enrollment Chart
        const courseCtx = document.getElementById('courseEnrollmentChart');
        if (courseCtx && courseLabels.length > 0) {
            try {
                new Chart(courseCtx, {
                    type: 'bar',
                    data: {
                        labels: courseLabels,
                        datasets: [{
                            label: 'Enrollments',
                            data: courseData,
                            backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'],
                            borderRadius: 8
                        }]
                    },
                    options: { ...commonOptions, indexAxis: 'y' }
                });
            } catch(e) {
                console.error('Course chart error:', e);
            }
        }

        console.log('All charts initialized');
    }
</script>
