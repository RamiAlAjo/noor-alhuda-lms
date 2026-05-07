<?php

namespace App\Services\AI\Engines;

use App\Services\AI\Contracts\ModelInfo;
use App\Services\AI\Contracts\PredictionInterface;
use App\Services\AI\Contracts\PredictionResult;
use App\Services\AI\Contracts\ValidationResult;
use App\Services\AI\Exceptions\AIServiceException;
use App\Services\AI\Exceptions\DataQualityException;
use App\Services\AI\FeatureExtractors\StudentFeatureExtractor;
use App\Services\AI\Validators\PredictionValidator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Student Performance Prediction Engine
 *
 * Predicts student academic performance using ensemble of ML models
 * with rule-based fallback when ML service is unavailable.
 *
 * Core Prediction Logic:
 * 1. Validates input features using PredictionValidator
 * 2. Extracts and normalizes features using StudentFeatureExtractor
 * 3. Generates prediction via ML service or rule-based fallback
 * 4. Calibrates confidence based on feature quality
 * 5. Returns structured PredictionResult with feature importance
 */
class StudentPerformancePredictionEngine implements PredictionInterface
{
    private string $mlServiceUrl;

    private string $mlApiKey;

    private StudentFeatureExtractor $featureExtractor;

    private PredictionValidator $validator;

    /**
     * Feature weights for rule-based fallback prediction
     * Based on correlation analysis with academic performance
     */
    private const FEATURE_WEIGHTS = [
        'historical_gpa' => 0.30,
        'grade_trend' => 0.15,
        'attendance_rate' => 0.15,
        'assignment_completion' => 0.12,
        'quiz_average' => 0.10,
        'grade_consistency' => 0.08,
        'late_submission_rate' => 0.05,
        'course_difficulty' => 0.05,
    ];

    /**
     * Circuit breaker configuration
     */
    private const CIRCUIT_BREAKER_KEY = 'ml_service_circuit_breaker';

    private const CIRCUIT_BREAKER_THRESHOLD = 5;

    private const CIRCUIT_BREAKER_TIMEOUT = 300; // 5 minutes

    public function __construct(
        StudentFeatureExtractor $featureExtractor,
        PredictionValidator $validator
    ) {
        $this->mlServiceUrl = config('services.ml_api.url', 'http://localhost:8000');
        $this->mlApiKey = config('services.ml_api.key', '');
        $this->featureExtractor = $featureExtractor;
        $this->validator = $validator;
    }

    /**
     * Predict student performance
     *
     * Core prediction pipeline:
     * 1. Validate input features
     * 2. Extract and normalize features
     * 3. Check cache for existing prediction
     * 4. Call ML service or use fallback
     * 5. Calibrate confidence based on feature quality
     * 6. Return structured result
     */
    public function predict(array $features): PredictionResult
    {
        $startTime = microtime(true);

        try {
            // Step 1: Validate input features
            $validation = $this->validateFeatures($features);
            if ($validation->failed()) {
                throw new DataQualityException(
                    'Invalid features: '.$validation->getErrorsAsString(),
                    $validation->errors,
                    $features
                );
            }

            // Step 2: Extract and normalize features
            $processedFeatures = $this->featureExtractor->extract($features);

            // Step 3: Assess feature quality for confidence calibration
            $featureQuality = $this->assessFeatureQuality($processedFeatures);

            // Step 4: Check cache
            $cacheKey = $this->generateCacheKey($processedFeatures);
            if ($cached = Cache::get($cacheKey)) {
                Log::debug('Prediction cache hit', ['key' => $cacheKey]);

                return $cached;
            }

            // Step 5: Generate prediction (ML or fallback)
            $prediction = $this->generatePrediction($processedFeatures, $featureQuality);

            // Step 6: Post-process and create result
            $result = $this->createResult($prediction, $processedFeatures, $featureQuality, $startTime);

            // Step 7: Cache result
            Cache::put($cacheKey, $result, now()->addMinutes(15));

            // Step 8: Log for monitoring
            $this->logPrediction($result, $processedFeatures, $startTime);

            return $result;

        } catch (DataQualityException $e) {
            Log::warning('Data quality issue in prediction', [
                'error' => $e->getMessage(),
                'student_id' => $features['student_id'] ?? 'unknown',
                'course_id' => $features['course_id'] ?? 'unknown',
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Prediction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Fallback to rule-based prediction
            return $this->fallbackPrediction($features, $startTime);
        }
    }

    /**
     * Make batch predictions
     */
    public function batchPredict(array $batchFeatures): array
    {
        $results = [];

        foreach ($batchFeatures as $index => $features) {
            try {
                $results[$index] = $this->predict($features);
            } catch (\Exception $e) {
                Log::error("Batch prediction failed for index {$index}", [
                    'error' => $e->getMessage(),
                ]);
                $results[$index] = $this->fallbackPrediction($features, microtime(true));
            }
        }

        return $results;
    }

    /**
     * Get model information
     */
    public function getModelInfo(): ModelInfo
    {
        return new ModelInfo(
            name: 'Student Performance Predictor',
            version: config('services.ml_api.model_version', '1.0.0'),
            type: 'ensemble',
            capabilities: ['classification', 'regression', 'feature_importance'],
            description: 'Predicts student academic performance using ensemble of gradient boosted trees and neural networks',
            trainedAt: '2024-01-15',
            metrics: [
                'accuracy' => 0.87,
                'precision' => 0.85,
                'recall' => 0.89,
                'f1_score' => 0.87,
            ]
        );
    }

    /**
     * Validate input features
     */
    public function validateFeatures(array $features): ValidationResult
    {
        return $this->validator->validate($features, [
            'student_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer|exists:courses,id',
            'historical_grades' => 'array|min:1',
            'attendance_rate' => 'numeric|between:0,1',
            'assignments_submitted' => 'integer|min:0',
            'quiz_average' => 'numeric|between:0,100',
        ]);
    }

    /**
     * Check if service is healthy
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => "Bearer {$this->mlApiKey}"])
                ->get("{$this->mlServiceUrl}/health");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Assess feature quality for confidence calibration
     *
     * Evaluates completeness, consistency, and reliability of features
     * to adjust prediction confidence accordingly.
     */
    private function assessFeatureQuality(array $features): array
    {
        $quality = [
            'completeness' => 0.0,
            'consistency' => 0.0,
            'reliability' => 0.0,
            'overall' => 0.0,
        ];

        // Check feature completeness
        $requiredFeatures = ['historical_gpa', 'attendance_rate', 'assignment_completion', 'quiz_average'];
        $presentFeatures = array_filter($requiredFeatures, fn ($f) => isset($features[$f]) && $features[$f] !== null);
        $quality['completeness'] = count($presentFeatures) / count($requiredFeatures);

        // Check feature consistency (no contradictory values)
        $quality['consistency'] = $this->calculateFeatureConsistency($features);

        // Check feature reliability (based on data recency and volume)
        $quality['reliability'] = $this->calculateFeatureReliability($features);

        // Calculate overall quality score
        $quality['overall'] = ($quality['completeness'] * 0.4) +
                             ($quality['consistency'] * 0.3) +
                             ($quality['reliability'] * 0.3);

        return $quality;
    }

    /**
     * Calculate feature consistency score
     */
    private function calculateFeatureConsistency(array $features): float
    {
        $consistency = 1.0;

        // Check for contradictory patterns
        if (isset($features['historical_gpa'], $features['quiz_average'])) {
            $gpaNormalized = $features['historical_gpa'] > 0 ? $features['historical_gpa'] / 4.0 : 0;
            $quizNormalized = $features['quiz_average'] > 0 ? $features['quiz_average'] / 100.0 : 0;

            // If GPA and quiz scores differ significantly, reduce consistency
            $difference = abs($gpaNormalized - $quizNormalized);
            if ($difference > 0.3) {
                $consistency -= 0.2;
            }
        }

        // Check attendance vs assignment completion correlation
        if (isset($features['attendance_rate'], $features['assignment_completion'])) {
            $correlation = abs($features['attendance_rate'] - $features['assignment_completion']);
            if ($correlation > 0.4) {
                $consistency -= 0.15;
            }
        }

        return max(0.0, $consistency);
    }

    /**
     * Calculate feature reliability score
     */
    private function calculateFeatureReliability(array $features): float
    {
        $reliability = 0.7; // Base reliability

        // More historical data increases reliability
        if (isset($features['semester_count']) && $features['semester_count'] > 3) {
            $reliability += 0.15;
        }

        // Recent data increases reliability
        if (isset($features['days_since_enrollment']) && $features['days_since_enrollment'] > 30) {
            $reliability += 0.15;
        }

        return min(1.0, $reliability);
    }

    /**
     * Generate prediction using ML service or rule-based fallback
     */
    private function generatePrediction(array $features, array $featureQuality): array
    {
        // Try ML service first if available
        if ($this->isMLServiceAvailable()) {
            try {
                $mlPrediction = $this->callMLService($features);

                return [
                    'prediction' => $mlPrediction['prediction'],
                    'confidence' => $mlPrediction['confidence'],
                    'method' => 'ml_ensemble',
                    'factors' => $mlPrediction['factors'] ?? [],
                    'feature_importance' => $mlPrediction['feature_importance'] ?? null,
                ];
            } catch (\Exception $e) {
                Log::warning('ML service failed, using rule-based fallback', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Rule-based fallback prediction
        return $this->ruleBasedPrediction($features, $featureQuality);
    }

    /**
     * Rule-based prediction using weighted feature combination
     */
    private function ruleBasedPrediction(array $features, array $featureQuality): array
    {
        $prediction = 0.0;
        $totalWeight = 0.0;
        $factors = [];

        // Apply weighted features
        foreach (self::FEATURE_WEIGHTS as $feature => $weight) {
            if (isset($features[$feature])) {
                $value = $this->normalizeFeatureValue($feature, $features[$feature]);
                $prediction += $value * $weight;
                $totalWeight += $weight;
                $factors[] = $feature;
            }
        }

        // Normalize by total weight
        if ($totalWeight > 0) {
            $prediction = ($prediction / $totalWeight) * 100;
        } else {
            $prediction = 70.0; // Default prediction
        }

        // Adjust confidence based on feature quality
        $confidence = 0.6 * $featureQuality['overall'];

        return [
            'prediction' => min(100, max(0, $prediction)),
            'confidence' => min(0.95, max(0.3, $confidence)),
            'method' => 'rule_based',
            'factors' => $factors,
            'feature_importance' => ! empty($factors) ? array_fill_keys($factors, 1.0 / count($factors)) : [],
        ];
    }

    /**
     * Normalize feature value to 0-100 scale
     */
    private function normalizeFeatureValue(string $feature, $value): float
    {
        return match ($feature) {
            'historical_gpa' => ($value / 4.0) * 100,
            'grade_trend' => min(100, max(0, 50 + ($value * 10))),
            'attendance_rate' => $value * 100,
            'assignment_completion' => $value * 100,
            'quiz_average' => $value,
            'grade_consistency' => max(0, 100 - ($value * 10)),
            'late_submission_rate' => (1 - $value) * 100,
            'course_difficulty' => (1 - $value) * 100,
            default => (float) $value,
        };
    }

    /**
     * Check if ML service is available with circuit breaker pattern
     */
    private function isMLServiceAvailable(): bool
    {
        // Check circuit breaker state
        $circuitState = Cache::get(self::CIRCUIT_BREAKER_KEY, ['failures' => 0, 'last_failure' => null, 'state' => 'closed']);

        // If circuit is open, check if timeout has passed
        if ($circuitState['state'] === 'open') {
            if ($circuitState['last_failure'] && now()->diffInSeconds($circuitState['last_failure']) < self::CIRCUIT_BREAKER_TIMEOUT) {
                Log::info('ML service circuit breaker is open, skipping health check');

                return false;
            }
            // Timeout passed, try half-open state
            $circuitState['state'] = 'half-open';
            Cache::put(self::CIRCUIT_BREAKER_KEY, $circuitState, now()->addMinutes(10));
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => "Bearer {$this->mlApiKey}"])
                ->get("{$this->mlServiceUrl}/health");

            if ($response->successful()) {
                // Reset circuit breaker on success
                if ($circuitState['state'] === 'half-open') {
                    Cache::put(self::CIRCUIT_BREAKER_KEY, [
                        'failures' => 0,
                        'last_failure' => null,
                        'state' => 'closed',
                    ], now()->addMinutes(10));
                    Log::info('ML service recovered, circuit breaker closed');
                }

                return true;
            }

            // Service returned error
            $this->recordCircuitBreakerFailure($circuitState);

            return false;
        } catch (\Exception $e) {
            Log::warning('ML service health check failed', [
                'error' => $e->getMessage(),
            ]);
            $this->recordCircuitBreakerFailure($circuitState);

            return false;
        }
    }

    /**
     * Record a circuit breaker failure
     */
    private function recordCircuitBreakerFailure(array $circuitState): void
    {
        $circuitState['failures']++;
        $circuitState['last_failure'] = now();

        if ($circuitState['failures'] >= self::CIRCUIT_BREAKER_THRESHOLD) {
            $circuitState['state'] = 'open';
            Log::warning('ML service circuit breaker opened', [
                'failures' => $circuitState['failures'],
            ]);
        }

        Cache::put(self::CIRCUIT_BREAKER_KEY, $circuitState, now()->addMinutes(10));
    }

    /**
     * Call ML service with retry logic
     */
    private function callMLService(array $features): array
    {
        $maxRetries = 3;
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout(config('services.ml_api.timeout', 30))
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->mlApiKey}",
                        'Content-Type' => 'application/json',
                        'X-Request-ID' => uniqid('perf_', true),
                    ])
                    ->post("{$this->mlServiceUrl}/predict/performance", [
                        'features' => $features,
                        'model_version' => config('services.ml_api.model_version', '1.0.0'),
                    ]);

                if (! $response->successful()) {
                    throw new AIServiceException(
                        "ML service returned {$response->status()}",
                        'ml_service',
                        'predict',
                        ['status' => $response->status(), 'body' => $response->body()]
                    );
                }

                return $response->json();

            } catch (\Exception $e) {
                $lastException = $e;
                Log::warning("ML service attempt {$attempt} failed", [
                    'error' => $e->getMessage(),
                ]);

                if ($attempt < $maxRetries) {
                    usleep(100000 * $attempt); // Exponential backoff
                }
            }
        }

        throw $lastException;
    }

    /**
     * Create prediction result from prediction data
     *
     * Constructs a structured PredictionResult with:
     * - Predicted value (0-100 scale)
     * - Calibrated confidence based on feature quality
     * - Prediction method used
     * - Key factors influencing prediction
     * - Feature importance scores
     * - Latency metrics
     */
    private function createResult(array $prediction, array $features, array $featureQuality, float $startTime): PredictionResult
    {
        $latency = (microtime(true) - $startTime) * 1000;

        // Calibrate confidence based on feature quality
        $baseConfidence = (float) $prediction['confidence'];
        $calibratedConfidence = $this->calibrateConfidence($baseConfidence, $featureQuality);

        return new PredictionResult(
            value: (float) $prediction['prediction'],
            confidence: $calibratedConfidence,
            method: $prediction['method'] ?? 'ml_prediction',
            factors: $prediction['factors'] ?? [],
            featureImportance: $prediction['feature_importance'] ?? null,
            latency: $latency,
            modelVersion: $prediction['model_version'] ?? null
        );
    }

    /**
     * Calibrate confidence based on feature quality
     *
     * Adjusts base confidence using feature quality metrics:
     * - Higher quality features → higher confidence
     * - Lower quality features → lower confidence
     */
    private function calibrateConfidence(float $baseConfidence, array $featureQuality): float
    {
        // Apply quality-based adjustment
        $qualityAdjustment = $featureQuality['overall'] * 0.2;
        $calibrated = $baseConfidence * (0.8 + $qualityAdjustment);

        // Ensure confidence stays within reasonable bounds
        return min(0.99, max(0.1, $calibrated));
    }

    /**
     * Fallback rule-based prediction
     */
    private function fallbackPrediction(array $features, float $startTime): PredictionResult
    {
        Log::info('Using fallback rule-based prediction');

        $historicalAvg = $features['historical_average'] ?? 70;
        $recentTrend = $features['recent_trend'] ?? 0;
        $attendance = $features['attendance_rate'] ?? 0.8;

        // Simple weighted average
        $prediction = ($historicalAvg * 0.6) + ($recentTrend * 0.3) + ($attendance * 100 * 0.1);

        $latency = (microtime(true) - $startTime) * 1000;

        return new PredictionResult(
            value: min(100, max(0, $prediction)),
            confidence: 0.5,
            method: 'rule_based_fallback',
            factors: ['historical_average', 'recent_trend', 'attendance'],
            latency: $latency
        );
    }

    /**
     * Generate cache key for features
     */
    private function generateCacheKey(array $features): string
    {
        return 'prediction_'.md5(serialize($features));
    }

    /**
     * Log prediction for monitoring
     *
     * Logs comprehensive prediction metrics including:
     * - Prediction value and confidence
     * - Method used (ML or rule-based)
     * - Feature quality metrics
     * - Latency and performance data
     */
    private function logPrediction(PredictionResult $result, array $features, float $startTime): void
    {
        $latency = (microtime(true) - $startTime) * 1000;

        Log::channel('ai')->info('Prediction completed', [
            'engine' => 'student_performance',
            'prediction' => $result->value,
            'confidence' => $result->confidence,
            'confidence_level' => $result->getConfidenceLevel(),
            'method' => $result->method,
            'factors_count' => count($result->factors),
            'feature_count' => count($features),
            'latency_ms' => $latency,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
