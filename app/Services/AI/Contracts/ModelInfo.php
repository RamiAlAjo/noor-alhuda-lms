<?php

namespace App\Services\AI\Contracts;

/**
 * Value object representing model information
 */
class ModelInfo
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $type,
        public readonly array $capabilities = [],
        public readonly ?string $description = null,
        public readonly ?string $trainedAt = null,
        public readonly ?array $metrics = null
    ) {}

    /**
     * Check if model supports a specific capability
     */
    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->capabilities);
    }

    /**
     * Get model accuracy if available
     */
    public function getAccuracy(): ?float
    {
        return $this->metrics['accuracy'] ?? null;
    }

    /**
     * Get model precision if available
     */
    public function getPrecision(): ?float
    {
        return $this->metrics['precision'] ?? null;
    }

    /**
     * Get model recall if available
     */
    public function getRecall(): ?float
    {
        return $this->metrics['recall'] ?? null;
    }

    /**
     * Get model F1 score if available
     */
    public function getF1Score(): ?float
    {
        return $this->metrics['f1_score'] ?? null;
    }

    /**
     * Convert to array for serialization
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'type' => $this->type,
            'capabilities' => $this->capabilities,
            'description' => $this->description,
            'trained_at' => $this->trainedAt,
            'metrics' => $this->metrics,
        ];
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            version: $data['version'],
            type: $data['type'],
            capabilities: $data['capabilities'] ?? [],
            description: $data['description'] ?? null,
            trainedAt: $data['trained_at'] ?? null,
            metrics: $data['metrics'] ?? null
        );
    }
}
