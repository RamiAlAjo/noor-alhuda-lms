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

        // Try ML prediction first
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
            Log::warning('ML prediction failed, using fallback: '.$e->getMessage());

            // Fallback to rule-based prediction
            $result = $this->ruleBasedPrediction($courseId, $semesterId);
        }

        // Cache the prediction
        Cache::put($cacheKey, $result, now()->addHours(config('services.ml_api.cache_ttl', 6)));

        return $result;
    }

    /**
     * Call external ML service
     */
    private function callMLService(string $endpoint, array $features): array
    {
        $response = Http::timeout(config('services.ml_api.timeout', 30))
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
     * Fallback rule-based prediction when ML service is unavailable
     */
    private function ruleBasedPrediction(int $courseId, int $semesterId): array
    {
        $course = Course::findOrFail($courseId);
        $semester = Semester::find($semesterId);

        // Base capacity on course type and year level
        $baseCapacity = $this->getBaseCapacityByYearLevel($course->year_level);

        // Adjust for course type (lab courses need smaller groups)
        if ($course->lab_hours > 0) {
            $baseCapacity = min($baseCapacity, 20);
        }

        // Adjust for theory hours
        if ($course->theory_hours > 3) {
            $baseCapacity = min($baseCapacity, 30);
        }

        // Get historical data for adjustment
        $historicalAvg = $this->featureEngine->getHistoricalAverage($courseId);
        $historicalMax = $this->featureEngine->getHistoricalMax($courseId);
        $historicalMin = $this->featureEngine->getHistoricalMin($courseId);

        // Use historical data if available
        if ($historicalAvg > 0) {
            // Blend historical average with base capacity
            $baseCapacity = (int) (($historicalAvg * 0.7) + ($baseCapacity * 0.3));
        }

        // Calculate predicted students based on fill rate trend
        $fillRate = $this->featureEngine->getHistoricalFillRate($courseId);
        $predictedStudents = $fillRate > 0
            ? (int) ($baseCapacity * ($fillRate / 100))
            : (int) ($baseCapacity * 0.75);

        // Determine phase adjustment
        $phase = $semester
            ? $this->featureEngine->getSemesterPhase($semesterId)
            : 'registration';

        $phaseMultiplier = match ($phase) {
            'pre_registration' => 1.0,
            'registration' => 1.1,
            'early' => 1.0,
            'mid' => 0.95,
            'late' => 0.9,
            default => 1.0,
        };

        $predictedStudents = (int) ceil($predictedStudents * $phaseMultiplier);

        return [
            'predicted_students' => max($predictedStudents, $historicalMin > 0 ? $historicalMin : 5),
            'recommended_capacity' => $baseCapacity,
            'minimum_viable' => (int) max(5, floor($baseCapacity * 0.4)),
            'maximum_optimal' => (int) min(100, ceil($baseCapacity * 1.3)),
            'confidence_level' => $historicalAvg > 0 ? 0.65 : 0.5,
            'method' => 'rule_based',
            'feature_importance' => null,
        ];
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
    private function getBaseCapacityByYearLevel(int $yearLevel): int
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
     * Get system status
     */
    public function getSystemStatus(): array
    {
        $mlAvailable = $this->isMLServiceAvailable();

        return [
            'ml_service_available' => $mlAvailable,
            'ml_service_url' => $this->mlServiceUrl,
            'default_model' => config('services.ml_api.model_version', '1.0.0'),
            'cache_ttl' => config('services.ml_api.cache_ttl', 6).' hours',
            'prediction_methods' => ['ml_prediction', 'rule_based'],
        ];
    }
}
