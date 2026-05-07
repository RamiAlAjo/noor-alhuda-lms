<?php

namespace App\Services\AI\Contracts;

/**
 * Interface for AI prediction services
 *
 * All prediction engines must implement this interface to ensure
 * consistent behavior and enable polymorphic usage.
 */
interface PredictionInterface
{
    /**
     * Make a prediction for given features
     *
     * @param  array  $features  Input features for prediction
     * @return PredictionResult Prediction result with confidence
     *
     * @throws \App\Services\AI\Exceptions\AIServiceException On prediction failure
     * @throws \App\Services\AI\Exceptions\DataQualityException On invalid features
     */
    public function predict(array $features): PredictionResult;

    /**
     * Make batch predictions for multiple feature sets
     *
     * @param  array  $batchFeatures  Array of feature sets
     * @return array Array of PredictionResult objects
     */
    public function batchPredict(array $batchFeatures): array;

    /**
     * Get model version and metadata
     *
     * @return ModelInfo Model information
     */
    public function getModelInfo(): ModelInfo;

    /**
     * Validate input features before prediction
     *
     * @param  array  $features  Features to validate
     * @return ValidationResult Validation result with errors if any
     */
    public function validateFeatures(array $features): ValidationResult;

    /**
     * Check if the prediction service is healthy
     *
     * @return bool True if service is operational
     */
    public function isHealthy(): bool;
}
