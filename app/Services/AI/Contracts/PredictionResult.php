<?php

namespace App\Services\AI\Contracts;

/**
 * Value object representing a prediction result
 */
class PredictionResult
{
    public function __construct(
        public readonly float $value,
        public readonly float $confidence,
        public readonly string $method = 'ml_prediction',
        public readonly array $factors = [],
        public readonly ?array $featureImportance = null,
        public readonly ?float $latency = null,
        public readonly ?string $modelVersion = null
    ) {}

    /**
     * Check if prediction has high confidence
     */
    public function isHighConfidence(float $threshold = 0.8): bool
    {
        return $this->confidence >= $threshold;
    }

    /**
     * Check if prediction has medium confidence
     */
    public function isMediumConfidence(): bool
    {
        return $this->confidence >= 0.6 && $this->confidence < 0.8;
    }

    /**
     * Check if prediction has low confidence
     */
    public function isLowConfidence(): bool
    {
        return $this->confidence < 0.6;
    }

    /**
     * Get confidence level as string
     */
    public function getConfidenceLevel(): string
    {
        if ($this->isHighConfidence()) {
            return 'high';
        }

        if ($this->isMediumConfidence()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Convert to array for serialization
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'confidence' => $this->confidence,
            'confidence_level' => $this->getConfidenceLevel(),
            'method' => $this->method,
            'factors' => $this->factors,
            'feature_importance' => $this->featureImportance,
            'latency_ms' => $this->latency,
            'model_version' => $this->modelVersion,
        ];
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
            confidence: $data['confidence'],
            method: $data['method'] ?? 'ml_prediction',
            factors: $data['factors'] ?? [],
            featureImportance: $data['feature_importance'] ?? null,
            latency: $data['latency_ms'] ?? null,
            modelVersion: $data['model_version'] ?? null
        );
    }
}
