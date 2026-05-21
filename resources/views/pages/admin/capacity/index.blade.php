{{--
    =============================================================================
    CAPACITY MANAGEMENT DASHBOARD VIEW
    =============================================================================

    Purpose: AI-powered capacity management dashboard showing course offerings,
    enrollment predictions, and capacity optimization recommendations.

    Route: admin.capacity.index
    Controller: Admin\CapacityManagementController@index

    Components:
    - Semester selector
    - Capacity overview stats
    - Course offerings table with AI predictions
    - Capacity status indicators
    - Action buttons for predictions and recommendations

    Required Data:
    - $offerings: Collection of course offerings with metrics and predictions
    - $stats: Dashboard statistics
    - $semesterId: Current selected semester ID
    - $semesters: Available semesters

    =============================================================================
--}}
<x-layouts::app :title="__('AI Capacity Management')">

    <!-- Header Section -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('AI Capacity Management') }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('Optimize course capacities with AI-powered predictions') }}</p>
        </div>
        <div class="flex space-x-3">
            <button id="batch-btn" type="button" onclick="runBatchPrediction()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-60">
                <svg id="batch-icon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span id="batch-text">{{ __('Run Batch Prediction') }}</span>
            </button>
        </div>
    </div>

    <!-- Batch Prediction Status Message -->
    <div id="batch-status" class="hidden mb-6 rounded-xl border p-4 text-sm transition-all"></div>

    <!-- Semester Selector -->
    <div class="mb-6">
        <form method="GET" class="flex items-center space-x-4">
            <label for="semester_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Select Semester:') }}</label>
            <select name="semester_id" id="semester_id" onchange="this.form.submit()" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                @foreach($semesters as $semester)
                    <option value="{{ $semester->id }}" {{ $semester->id == $semesterId ? 'selected' : '' }}>
                        {{ $semester->name }} ({{ $semester->start_date->format('M Y') }} - {{ $semester->end_date->format('M Y') }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Offerings') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_offerings'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Optimal Capacity') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['optimal_capacity'] }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('At Risk') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['at_risk'] }}</p>
                </div>
            </div>
        </div>

        {{-- AI Efficiency Score --}}
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('AI Efficiency Score') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['efficiency_score'] ?? 0, 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        @if(($stats['efficiency_score'] ?? 0) >= 80)
                            Excellent utilization
                        @elseif(($stats['efficiency_score'] ?? 0) >= 60)
                            Good utilization
                        @elseif(($stats['efficiency_score'] ?? 0) >= 40)
                            Needs improvement
                        @else
                            Poor utilization
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Over Capacity') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['over_capacity'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- AI Insights Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- AI Recommendations --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <svg class="inline w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    AI Recommendations
                </h3>
            </div>
            <div class="p-6">
                @if(!empty($aiRecommendations ?? []))
                    <div class="space-y-4">
                        @foreach($aiRecommendations ?? [] as $recommendation)
                            <div class="border-l-4 {{ match($recommendation['priority']) {
                                'high' => 'border-red-400',
                                'medium' => 'border-yellow-400',
                                'low' => 'border-green-400',
                                default => 'border-gray-400'
                            } }} pl-4 py-2">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $recommendation['title'] }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $recommendation['description'] }}
                                        </p>
                                        <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="mr-3">Confidence: {{ number_format($recommendation['confidence'] * 100, 0) }}%</span>
                                            <span class="capitalize">{{ $recommendation['priority'] }} priority</span>
                                        </div>
                                    </div>
                                    @if(isset($recommendation['action_required']))
                                        <button type="button" class="ml-3 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                            {{ Str::limit($recommendation['action_required'], 20) }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No AI recommendations at this time</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The system is analyzing your capacity data for optimization opportunities.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- System Health & Performance --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <svg class="inline w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    System Health & Performance
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    {{-- Overall Health Score --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Health</span>
                        <div class="flex items-center">
                            <span class="text-sm font-semibold {{ ($analytics['system_status']['system_health']['score'] ?? 0) >= 80 ? 'text-green-600' : (($analytics['system_status']['system_health']['score'] ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ number_format($analytics['system_status']['system_health']['score'] ?? 0, 1) }}%
                            </span>
                            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400 capitalize">
                                ({{ $analytics['system_status']['system_health']['status'] ?? 'unknown' }})
                            </span>
                        </div>
                    </div>

                    {{-- Data Quality --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Data Quality</span>
                        <span class="text-sm font-semibold {{ ($analytics['system_status']['data_quality_score'] ?? 0) >= 80 ? 'text-green-600' : (($analytics['system_status']['data_quality_score'] ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($analytics['system_status']['data_quality_score'] ?? 0, 1) }}%
                        </span>
                    </div>

                    {{-- Prediction Method --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Prediction Method</span>
                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                            {{ ucfirst(str_replace('_', ' ', $analytics['prediction_accuracy']['method'] ?? 'unknown')) }}
                        </span>
                    </div>

                    {{-- ML Service Status --}}
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">ML Service</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $analytics['system_status']['ml_service_available'] ?? false ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                            {{ $analytics['system_status']['ml_service_available'] ?? false ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>
                </div>

                {{-- Performance Insights --}}
                @if(!empty($analytics['performance_insights']['capacity_utilization']['insights'] ?? []))
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Key Insights</h4>
                        <ul class="space-y-1">
                            @foreach($analytics['performance_insights']['capacity_utilization']['insights'] ?? [] as $insight)
                                <li class="text-xs text-gray-600 dark:text-gray-400 flex items-start">
                                    <svg class="w-3 h-3 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $insight }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Course Offerings Table -->
    <div class="rounded-lg bg-white shadow dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Course Offerings') }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-3 py-3 w-12 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Course') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Teacher') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Enrolled') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Capacity') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('AI Prediction') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                     @foreach($offerings as $offering)
                         <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                             <td class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                 {{ $loop->iteration }}
                             </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $offering['course']['code'] }} - {{ $offering['course']['name'] }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Section {{ $offering['section_name'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $offering['teacher']['name'] ?? __('Unassigned') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $offering['enrolled_count'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $offering['capacity'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($offering['prediction'])
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ number_format($offering['prediction']['predicted_enrollment'], 0) }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ number_format($offering['prediction']['confidence'] * 100, 1) }}% confidence
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('No prediction') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'optimal' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'at_risk' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'over_capacity' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'under_capacity' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$offering['capacity_status']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                                    {{ ucfirst(str_replace('_', ' ', $offering['capacity_status'])) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button type="button" onclick="predictOffering({{ $offering['id'] }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        {{ __('Predict') }}
                                    </button>
                                    <a href="{{ route('admin.capacity.show', $offering['id']) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ __('Details') }}
                                    </a>
                                    @if(count($offering['recommendations']) > 0)
                                        <button type="button" onclick="showRecommendations({{ $offering['id'] }}, {{ json_encode($offering['recommendations']) }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            {{ __('Apply') }}
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Capacity Distribution') }}</h3>
            <canvas id="capacityChart" class="w-full"></canvas>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Prediction Accuracy') }}</h3>
            <canvas id="accuracyChart" class="w-full"></canvas>
        </div>
    </div>

    <script>
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Capacity distribution chart
            const capacityCtx = document.getElementById('capacityChart').getContext('2d');
            new Chart(capacityCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Optimal', 'At Risk', 'Over Capacity', 'Under Capacity'],
                    datasets: [{
                        data: [{{ $stats['optimal_capacity'] }}, {{ $stats['at_risk'] }}, {{ $stats['over_capacity'] }}, {{ $stats['under_capacity'] ?? 0 }}],
                        backgroundColor: [
                            'rgb(34, 197, 94)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(59, 130, 246)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Prediction accuracy chart
            const accuracyCtx = document.getElementById('accuracyChart').getContext('2d');
            new Chart(accuracyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['months'] ?? []) !!},
                    datasets: [{
                        label: 'Prediction Accuracy (%)',
                        data: {!! json_encode($analytics['accuracy'] ?? []) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        });

        // Prediction functions
        function predictOffering(offeringId) {
            fetch(`{{ url('/admin/capacity/predict') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ offering_id: offeringId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Prediction failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while running prediction');
            });
        }

        function runBatchPrediction() {
            const btn = document.getElementById('batch-btn');
            const btnText = document.getElementById('batch-text');
            const btnIcon = document.getElementById('batch-icon');
            const status = document.getElementById('batch-status');
            const semesterId = {{ $semesterId ?? 'null' }};

            if (!semesterId) {
                showBatchStatus('error', 'No semester selected for batch prediction.');
                return;
            }

            // Set loading state
            btn.disabled = true;
            btnText.textContent = '{{ __("Running Batch Prediction...") }}';
            btnIcon.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            `;

            status.className = 'mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-200';
            status.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span>{{ __("Running AI batch predictions for all courses in this semester. This may take a moment...") }}</span>
                </div>
            `;
            status.classList.remove('hidden');

            fetch(`{{ url('/admin/capacity/batch') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ semester_id: semesterId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    status.className = 'mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-200';
                    status.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span><strong>{{ __("Success!") }}</strong> ${data.processed || 0} {{ __("predictions generated and saved.") }} {{ __("Reloading page...") }}</span>
                        </div>
                    `;
                    
                    // Reload after short delay so user can read the message
                    setTimeout(() => {
                        location.reload();
                    }, 1200);
                } else {
                    showBatchStatus('error', data.message || '{{ __("Batch prediction failed for an unknown reason.") }}');
                    resetBatchButton();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showBatchStatus('error', '{{ __("An unexpected error occurred while running batch prediction. Please check the browser console or try again.") }}');
                resetBatchButton();
            });
        }

        function showBatchStatus(type, message) {
            const status = document.getElementById('batch-status');
            const classes = {
                'success': 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-200',
                'error': 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200',
                'info': 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-200'
            };

            status.className = `mb-6 rounded-xl border p-4 text-sm ${classes[type] || classes['error']}`;
            status.innerHTML = `
                <div class="flex items-start">
                    <div class="flex-1">${message}</div>
                    <button onclick="document.getElementById('batch-status').classList.add('hidden')" class="ml-4 text-current opacity-60 hover:opacity-100">×</button>
                </div>
            `;
            status.classList.remove('hidden');
        }

        function resetBatchButton() {
            const btn = document.getElementById('batch-btn');
            const btnText = document.getElementById('batch-text');
            const btnIcon = document.getElementById('batch-icon');

            btn.disabled = false;
            btnText.textContent = '{{ __("Run Batch Prediction") }}';
            btnIcon.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            `;
        }

        function showRecommendations(offeringId, recommendations) {
            // Show recommendations modal or apply directly
            if (confirm('Apply AI recommendations for this course offering?')) {
                fetch(`{{ url('/admin/capacity/apply') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        offering_id: offeringId,
                        recommendations: recommendations
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to apply recommendations: ' + data.message);
                    }
                });
            }
        }
    </script>

</x-layouts::app>