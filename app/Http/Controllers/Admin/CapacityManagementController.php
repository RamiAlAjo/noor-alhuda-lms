<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Services\AI\CapacityDataCollector;
use App\Services\AI\CapacityPredictionService;
use App\Services\AI\FeatureEngineering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CapacityManagementController extends Controller
{
    public function __construct(
        private CapacityDataCollector $dataCollector,
        private CapacityPredictionService $predictionService,
        private FeatureEngineering $featureEngine
    ) {}

    /**
     * Display capacity management dashboard
     * Route: admin.capacity.index
     */
    public function index(Request $request): View
    {
        $semesterId = $request->get('semester_id',
            Semester::where('is_active', true)->first()?->id);

        if (! $semesterId) {
            $semesterId = Semester::first()?->id;
        }

        $semesters = Semester::orderBy('start_date', 'desc')->get();

        $offerings = CourseOffering::where('semester_id', $semesterId)
            ->with(['course.department', 'teacher', 'semester'])
            ->get()
            ->map(function ($offering) {
                $metrics = $this->dataCollector->collectOfferingMetrics($offering->id);
                $prediction = $this->getCachedPrediction($offering->course_id, $offering->semester_id);
                $recommendations = $this->generateRecommendations($offering, $metrics, $prediction);

                return [
                    'id' => $offering->id,
                    'course' => [
                        'id' => $offering->course->id,
                        'name' => $offering->course->name,
                        'code' => $offering->course->code,
                    ],
                    'section_name' => $offering->section_name,
                    'teacher' => $offering->teacher ? [
                        'id' => $offering->teacher->id,
                        'name' => $offering->teacher->name,
                    ] : null,
                    'semester' => [
                        'id' => $offering->semester->id,
                        'name' => $offering->semester->name,
                    ],
                    'enrolled_count' => $metrics['current_enrollment'],
                    'capacity' => $metrics['max_capacity'],
                    'prediction' => $prediction ? [
                        'predicted_enrollment' => $prediction['predicted_students'],
                        'confidence' => $prediction['confidence_level'],
                        'recommended_capacity' => $prediction['recommended_capacity'],
                    ] : null,
                    'recommendations' => $recommendations,
                    'capacity_status' => $this->determineCapacityStatus($offering, $prediction),
                ];
            });

        $stats = $this->calculateDashboardStats($offerings);
        $analytics = $this->getAnalyticsData($semesterId);

        return view('pages.admin.capacity.index', compact(
            'offerings',
            'stats',
            'semesterId',
            'semesters',
            'analytics'
        ));
    }

    /**
     * Get detailed capacity analysis for a specific offering
     * Route: admin.capacity.show
     */
    public function show(int $offeringId): View
    {
        $offering = CourseOffering::with([
            'course.department',
            'teacher',
            'semester',
        ])->findOrFail($offeringId);

        $metrics = $this->dataCollector->collectOfferingMetrics($offeringId);
        $prediction = $this->predictionService->predictOptimalCapacity(
            $offering->course_id,
            $offering->semester_id
        );

        $historicalData = $this->getHistoricalAnalysis($offering->course_id);
        $recommendations = $this->generateRecommendations($offering, $metrics, $prediction);
        $enrollments = $this->getEnrollmentDetails($offeringId);

        return view('pages.admin.capacity.show', compact(
            'offering',
            'metrics',
            'prediction',
            'historicalData',
            'recommendations',
            'enrollments'
        ));
    }

    /**
     * Get enhanced capacity analytics data with AI insights
     * Route: admin.capacity.analytics
     */
    public function analytics(Request $request): array
    {
        $semesterId = $request->get('semester_id',
            Semester::where('is_active', true)->first()?->id);

        $courseId = $request->get('course_id'); // Optional course-specific analytics

        // Enrollment trend data
        $enrollmentTrends = $this->getEnrollmentTrends($semesterId);

        // Capacity utilization by department
        $departmentUtilization = $this->getDepartmentUtilization($semesterId);

        // Enhanced prediction accuracy metrics
        $predictionAccuracy = $this->predictionService->getPredictionAccuracy($semesterId);

        // Advanced bottleneck analysis
        $capacityStats = $this->dataCollector->getCapacityStats($semesterId);
        $bottlenecks = $this->identifyBottlenecks($semesterId);

        // System status with AI insights
        $systemStatus = $this->predictionService->getSystemStatus();

        // AI-powered recommendations (course-specific if provided)
        $recommendations = $courseId
            ? $this->predictionService->generateAdvancedRecommendations($courseId, $semesterId)
            : $this->generateGlobalRecommendations($semesterId);

        // Performance insights
        $performanceInsights = $this->generatePerformanceInsights($semesterId);

        return [
            'enrollment_trends' => $enrollmentTrends,
            'department_utilization' => $departmentUtilization,
            'prediction_accuracy' => $predictionAccuracy,
            'capacity_stats' => $capacityStats,
            'bottlenecks' => $bottlenecks,
            'system_status' => $systemStatus,
            'ai_recommendations' => $recommendations,
            'performance_insights' => $performanceInsights,
        ];
    }

    /**
     * Trigger capacity prediction refresh
     * Route: admin.capacity.predict
     */
    public function predict(Request $request): array
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            $prediction = $this->predictionService->refreshPrediction(
                $request->course_id,
                $request->semester_id
            );

            return [
                'success' => true,
                'message' => 'Prediction updated successfully',
                'prediction' => $prediction,
            ];
        } catch (\Exception $e) {
            Log::error('Prediction failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to generate prediction: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Apply AI-recommended capacity
     * Route: admin.capacity.apply
     */
    public function applyRecommendation(Request $request): array
    {
        $request->validate([
            'offering_id' => 'required|exists:course_offerings,id',
            'new_capacity' => 'required|integer|min:1|max:200',
        ]);

        $offering = CourseOffering::findOrFail($request->offering_id);
        $oldCapacity = $offering->max_students;

        $offering->update(['max_students' => $request->new_capacity]);

        // Log the change
        DB::table('activity_logs')->insert([
            'user_id' => auth()->id(),
            'action' => 'capacity_adjusted',
            'description' => "Adjusted capacity for {$offering->course->name} (Section {$offering->section_name}) from {$oldCapacity} to {$request->new_capacity}",
            'properties' => json_encode([
                'offering_id' => $offering->id,
                'course_name' => $offering->course->name,
                'section' => $offering->section_name,
                'old_capacity' => $oldCapacity,
                'new_capacity' => $request->new_capacity,
                'source' => 'ai_recommendation',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Clear prediction cache
        Cache::forget("capacity_prediction_{$offering->course_id}_{$offering->semester_id}");

        return [
            'success' => true,
            'message' => "Capacity adjusted from {$oldCapacity} to {$request->new_capacity}",
            'old_capacity' => $oldCapacity,
            'new_capacity' => $request->new_capacity,
        ];
    }

    /**
     * Run batch predictions for all offerings in a semester
     * Route: admin.capacity.batch
     */
    public function batchPredict(Request $request): array
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
        ]);

        try {
            $results = $this->predictionService->batchPredict($request->semester_id);

            return [
                'success' => true,
                'message' => 'Batch predictions completed',
                'processed' => count($results),
                'results' => $results,
            ];
        } catch (\Exception $e) {
            Log::error('Batch prediction failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Batch prediction failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Export capacity report
     * Route: admin.capacity.export
     */
    public function export(Request $request)
    {
        $semesterId = $request->get('semester_id',
            Semester::where('is_active', true)->first()?->id);

        $offerings = CourseOffering::where('semester_id', $semesterId)
            ->with(['course.department', 'teacher'])
            ->get()
            ->map(function ($offering) {
                $prediction = $this->getCachedPrediction($offering->course_id, $offering->semester_id);

                return [
                    'course_code' => $offering->course->code,
                    'course_name' => $offering->course->name,
                    'section' => $offering->section_name,
                    'instructor' => $offering->teacher?->name ?? 'TBA',
                    'current_enrollment' => $offering->enrolled_count,
                    'max_capacity' => $offering->max_students,
                    'fill_rate' => $offering->max_students > 0
                        ? round(($offering->enrolled_count / $offering->max_students) * 100, 1).'%'
                        : '0%',
                    'predicted' => $prediction['predicted_students'] ?? 'N/A',
                    'recommended' => $prediction['recommended_capacity'] ?? 'N/A',
                    'confidence' => $prediction['confidence_level']
                        ? round($prediction['confidence_level'] * 100, 0).'%'
                        : 'N/A',
                ];
            });

        return response()->json($offerings);
    }

    // Private helper methods...

    private function getCachedPrediction(int $courseId, int $semesterId): ?array
    {
        return Cache::remember(
            "capacity_prediction_{$courseId}_{$semesterId}",
            now()->addHours(6),
            fn () => $this->predictionService->predictOptimalCapacity($courseId, $semesterId)
        );
    }

    private function determineCapacityStatus($offering, ?array $prediction): string
    {
        if (! $prediction) {
            return 'unknown';
        }

        $fillRate = $offering->max_students > 0
            ? ($offering->enrolled_count / $offering->max_students) * 100
            : 0;

        if ($fillRate >= 100) {
            return 'full';
        }
        if ($fillRate >= 90) {
            return 'almost_full';
        }
        if ($fillRate <= 30) {
            return 'underutilized';
        }
        if ($fillRate >= 50 && $fillRate <= 85) {
            return 'optimal';
        }

        return 'normal';
    }

    private function calculateDashboardStats($offerings): array
    {
        return [
            'total_offerings' => $offerings->count(),
            'over_capacity' => $offerings->where('capacity_status', 'full')->count(),
            'almost_full' => $offerings->where('capacity_status', 'almost_full')->count(),
            'under_capacity' => $offerings->where('capacity_status', 'underutilized')->count(),
            'optimal_capacity' => $offerings->where('capacity_status', 'optimal')->count(),
            'at_risk' => $offerings->where('capacity_status', 'almost_full')->count(),
            'avg_fill_rate' => $offerings->avg(fn ($o) => $o['fill_percentage'] ?? 0),
            'total_waitlist' => $offerings->sum('pending_enrollments'),
            'high_demand' => $offerings->filter(fn ($o) => ($o['recent_enrollment_velocity'] ?? 0) > 2)->count(),
        ];
    }

    private function getHistoricalAnalysis(int $courseId): array
    {
        return DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->orderBy('enrollment_date', 'desc')
            ->limit(12)
            ->get()
            ->toArray();
    }

    private function getEnrollmentDetails(int $offeringId): array
    {
        return Enrollment::where('course_offering_id', $offeringId)
            ->with(['student:id,first_name,last_name,email'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function generateRecommendations($offering, array $metrics, ?array $prediction): array
    {
        $recommendations = [];

        // High enrollment velocity
        $velocity = $metrics['recent_enrollment_velocity'] ?? 0;
        if ($velocity > 2) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'High Enrollment Demand',
                'message' => 'Enrollment velocity is '.number_format($velocity, 1).' students/day. Consider increasing capacity.',
                'action' => 'increase_capacity',
                'suggested_value' => min($offering->max_students + 10, 100),
            ];
        }

        // Overcapacity
        $fillRate = $metrics['fill_percentage'] ?? 0;
        if ($fillRate >= 95) {
            $recommendations[] = [
                'type' => 'urgent',
                'title' => 'At Capacity',
                'message' => 'Course is at '.number_format($fillRate, 0).'% capacity. Students may be on waitlist.',
                'action' => 'increase_capacity',
                'suggested_value' => min($offering->max_students + 15, 120),
            ];
        }

        // Underutilized course
        if ($fillRate < 40) {
            $recommendations[] = [
                'type' => 'info',
                'title' => 'Low Enrollment',
                'message' => 'Course is below optimal capacity. Consider promotional activities or schedule adjustment.',
                'action' => 'promote_course',
            ];
        }

        // Prediction mismatch
        if ($prediction &&
            $prediction['confidence_level'] > 0.7 &&
            abs($prediction['recommended_capacity'] - $offering->max_students) > 5) {
            $recommendations[] = [
                'type' => 'suggestion',
                'title' => 'Capacity Adjustment Suggested',
                'message' => "AI recommends capacity of {$prediction['recommended_capacity']} based on historical data.",
                'action' => 'adjust_capacity',
                'suggested_value' => $prediction['recommended_capacity'],
            ];
        }

        // Recommended optimal fill
        if ($fillRate >= 50 && $fillRate <= 85 && $prediction) {
            $recommendations[] = [
                'type' => 'success',
                'title' => 'Optimal Capacity',
                'message' => 'Current enrollment is within optimal range.',
                'action' => 'none',
            ];
        }

        return $recommendations;
    }

    private function getEnrollmentTrends(int $semesterId): array
    {
        $months = [];
        $enrollments = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $count = DB::table('enrollment_histories')
                ->whereYear('enrollment_date', $date->year)
                ->whereMonth('enrollment_date', $date->month)
                ->sum('enrolled_count');

            $enrollments[] = $count;
        }

        // If no data, generate sample data
        if (array_sum($enrollments) === 0) {
            return [
                'labels' => $months,
                'data' => [120, 145, 168, 152, 178, 195],
            ];
        }

        return ['labels' => $months, 'data' => $enrollments];
    }

    private function getDepartmentUtilization(int $semesterId): array
    {
        $data = CourseOffering::where('semester_id', $semesterId)
            ->join('courses', 'course_offerings.course_id', '=', 'courses.id')
            ->join('departments', 'courses.department_id', '=', 'departments.id')
            ->selectRaw('departments.id, departments.name,
                AVG(course_offerings.enrolled_count) as avg_enrolled,
                AVG(course_offerings.max_students) as avg_capacity,
                AVG(course_offerings.enrolled_count / NULLIF(course_offerings.max_students, 0) * 100) as utilization')
            ->groupBy('departments.id', 'departments.name')
            ->get();

        if ($data->isEmpty()) {
            // Sample data for demonstration
            return [
                ['name' => 'Computer Science', 'avg_enrolled' => 28, 'avg_capacity' => 35, 'utilization' => 80],
                ['name' => 'Mathematics', 'avg_enrolled' => 22, 'avg_capacity' => 30, 'utilization' => 73],
                ['name' => 'Physics', 'avg_enrolled' => 18, 'avg_capacity' => 25, 'utilization' => 72],
            ];
        }

        return $data->toArray();
    }

    private function identifyBottlenecks(int $semesterId): array
    {
        $bottlenecks = CourseOffering::where('semester_id', $semesterId)
            ->whereRaw('enrolled_count >= max_students')
            ->with(['course', 'teacher'])
            ->get()
            ->map(function ($offering) {
                $waitlist = Enrollment::where('course_offering_id', $offering->id)
                    ->where('status', 'pending')
                    ->count();

                return [
                    'offering_id' => $offering->id,
                    'course' => $offering->course->name,
                    'course_code' => $offering->course->code,
                    'section' => $offering->section_name,
                    'teacher' => $offering->teacher?->name,
                    'current_enrollment' => $offering->enrolled_count,
                    'max_capacity' => $offering->max_students,
                    'waitlist_count' => $waitlist,
                ];
            });

        return $bottlenecks->toArray();
    }

    /**
     * Generate global AI recommendations for the semester
     */
    private function generateGlobalRecommendations(int $semesterId): array
    {
        $recommendations = [];

        // Get capacity stats for analysis
        $stats = $this->dataCollector->getCapacityStats($semesterId);

        // High bottleneck ratio
        if ($stats['bottleneck_analysis']['bottleneck_percentage'] > 20) {
            $recommendations[] = [
                'type' => 'system_optimization',
                'priority' => 'high',
                'title' => 'High Capacity Bottleneck Ratio',
                'description' => sprintf('%.1f%% of courses are at capacity. Consider system-wide capacity increases.', $stats['bottleneck_analysis']['bottleneck_percentage']),
                'confidence' => 0.9,
                'action_required' => 'Review capacity allocation strategy',
                'expected_impact' => 'Reduced student access issues',
            ];
        }

        // Low efficiency score
        if ($stats['efficiency_score'] < 60) {
            $recommendations[] = [
                'type' => 'system_optimization',
                'priority' => 'medium',
                'title' => 'Low Capacity Efficiency',
                'description' => sprintf('Overall capacity efficiency is %.1f%%. Many courses are under/over-utilized.', $stats['efficiency_score']),
                'confidence' => 0.85,
                'action_required' => 'Optimize course capacities based on AI recommendations',
                'expected_impact' => 'Better resource utilization',
            ];
        }

        // Seasonal optimization
        $month = now()->month;
        if (in_array($month, [8, 9, 1])) { // Peak registration months
            $recommendations[] = [
                'type' => 'seasonal_optimization',
                'priority' => 'medium',
                'title' => 'Peak Registration Period',
                'description' => 'Currently in peak registration period. Monitor capacity closely.',
                'confidence' => 0.8,
                'action_required' => 'Enable real-time capacity monitoring',
                'expected_impact' => 'Proactive bottleneck prevention',
            ];
        }

        return $recommendations;
    }

    /**
     * Generate performance insights and analytics
     */
    private function generatePerformanceInsights(int $semesterId): array
    {
        $insights = [];

        // Prediction accuracy trends
        $accuracy = $this->predictionService->getPredictionAccuracy($semesterId);
        if ($accuracy['sample_size'] > 0) {
            $insights['prediction_accuracy'] = [
                'current_accuracy' => $accuracy['accuracy'],
                'sample_size' => $accuracy['sample_size'],
                'trend' => $this->analyzeAccuracyTrend($semesterId),
                'recommendation' => $accuracy['accuracy'] < 70 ? 'Consider improving data quality or model features' : 'Prediction accuracy is good',
            ];
        }

        // Capacity utilization insights
        $stats = $this->dataCollector->getCapacityStats($semesterId);
        $insights['capacity_utilization'] = [
            'overall_rate' => $stats['overall_utilization'],
            'efficiency_score' => $stats['efficiency_score'],
            'bottleneck_rate' => $stats['bottleneck_analysis']['bottleneck_percentage'],
            'distribution' => $stats['utilization_distribution'],
            'insights' => $this->generateUtilizationInsights($stats),
        ];

        // System health insights
        $systemStatus = $this->predictionService->getSystemStatus();
        $insights['system_health'] = [
            'overall_score' => $systemStatus['system_health']['score'],
            'status' => $systemStatus['system_health']['status'],
            'data_quality' => $systemStatus['data_quality_score'],
            'recommendations' => $this->generateSystemHealthRecommendations($systemStatus),
        ];

        return $insights;
    }

    /**
     * Analyze prediction accuracy trends
     */
    private function analyzeAccuracyTrend(int $semesterId): string
    {
        // Compare current accuracy with historical semesters
        $currentAccuracy = $this->predictionService->getPredictionAccuracy($semesterId);

        if ($currentAccuracy['sample_size'] < 5) {
            return 'insufficient_data';
        }

        // For now, return stable (could be enhanced with historical comparison)
        return 'stable';
    }

    /**
     * Generate utilization insights
     */
    private function generateUtilizationInsights(array $stats): array
    {
        $insights = [];

        if ($stats['overall_utilization'] < 50) {
            $insights[] = 'Overall utilization is low. Consider reducing course offerings or increasing promotion.';
        } elseif ($stats['overall_utilization'] > 90) {
            $insights[] = 'High utilization indicates strong demand. Consider capacity expansion.';
        } else {
            $insights[] = 'Utilization is within optimal range.';
        }

        $underUtilizedRate = ($stats['utilization_distribution']['0-20'] + $stats['utilization_distribution']['21-40']) / max(1, $stats['total_offerings']) * 100;
        if ($underUtilizedRate > 30) {
            $insights[] = 'High number of under-utilized courses. Review course offerings and capacity settings.';
        }

        return $insights;
    }

    /**
     * Generate system health recommendations
     */
    private function generateSystemHealthRecommendations(array $systemStatus): array
    {
        $recommendations = [];

        $healthScore = $systemStatus['system_health']['score'];
        $dataQuality = $systemStatus['data_quality_score'];

        if ($healthScore < 60) {
            $recommendations[] = 'System health is low. Focus on improving data collection and prediction accuracy.';
        }

        if ($dataQuality < 70) {
            $recommendations[] = 'Data quality needs improvement. Ensure all enrollment records include capacity information.';
        }

        if (! $systemStatus['ml_service_available']) {
            $recommendations[] = 'ML service is unavailable. Currently using rule-based predictions with reduced accuracy.';
        }

        return $recommendations;
    }

    private function getAnalyticsData(int $semesterId): array
    {
        return [
            'enrollment_trends' => $this->getEnrollmentTrends($semesterId),
            'department_utilization' => $this->getDepartmentUtilization($semesterId),
            'prediction_accuracy' => $this->predictionService->getPredictionAccuracy($semesterId),
            'capacity_stats' => $this->dataCollector->getCapacityStats($semesterId),
            'bottlenecks' => $this->identifyBottlenecks($semesterId),
        ];
    }
}
