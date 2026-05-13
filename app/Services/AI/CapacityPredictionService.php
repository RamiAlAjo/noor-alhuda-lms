<?php

namespace App\Services\AI;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Semester;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CapacityPredictionService
{
    private string $mlServiceUrl;

    private string $mlApiKey;

    private FeatureEngineering $featureEngine;

    public function __construct(FeatureEngineering $featureEngine)
    {
        $this->mlServiceUrl = config('services.ml_api.url', 'http://localhost:8000');
        $this->mlApiKey = config('services.ml_api.key', '');
        $this->featureEngine = $featureEngine;
    }

    /**
     * Predict optimal capacity for a course offering
     */
    public function predictOptimalCapacity(int $courseId, int $semesterId): array
    {
        // Try to get cached prediction first
        $cacheKey = "capacity_prediction_{$courseId}_{$semesterId}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        $features = $this->featureEngine->generateFeatures($courseId, $semesterId);

        // Try ML prediction first, fallback to enhanced rule-based prediction
        try {
            $prediction = $this->callMLService('/predict/capacity', $features);

            $result = [
                'predicted_students' => (int) $prediction['predicted_enrollment'],
                'recommended_capacity' => $this->calculateRecommendedCapacity($prediction),
                'minimum_viable' => (int) ($prediction['minimum_students'] ?? 10),
                'maximum_optimal' => (int) ($prediction['maximum_students'] ?? 50),
                'confidence_level' => (float) ($prediction['confidence'] ?? 0.75),
                'method' => 'ml_prediction',
                'feature_importance' => $prediction['feature_importance'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::warning('ML prediction failed, using enhanced rule-based: '.$e->getMessage());

            // Enhanced fallback to rule-based prediction with better algorithms
            $result = $this->enhancedRuleBasedPrediction($courseId, $semesterId, $features);
        }

        // Cache the prediction
        Cache::put($cacheKey, $result, now()->addMinutes(30));

        return $result;
    }

    /**
     * Call external ML service
     */
    private function callMLService(string $endpoint, array $features): array
    {
        $response = Http::timeout(config('services.ml_api.timeout', 2))
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->mlApiKey,
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->mlServiceUrl}{$endpoint}", [
                'features' => $features,
                'model_version' => config('services.ml_api.model_version', '1.0.0'),
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('ML service returned: '.$response->status());
        }

        return $response->json();
    }

    /**
     * Enhanced rule-based prediction with statistical analysis and trend detection
     */
    private function enhancedRuleBasedPrediction(int $courseId, int $semesterId, array $features): array
    {
        $course = Course::findOrFail($courseId);
        $semester = Semester::find($semesterId);

        // Advanced base capacity calculation with multiple factors
        $baseCapacity = $this->calculateAdvancedBaseCapacity($course, $features);

        // Statistical analysis of historical data
        $historicalStats = $this->performHistoricalAnalysis($courseId);

        // Apply seasonal and temporal adjustments
        $temporalAdjustment = $this->calculateTemporalAdjustment($semesterId, $courseId);

        // Trend analysis for enrollment velocity
        $trendMultiplier = $this->analyzeEnrollmentTrends($courseId);

        // Department-specific adjustments
        $departmentAdjustment = $this->getDepartmentAdjustment($course->department_id ?? null);

        // Calculate predicted students with all factors
        $predictedStudents = $this->calculatePredictedEnrollment(
            $baseCapacity,
            $historicalStats,
            $temporalAdjustment,
            $trendMultiplier,
            $departmentAdjustment
        );

        // Calculate confidence based on data quality and consistency
        $confidence = $this->calculatePredictionConfidence($historicalStats, $features);

        // Generate feature importance for transparency
        $featureImportance = $this->generateFeatureImportance($features, $historicalStats);

        return [
            'predicted_students' => max($predictedStudents, $historicalStats['min'] ?? 5),
            'recommended_capacity' => $baseCapacity,
            'minimum_viable' => (int) max(5, floor($baseCapacity * 0.3)),
            'maximum_optimal' => (int) min(120, ceil($baseCapacity * 1.4)),
            'confidence_level' => $confidence,
            'method' => 'enhanced_rule_based',
            'feature_importance' => $featureImportance,
        ];
    }

    /**
     * Advanced base capacity calculation considering multiple course factors
     */
    private function calculateAdvancedBaseCapacity(Course $course, array $features): int
    {
        // Start with year level base
        $baseCapacity = $this->getBaseCapacityByYearLevel($course->year_level);

        // Course type adjustments
        if ($course->lab_hours > 0) {
            // Lab courses need smaller groups, but consider lab equipment constraints
            $labRatio = $course->lab_hours / ($course->theory_hours + $course->lab_hours);
            $baseCapacity = (int) ($baseCapacity * (1 - $labRatio * 0.4));
        }

        // Theory hours adjustment - more theory hours might need smaller groups for better interaction
        if ($course->theory_hours >= 4) {
            $baseCapacity = (int) ($baseCapacity * 0.85);
        } elseif ($course->theory_hours >= 3) {
            $baseCapacity = (int) ($baseCapacity * 0.9);
        }

        // Credits adjustment - higher credit courses might have different dynamics
        if ($course->credits >= 4) {
            $baseCapacity = (int) ($baseCapacity * 0.95);
        }

        // Department-specific base adjustments
        $departmentMultiplier = $this->getDepartmentCapacityMultiplier($course->department_id ?? null);
        $baseCapacity = (int) ($baseCapacity * $departmentMultiplier);

        return max(10, min(50, $baseCapacity)); // Ensure reasonable bounds
    }

    /**
     * Perform comprehensive historical analysis
     */
    private function performHistoricalAnalysis(int $courseId): array
    {
        $historicalData = DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->orderBy('enrollment_date', 'desc')
            ->take(12) // Last 12 months of data
            ->get();

        if ($historicalData->isEmpty()) {
            return [
                'average' => 0,
                'median' => 0,
                'max' => 0,
                'min' => 0,
                'std_dev' => 0,
                'trend' => 0,
                'count' => 0,
                'fill_rate_avg' => 0,
            ];
        }

        $enrollments = $historicalData->pluck('enrolled_count')->toArray();
        $maxCapacities = $historicalData->pluck('max_capacity')->filter()->toArray();

        $average = array_sum($enrollments) / count($enrollments);
        $median = $this->calculateMedian($enrollments);
        $max = max($enrollments);
        $min = min($enrollments);
        $stdDev = $this->calculateStandardDeviation($enrollments, $average);

        // Calculate trend (simple linear regression slope)
        $trend = $this->calculateTrend($enrollments);

        // Calculate average fill rate
        $fillRates = [];
        foreach ($enrollments as $i => $enrollment) {
            if (isset($maxCapacities[$i]) && $maxCapacities[$i] > 0) {
                $fillRates[] = ($enrollment / $maxCapacities[$i]) * 100;
            }
        }
        $fillRateAvg = ! empty($fillRates) ? array_sum($fillRates) / count($fillRates) : 0;

        return [
            'average' => $average,
            'median' => $median,
            'max' => $max,
            'min' => $min,
            'std_dev' => $stdDev,
            'trend' => $trend,
            'count' => count($enrollments),
            'fill_rate_avg' => $fillRateAvg,
        ];
    }

    /**
     * Calculate temporal adjustments based on semester phase and historical patterns
     */
    private function calculateTemporalAdjustment(int $semesterId, int $courseId): float
    {
        $semester = Semester::find($semesterId);
        if (! $semester) {
            return 1.0;
        }

        $phase = $this->featureEngine->getSemesterPhase($semesterId);

        // Base phase multipliers
        $phaseMultiplier = match ($phase) {
            'pre_registration' => 1.05,  // Slight increase during pre-registration
            'registration' => 1.15,      // Higher during active registration
            'early' => 1.0,              // Normal during early semester
            'mid' => 0.95,               // Slight decrease mid-semester
            'late' => 0.9,               // Lower late in semester
            default => 1.0,
        };

        // Seasonal adjustment based on month
        $month = now()->month;
        $seasonalMultiplier = $this->getSeasonalMultiplier($month);

        // Course-specific temporal patterns
        $courseTemporalMultiplier = $this->getCourseTemporalPattern($courseId, $phase);

        return $phaseMultiplier * $seasonalMultiplier * $courseTemporalMultiplier;
    }

    /**
     * Analyze enrollment trends and velocity
     */
    private function analyzeEnrollmentTrends(int $courseId): float
    {
        // Get recent enrollment velocity (last 30 days)
        $recentVelocity = $this->featureEngine->calculateEnrollmentVelocity($courseId);

        // Get trend from historical data
        $historicalTrend = $this->featureEngine->getHistoricalTrend($courseId);

        // Combine velocity and trend for multiplier
        $velocityMultiplier = 1 + ($recentVelocity * 0.1); // 10% adjustment per student/day
        $trendMultiplier = 1 + ($historicalTrend * 0.05);  // 5% adjustment per trend unit

        return min(1.5, max(0.7, $velocityMultiplier * $trendMultiplier)); // Bound between 0.7 and 1.5
    }

    /**
     * Get department-specific capacity adjustments
     */
    private function getDepartmentAdjustment(?int $departmentId): float
    {
        if (! $departmentId) {
            return 1.0;
        }

        // Get department's average enrollment patterns
        $deptStats = $this->featureEngine->getDepartmentStats($departmentId);

        if ($deptStats['avg_enrollment'] > 0) {
            // Adjust based on whether this department typically has higher/lower enrollment
            $globalAvg = 25; // Assume global average
            $adjustment = $deptStats['avg_enrollment'] / $globalAvg;

            return min(1.3, max(0.8, $adjustment));
        }

        return 1.0;
    }

    /**
     * Calculate final predicted enrollment with all factors
     */
    private function calculatePredictedEnrollment(
        int $baseCapacity,
        array $historicalStats,
        float $temporalAdjustment,
        float $trendMultiplier,
        float $departmentAdjustment
    ): int {
        // Start with historical average if available
        if ($historicalStats['count'] >= 3) {
            $predicted = $historicalStats['median']; // Use median for robustness
        } else {
            $predicted = $baseCapacity * 0.8; // Conservative estimate for new courses
        }

        // Apply adjustments
        $predicted *= $temporalAdjustment;
        $predicted *= $trendMultiplier;
        $predicted *= $departmentAdjustment;

        // Apply fill rate adjustment
        if ($historicalStats['fill_rate_avg'] > 0) {
            $fillRateAdjustment = $historicalStats['fill_rate_avg'] / 100;
            $predicted *= $fillRateAdjustment;
        }

        return (int) ceil($predicted);
    }

    /**
     * Calculate prediction confidence based on data quality
     */
    private function calculatePredictionConfidence(array $historicalStats, array $features): float
    {
        $confidence = 0.5; // Base confidence

        // Historical data quality
        if ($historicalStats['count'] >= 6) {
            $confidence += 0.2;
        } elseif ($historicalStats['count'] >= 3) {
            $confidence += 0.1;
        }

        // Data consistency (lower std dev = higher confidence)
        if ($historicalStats['std_dev'] < 5) {
            $confidence += 0.15;
        } elseif ($historicalStats['std_dev'] < 10) {
            $confidence += 0.1;
        }

        // Feature completeness
        $featureCount = count(array_filter($features, fn ($v) => $v !== null && $v !== 0));
        $featureCompleteness = $featureCount / count($features);
        $confidence += $featureCompleteness * 0.1;

        return min(0.95, max(0.3, $confidence));
    }

    /**
     * Generate feature importance for transparency
     */
    private function generateFeatureImportance(array $features, array $historicalStats): array
    {
        $importance = [];

        // Historical data is most important
        if ($historicalStats['count'] > 0) {
            $importance['historical_average'] = 0.3;
            $importance['historical_trend'] = 0.2;
            $importance['historical_consistency'] = 0.1;
        }

        // Course characteristics
        $importance['course_year_level'] = 0.15;
        $importance['course_credits'] = 0.1;
        $importance['course_lab_hours'] = 0.05;

        // Temporal factors
        $importance['semester_phase'] = 0.05;
        $importance['seasonal_adjustment'] = 0.03;
        $importance['department_factor'] = 0.02;

        return $importance;
    }

    /**
     * Helper: Calculate median of array
     */
    private function calculateMedian(array $values): float
    {
        sort($values);
        $count = count($values);
        $middle = floor($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return $values[$middle];
    }

    /**
     * Helper: Calculate standard deviation
     */
    private function calculateStandardDeviation(array $values, float $mean): float
    {
        if (count($values) < 2) {
            return 0;
        }

        $variance = array_sum(array_map(fn ($v) => pow($v - $mean, 2), $values)) / (count($values) - 1);

        return sqrt($variance);
    }

    /**
     * Helper: Calculate trend using simple linear regression
     */
    private function calculateTrend(array $values): float
    {
        $count = count($values);
        if ($count < 2) {
            return 0;
        }

        $x = range(0, $count - 1);
        $xMean = array_sum($x) / $count;
        $yMean = array_sum($values) / $count;

        $numerator = 0;
        $denominator = 0;

        for ($i = 0; $i < $count; $i++) {
            $numerator += ($x[$i] - $xMean) * ($values[$i] - $yMean);
            $denominator += pow($x[$i] - $xMean, 2);
        }

        return $denominator > 0 ? $numerator / $denominator : 0;
    }

    /**
     * Helper: Get seasonal multiplier based on month
     */
    private function getSeasonalMultiplier(int $month): float
    {
        // Higher enrollment in fall (8-10), spring (1-3), lower in summer (6-7)
        return match (true) {
            in_array($month, [8, 9, 10]) => 1.1,  // Fall peak
            in_array($month, [1, 2, 3]) => 1.05,   // Spring
            in_array($month, [6, 7]) => 0.9,       // Summer low
            default => 1.0,
        };
    }

    /**
     * Helper: Get course-specific temporal patterns
     */
    private function getCourseTemporalPattern(int $courseId, string $phase): float
    {
        // Could be enhanced with course-specific historical patterns
        // For now, return 1.0
        return 1.0;
    }

    /**
     * Helper: Get department capacity multiplier
     */
    private function getDepartmentCapacityMultiplier(?int $departmentId): float
    {
        if (! $departmentId) {
            return 1.0;
        }

        // Department-specific capacity preferences
        // This could be made configurable
        return match ($departmentId) {
            // Add specific department adjustments here
            default => 1.0,
        };
    }

    /**
     * Fallback rule-based prediction when ML service is unavailable (kept for backward compatibility)
     */
    private function ruleBasedPrediction(int $courseId, int $semesterId): array
    {
        return $this->enhancedRuleBasedPrediction($courseId, $semesterId, []);
    }

    /**
     * Calculate recommended capacity with safety margins
     */
    private function calculateRecommendedCapacity(array $prediction): int
    {
        $predicted = $prediction['predicted_enrollment'];
        $confidence = $prediction['confidence'] ?? 0.75;

        // Add buffer based on confidence - higher confidence = smaller buffer
        $buffer = $confidence > 0.85 ? 1.10 : ($confidence > 0.7 ? 1.15 : 1.20);

        return (int) ceil($predicted * $buffer);
    }

    /**
     * Get base capacity by year level
     */
    private function getBaseCapacityByYearLevel(?int $yearLevel): int
    {
        return match ($yearLevel) {
            1 => 35,
            2 => 30,
            3 => 25,
            4 => 20,
            default => 30,
        };
    }

    /**
     * Batch predict for all courses in a semester
     */
    public function batchPredict(int $semesterId): array
    {
        $offerings = CourseOffering::where('semester_id', $semesterId)
            ->where('is_active', true)
            ->with('course')
            ->get();

        $results = [];

        foreach ($offerings as $offering) {
            $prediction = $this->predictOptimalCapacity(
                $offering->course_id,
                $semesterId
            );

            $results[] = [
                'offering_id' => $offering->id,
                'course_id' => $offering->course_id,
                'course_code' => $offering->course->code,
                'course_name' => $offering->course->name,
                'section' => $offering->section_name,
                'current_enrollment' => $offering->enrolled_count,
                'current_capacity' => $offering->max_students,
                'prediction' => $prediction,
            ];

            // Save prediction to database
            $this->savePrediction($offering->course_id, $semesterId, $prediction);
        }

        return $results;
    }

    /**
     * Save prediction to database
     */
    private function savePrediction(int $courseId, int $semesterId, array $prediction): void
    {
        \Illuminate\Support\Facades\DB::table('capacity_predictions')->updateOrInsert(
            [
                'course_id' => $courseId,
                'semester_id' => $semesterId,
                'prediction_horizon' => 'semester_start',
            ],
            [
                'predicted_students' => $prediction['predicted_students'],
                'recommended_capacity' => $prediction['recommended_capacity'],
                'minimum_viable' => $prediction['minimum_viable'],
                'maximum_optimal' => $prediction['maximum_optimal'],
                'confidence_level' => $prediction['confidence_level'],
                'feature_importance' => $prediction['feature_importance'] ? json_encode($prediction['feature_importance']) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Get prediction history for a course
     */
    public function getPredictionHistory(int $courseId, int $limit = 12): array
    {
        return \Illuminate\Support\Facades\DB::table('capacity_predictions')
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get comparison of prediction vs actual for past semesters
     */
    public function getPredictionAccuracy(int $semesterId): array
    {
        $predictions = \Illuminate\Support\Facades\DB::table('capacity_predictions')
            ->where('semester_id', $semesterId)
            ->get();

        if ($predictions->isEmpty()) {
            return [
                'accuracy' => null,
                'sample_size' => 0,
                'mape' => null,
                'bias' => null,
            ];
        }

        $errors = [];
        $biases = [];

        foreach ($predictions as $prediction) {
            $actual = CourseOffering::where('course_id', $prediction->course_id)
                ->where('semester_id', $prediction->semester_id)
                ->value('enrolled_count');

            if ($actual && $prediction->predicted_students > 0) {
                $error = abs($actual - $prediction->predicted_students) / $prediction->predicted_students;
                $errors[] = $error;
                $biases[] = ($actual - $prediction->predicted_students) / $prediction->predicted_students;
            }
        }

        $sampleSize = count($errors);

        if ($sampleSize === 0) {
            return [
                'accuracy' => null,
                'sample_size' => 0,
                'mape' => null,
                'bias' => null,
            ];
        }

        $mape = (array_sum($errors) / $sampleSize) * 100;
        $bias = (array_sum($biases) / $sampleSize) * 100;
        $accuracy = max(0, 100 - $mape);

        return [
            'accuracy' => $accuracy,
            'sample_size' => $sampleSize,
            'mape' => $mape,
            'bias' => $bias,
            'is_overestimating' => $bias < 0,
        ];
    }

    /**
     * Force refresh prediction (clear cache and recalculate)
     */
    public function refreshPrediction(int $courseId, int $semesterId): array
    {
        $cacheKey = "capacity_prediction_{$courseId}_{$semesterId}";
        Cache::forget($cacheKey);

        return $this->predictOptimalCapacity($courseId, $semesterId);
    }

    /**
     * Check if ML service is available
     */
    public function isMLServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer '.$this->mlApiKey,
                ])
                ->get("{$this->mlServiceUrl}/health");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get system status with enhanced metrics
     */
    public function getSystemStatus(): array
    {
        $mlAvailable = $this->isMLServiceAvailable();
        $systemHealth = $this->calculateSystemHealth();

        return [
            'ml_service_available' => $mlAvailable,
            'ml_service_url' => $this->mlServiceUrl,
            'default_model' => config('services.ml_api.model_version', '1.0.0'),
            'cache_ttl' => config('services.ml_api.cache_ttl', 30).' minutes',
            'prediction_methods' => ['ml_prediction', 'enhanced_rule_based', 'rule_based'],
            'system_health' => $systemHealth,
            'data_quality_score' => $this->calculateDataQualityScore(),
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Calculate overall system health
     */
    private function calculateSystemHealth(): array
    {
        $totalCourses = Course::count();
        $coursesWithHistory = DB::table('enrollment_histories')
            ->distinct('course_id')
            ->count('course_id');

        $historyCoverage = $totalCourses > 0 ? ($coursesWithHistory / $totalCourses) * 100 : 0;

        $avgPredictionsPerDay = DB::table('capacity_predictions')
            ->where('created_at', '>=', now()->subDays(7))
            ->count() / 7;

        $recentAccuracy = $this->getRecentAccuracy();

        $healthScore = min(100, ($historyCoverage * 0.4) + ($recentAccuracy * 0.4) + min(20, $avgPredictionsPerDay));

        return [
            'score' => round($healthScore, 1),
            'status' => $healthScore >= 80 ? 'excellent' : ($healthScore >= 60 ? 'good' : ($healthScore >= 40 ? 'fair' : 'poor')),
            'metrics' => [
                'history_coverage_percent' => round($historyCoverage, 1),
                'avg_predictions_per_day' => round($avgPredictionsPerDay, 1),
                'recent_accuracy_percent' => round($recentAccuracy, 1),
            ],
        ];
    }

    /**
     * Calculate data quality score
     */
    private function calculateDataQualityScore(): float
    {
        $totalRecords = DB::table('enrollment_histories')->count();
        $recordsWithCapacity = DB::table('enrollment_histories')
            ->whereNotNull('max_capacity')
            ->where('max_capacity', '>', 0)
            ->count();

        $capacityCompleteness = $totalRecords > 0 ? ($recordsWithCapacity / $totalRecords) * 100 : 0;

        $avgRecordsPerCourse = DB::table('enrollment_histories')
            ->selectRaw('course_id, COUNT(*) as record_count')
            ->groupBy('course_id')
            ->get()
            ->avg('record_count') ?? 0;

        // Score based on completeness and depth
        $score = ($capacityCompleteness * 0.6) + (min(20, $avgRecordsPerCourse) * 3);

        return min(100, round($score, 1));
    }

    /**
     * Get recent prediction accuracy
     */
    private function getRecentAccuracy(): float
    {
        $recentPredictions = DB::table('capacity_predictions')
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        if ($recentPredictions->isEmpty()) {
            return 0;
        }

        $accuratePredictions = 0;
        foreach ($recentPredictions as $prediction) {
            // Simple accuracy check: within 20% of actual enrollment
            $actual = CourseOffering::where('course_id', $prediction->course_id)
                ->where('semester_id', $prediction->semester_id)
                ->value('enrolled_count');

            if ($actual && $prediction->predicted_students > 0) {
                $accuracy = abs($actual - $prediction->predicted_students) / $prediction->predicted_students;
                if ($accuracy <= 0.2) { // Within 20%
                    $accuratePredictions++;
                }
            }
        }

        return $recentPredictions->count() > 0 ? ($accuratePredictions / $recentPredictions->count()) * 100 : 0;
    }

    /**
     * Generate advanced recommendations with AI insights
     */
    public function generateAdvancedRecommendations(int $courseId, int $semesterId): array
    {
        $features = $this->featureEngine->generateFeatures($courseId, $semesterId);
        $prediction = $this->predictOptimalCapacity($courseId, $semesterId);

        $recommendations = [];

        // Capacity optimization recommendations
        if ($prediction['confidence_level'] > 0.7) {
            $recommendedCapacity = $prediction['recommended_capacity'];
            $currentCapacity = CourseOffering::where('course_id', $courseId)
                ->where('semester_id', $semesterId)
                ->value('max_students') ?? 0;

            if ($currentCapacity > 0 && abs($recommendedCapacity - $currentCapacity) > 3) {
                $recommendations[] = [
                    'type' => 'capacity_optimization',
                    'priority' => 'high',
                    'title' => 'Capacity Adjustment Recommended',
                    'description' => "AI suggests capacity of {$recommendedCapacity} students (currently {$currentCapacity})",
                    'confidence' => $prediction['confidence_level'],
                    'action_required' => 'Adjust course capacity',
                    'expected_impact' => 'Improved resource utilization',
                ];
            }
        }

        // Risk analysis
        $risks = $this->analyzeRisks($courseId, $semesterId, $features);
        $recommendations = array_merge($recommendations, $risks);

        // Trend-based insights
        $trends = $this->analyzeTrends($courseId, $features);
        $recommendations = array_merge($recommendations, $trends);

        // Seasonal recommendations
        $seasonal = $this->generateSeasonalRecommendations($semesterId);
        $recommendations = array_merge($recommendations, $seasonal);

        return $recommendations;
    }

    /**
     * Analyze potential risks
     */
    private function analyzeRisks(int $courseId, int $semesterId, array $features): array
    {
        $risks = [];

        // High demand risk
        if (($features['historical_avg_enrollment'] ?? 0) > ($features['course_year_level'] ? $this->getBaseCapacityByYearLevel($features['course_year_level']) : 30)) {
            $risks[] = [
                'type' => 'risk_analysis',
                'priority' => 'medium',
                'title' => 'High Demand Expected',
                'description' => 'Historical enrollment exceeds typical capacity for this course level',
                'confidence' => 0.8,
                'action_required' => 'Monitor enrollment closely',
                'expected_impact' => 'Potential over-enrollment',
            ];
        }

        // Low confidence warning
        if (($features['historical_variance'] ?? 0) > 10) {
            $risks[] = [
                'type' => 'risk_analysis',
                'priority' => 'low',
                'title' => 'Unpredictable Enrollment',
                'description' => 'High variance in historical enrollment makes prediction uncertain',
                'confidence' => 0.9,
                'action_required' => 'Consider flexible capacity planning',
                'expected_impact' => 'Variable resource needs',
            ];
        }

        return $risks;
    }

    /**
     * Analyze enrollment trends
     */
    private function analyzeTrends(int $courseId, array $features): array
    {
        $trends = [];

        $trend = $features['historical_trend'] ?? 0;
        if (abs($trend) > 2) {
            $direction = $trend > 0 ? 'increasing' : 'decreasing';
            $trends[] = [
                'type' => 'trend_analysis',
                'priority' => 'medium',
                'title' => "Enrollment Trend: {$direction}",
                'description' => "Course enrollment has been {$direction} over recent semesters",
                'confidence' => 0.75,
                'action_required' => $trend > 0 ? 'Consider capacity increase' : 'Monitor for potential decline',
                'expected_impact' => 'Better capacity planning',
            ];
        }

        return $trends;
    }

    /**
     * Generate seasonal recommendations
     */
    private function generateSeasonalRecommendations(int $semesterId): array
    {
        $semester = Semester::find($semesterId);
        if (! $semester) {
            return [];
        }

        $recommendations = [];
        $month = now()->month;

        // Summer semester recommendations
        if ($month >= 6 && $month <= 8) {
            $recommendations[] = [
                'type' => 'seasonal_advice',
                'priority' => 'low',
                'title' => 'Summer Semester Considerations',
                'description' => 'Summer enrollment patterns may differ from regular semesters',
                'confidence' => 0.6,
                'action_required' => 'Review capacity for summer-specific demand',
                'expected_impact' => 'Optimized summer course offerings',
            ];
        }

        // Pre-registration period
        $daysUntilStart = $semester->start_date ? now()->diffInDays($semester->start_date) : 0;
        if ($daysUntilStart > 30) {
            $recommendations[] = [
                'type' => 'seasonal_advice',
                'priority' => 'medium',
                'title' => 'Early Planning Opportunity',
                'description' => 'Long lead time allows for capacity adjustments based on pre-registration data',
                'confidence' => 0.8,
                'action_required' => 'Monitor pre-registration trends',
                'expected_impact' => 'Proactive capacity management',
            ];
        }

        return $recommendations;
    }
}
