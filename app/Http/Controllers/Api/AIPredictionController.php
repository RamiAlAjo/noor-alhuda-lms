<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\Engines\StudentPerformancePredictionEngine;
use App\Services\AI\Exceptions\AIServiceException;
use App\Services\AI\Exceptions\DataQualityException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * AI Prediction Controller
 *
 * Provides REST API endpoints for AI-powered predictions.
 * Supports student performance prediction, batch processing, and model information.
 */
class AIPredictionController extends Controller
{
    private StudentPerformancePredictionEngine $predictionEngine;

    public function __construct(StudentPerformancePredictionEngine $predictionEngine)
    {
        $this->predictionEngine = $predictionEngine;
    }

    /**
     * Predict student performance
     *
     *
     * @example POST /api/ai/predict/performance
     * {
     *   "student_id": 123,
     *   "course_id": 456,
     *   "historical_grades": [85, 90, 88],
     *   "attendance_rate": 0.92,
     *   "assignments_submitted": 8,
     *   "quiz_average": 87.5
     * }
     */
    public function predictPerformance(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'student_id' => 'required|integer|exists:users,id',
                'course_id' => 'required|integer|exists:courses,id',
                'historical_grades' => 'required|array|min:1|max:20',
                'historical_grades.*' => 'numeric|between:0,100',
                'attendance_rate' => 'required|numeric|between:0,1',
                'assignments_submitted' => 'required|integer|min:0|max:1000',
                'quiz_average' => 'required|numeric|between:0,100',
            ]);

            $result = $this->predictionEngine->predict($request->all());

            return response()->json([
                'success' => true,
                'data' => $result->toArray(),
            ]);

        } catch (DataQualityException $e) {
            Log::warning('Prediction validation failed', [
                'error' => $e->getMessage(),
                'features' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'message' => $e->getMessage(),
                'validation_errors' => $e->getValidationErrors(),
            ], 422);

        } catch (AIServiceException $e) {
            Log::error('AI service error', [
                'error' => $e->getMessage(),
                'service' => $e->getService(),
                'operation' => $e->getOperation(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'AI service error',
                'message' => 'Prediction service temporarily unavailable',
            ], 503);

        } catch (\Exception $e) {
            Log::error('Prediction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => 'Failed to generate prediction',
            ], 500);
        }
    }

    /**
     * Batch predict student performance
     *
     *
     * @example POST /api/ai/predict/batch
     * {
     *   "predictions": [
     *     {"student_id": 123, "course_id": 456},
     *     {"student_id": 124, "course_id": 456}
     *   ]
     * }
     */
    public function batchPredict(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'predictions' => 'required|array|min:1|max:100',
                'predictions.*.student_id' => 'required|integer|exists:users,id',
                'predictions.*.course_id' => 'required|integer|exists:courses,id',
                'predictions.*.historical_grades' => 'required|array|min:1|max:20',
                'predictions.*.historical_grades.*' => 'numeric|between:0,100',
                'predictions.*.attendance_rate' => 'required|numeric|between:0,1',
                'predictions.*.assignments_submitted' => 'required|integer|min:0|max:1000',
                'predictions.*.quiz_average' => 'required|numeric|between:0,100',
            ]);

            $results = $this->predictionEngine->batchPredict($request->input('predictions'));

            return response()->json([
                'success' => true,
                'data' => array_map(fn ($r) => $r->toArray(), $results),
                'meta' => [
                    'total' => count($results),
                    'successful' => count(array_filter($results, fn ($r) => $r !== null)),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Batch prediction failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Batch prediction failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get model information
     *
     *
     * @example GET /api/ai/model/info
     */
    public function getModelInfo(): JsonResponse
    {
        try {
            $modelInfo = $this->predictionEngine->getModelInfo();

            return response()->json([
                'success' => true,
                'data' => $modelInfo->toArray(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get model info', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve model information',
            ], 500);
        }
    }

    /**
     * Check AI service health
     *
     *
     * @example GET /api/ai/health
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $isHealthy = $this->predictionEngine->isHealthy();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $isHealthy ? 'healthy' : 'degraded',
                    'service' => 'student_performance_prediction',
                    'timestamp' => now()->toISOString(),
                ],
            ], $isHealthy ? 200 : 503);

        } catch (\Exception $e) {
            Log::error('Health check failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'data' => [
                    'status' => 'unhealthy',
                    'service' => 'student_performance_prediction',
                    'error' => $e->getMessage(),
                    'timestamp' => now()->toISOString(),
                ],
            ], 503);
        }
    }
}
