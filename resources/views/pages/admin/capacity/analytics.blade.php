{{--
    =============================================================================
    CAPACITY ANALYTICS VIEW
    =============================================================================

    Purpose: Detailed analytics and insights for capacity management
    with advanced AI predictions and trend analysis.

    Route: admin.capacity.analytics
    Controller: Admin\CapacityManagementController@analytics

    Components:
    - Performance metrics overview
    - Prediction accuracy trends
    - Capacity utilization charts
    - Department-wise analysis
    - Export functionality

    Required Data:
    - $analytics: Analytics data with trends and metrics
    - $predictions: Prediction accuracy data
    - $departments: Department-wise capacity analysis

    =============================================================================
--}}
<x-layouts::app :title="__('Capacity Analytics')">

    <!-- Header Section -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('Capacity Analytics') }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('Advanced AI insights and performance metrics') }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.capacity.export') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ __('Export Report') }}
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Avg Prediction Accuracy') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($analytics['avg_accuracy'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Capacity Utilization') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($analytics['avg_utilization'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('AI Predictions Made') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $analytics['total_predictions'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Recommendations Applied') }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $analytics['applied_recommendations'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="mb-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Prediction Accuracy Over Time -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Prediction Accuracy Trend') }}</h3>
            <canvas id="accuracyTrendChart" class="w-full h-64"></canvas>
        </div>

        <!-- Capacity Utilization Distribution -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Capacity Utilization Distribution') }}</h3>
            <canvas id="utilizationChart" class="w-full h-64"></canvas>
        </div>
    </div>

    <!-- Department Analysis -->
    <div class="mb-8">
        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Department-wise Analysis') }}</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Department') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Courses') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Avg Utilization') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Prediction Accuracy') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Risk Level') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($departments ?? [] as $department)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $department['name'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $department['courses_count'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ number_format($department['avg_utilization'], 1) }}%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white">{{ number_format($department['prediction_accuracy'], 1) }}%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $riskClasses = [
                                            'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'high' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $riskClasses[$department['risk_level']] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($department['risk_level']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Top Performing Predictions') }}</h3>
            <div class="space-y-3">
                @foreach($analytics['top_predictions'] ?? [] as $prediction)
                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $prediction['course_code'] }}</div>
                            <div class="text-gray-500 dark:text-gray-400">{{ $prediction['accuracy'] }}% accuracy</div>
                        </div>
                        <div class="text-green-600 dark:text-green-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Areas for Improvement') }}</h3>
            <div class="space-y-3">
                @foreach($analytics['improvement_areas'] ?? [] as $area)
                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $area['title'] }}</div>
                            <div class="text-gray-500 dark:text-gray-400">{{ $area['description'] }}</div>
                        </div>
                        <div class="text-blue-600 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('AI Model Performance') }}</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Model Accuracy</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($analytics['model_accuracy'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1 dark:bg-gray-700">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $analytics['model_accuracy'] ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Data Quality</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($analytics['data_quality'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1 dark:bg-gray-700">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $analytics['data_quality'] ?? 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Prediction Confidence</span>
                        <span class="text-gray-900 dark:text-white">{{ number_format($analytics['avg_confidence'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1 dark:bg-gray-700">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $analytics['avg_confidence'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Accuracy trend chart
            const accuracyCtx = document.getElementById('accuracyTrendChart').getContext('2d');
            new Chart(accuracyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['accuracy_trend']['months'] ?? []) !!},
                    datasets: [{
                        label: 'Prediction Accuracy (%)',
                        data: {!! json_encode($analytics['accuracy_trend']['accuracy'] ?? []) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Utilization distribution chart
            const utilizationCtx = document.getElementById('utilizationChart').getContext('2d');
            new Chart(utilizationCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($analytics['utilization_distribution']['ranges'] ?? []) !!},
                    datasets: [{
                        label: 'Number of Courses',
                        data: {!! json_encode($analytics['utilization_distribution']['counts'] ?? []) !!},
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(147, 51, 234, 0.8)'
                        ],
                        borderColor: [
                            'rgb(34, 197, 94)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)',
                            'rgb(59, 130, 246)',
                            'rgb(147, 51, 234)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

</x-layouts::app>