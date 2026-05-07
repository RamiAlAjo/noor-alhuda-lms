{{--
    =============================================================================
    CAPACITY DETAIL VIEW
    =============================================================================

    Purpose: Detailed view of a specific course offering's capacity metrics,
    AI predictions, and optimization recommendations.

    Route: admin.capacity.show
    Controller: Admin\CapacityManagementController@show

    Components:
    - Course offering details
    - Current enrollment metrics
    - AI prediction details
    - Capacity optimization recommendations
    - Historical data charts
    - Action buttons for applying recommendations

    Required Data:
    - $offering: CourseOffering model with relationships
    - $metrics: Current capacity metrics
    - $prediction: AI prediction data
    - $recommendations: Optimization recommendations
    - $historicalData: Enrollment history for charts

    =============================================================================
--}}
<x-layouts::app :title="__('Capacity Details - :offering', ['offering' => $offering->course->code . ' ' . $offering->section_name])">

    <!-- Header Section -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ $offering->course->code }} {{ $offering->section_name }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $offering->course->name }}</p>
        </div>
        <div class="flex space-x-3">
            <button type="button" onclick="runPrediction()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                {{ __('Run AI Prediction') }}
            </button>
            <a href="{{ route('admin.capacity.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                {{ __('Back to Overview') }}
            </a>
        </div>
    </div>

    <!-- Offering Details -->
    <div class="mb-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Offering Information') }}</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Course Code') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $offering->course->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Section') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $offering->section_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Department') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $offering->course->department->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Teacher') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $offering->teacher->name ?? __('Unassigned') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Semester') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $offering->semester->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Capacity') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $offering->capacity }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div>
            <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Current Status') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Enrolled Students') }}</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $metrics['enrolled_count'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Available Spots') }}</span>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ ($offering->capacity ?? 0) - ($metrics['enrolled_count'] ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Utilization Rate') }}</span>
                        <span class="text-lg font-semibold {{ ($metrics['utilization_rate'] ?? 0) > 90 ? 'text-red-600' : (($metrics['utilization_rate'] ?? 0) > 70 ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ number_format($metrics['utilization_rate'] ?? 0, 1) }}%
                        </span>
                    </div>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Capacity Status') }}</span>
                            @php
                                $statusClasses = [
                                    'optimal' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'at_risk' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'over_capacity' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    'under_capacity' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                ];
                                $capacityStatus = $this->determineCapacityStatus($offering, $prediction ?? null);
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$capacityStatus] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' }}">
                                {{ ucfirst(str_replace('_', ' ', $capacityStatus)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Prediction Section -->
    @if($prediction)
    <div class="mb-8">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('AI Prediction') }}</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Confidence') }}</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        {{ number_format($prediction['confidence'] * 100, 1) }}%
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($prediction['predicted_enrollment'], 0) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Predicted Enrollment') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($prediction['recommended_capacity'], 0) }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Recommended Capacity') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold {{ $prediction['predicted_enrollment'] > $offering->capacity ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                        {{ number_format(abs($prediction['predicted_enrollment'] - $offering->capacity), 0) }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $prediction['predicted_enrollment'] > $offering->capacity ? __('Over Capacity') : __('Available Spots') }}
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">{{ __('Prediction Factors') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($prediction['factors'] ?? [] as $factor)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $factor['name'] }}</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $factor['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recommendations Section -->
    @if(count($recommendations ?? []) > 0)
    <div class="mb-8">
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('AI Recommendations') }}</h3>
                <button type="button" onclick="applyAllRecommendations()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    {{ __('Apply All') }}
                </button>
            </div>

            <div class="space-y-4">
                @foreach($recommendations as $recommendation)
                    <div class="flex items-start justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $recommendation['title'] }}</h4>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $recommendation['description'] }}</p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Impact') }}:
                                    <span class="font-medium {{ $recommendation['impact'] === 'high' ? 'text-green-600' : ($recommendation['impact'] === 'medium' ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ ucfirst($recommendation['impact']) }}
                                    </span>
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Confidence') }}: {{ number_format($recommendation['confidence'] * 100, 1) }}%
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 flex space-x-2">
                            <button type="button" onclick="applyRecommendation({{ $recommendation['id'] }})" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                                {{ __('Apply') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Enrollment History -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Enrollment History') }}</h3>
            <canvas id="enrollmentHistoryChart" class="w-full h-64"></canvas>
        </div>

        <!-- Capacity Utilization -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Capacity Utilization') }}</h3>
            <canvas id="capacityUtilizationChart" class="w-full h-64"></canvas>
        </div>
    </div>

    <!-- Student List -->
    <div class="mt-8">
        <div class="rounded-lg bg-white shadow dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Enrolled Students') }}</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Student ID') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Enrollment Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($offering->enrollments ?? [] as $enrollment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $enrollment->student->user_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $enrollment->student->profile->first_name ?? '' }} {{ $enrollment->student->profile->last_name ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->student->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $enrollment->created_at->format('M j, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $enrollment->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : ($enrollment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                        {{ ucfirst($enrollment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enrollment history chart
            const enrollmentCtx = document.getElementById('enrollmentHistoryChart').getContext('2d');
            new Chart(enrollmentCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($historicalData['months'] ?? []) !!},
                    datasets: [{
                        label: 'Enrollment Count',
                        data: {!! json_encode($historicalData['enrollments'] ?? []) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
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

            // Capacity utilization chart
            const capacityCtx = document.getElementById('capacityUtilizationChart').getContext('2d');
            new Chart(capacityCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Enrolled', 'Available'],
                    datasets: [{
                        data: [
                            {{ $metrics['enrolled_count'] ?? 0 }},
                            {{ max(0, ($offering->capacity ?? 0) - ($metrics['enrolled_count'] ?? 0)) }}
                        ],
                        backgroundColor: [
                            'rgb(239, 68, 68)',
                            'rgb(34, 197, 94)'
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
        });

        // AI Functions
        function runPrediction() {
            fetch(`{{ url('/admin/capacity/predict') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ offering_id: {{ $offering->id }} })
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

        function applyRecommendation(recommendationId) {
            fetch(`{{ url('/admin/capacity/apply') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    offering_id: {{ $offering->id }},
                    recommendation_id: recommendationId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to apply recommendation: ' + data.message);
                }
            });
        }

        function applyAllRecommendations() {
            fetch(`{{ url('/admin/capacity/apply') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    offering_id: {{ $offering->id }},
                    apply_all: true
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
    </script>

</x-layouts::app>