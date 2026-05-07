<?php

namespace App\Services\AI\Exceptions;

/**
 * Exception thrown when input data quality is insufficient
 */
class DataQualityException extends \InvalidArgumentException
{
    private array $validationErrors;

    private array $features;

    public function __construct(
        string $message,
        array $validationErrors = [],
        array $features = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->validationErrors = $validationErrors;
        $this->features = $features;
    }

    /**
     * Get validation errors
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * Get the features that failed validation
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * Check if a specific field has an error
     */
    public function hasErrorForField(string $field): bool
    {
        foreach ($this->validationErrors as $error) {
            if (str_contains($error, $field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get errors for a specific field
     */
    public function getErrorsForField(string $field): array
    {
        return array_filter(
            $this->validationErrors,
            fn ($error) => str_contains($error, $field)
        );
    }

    /**
     * Create exception for missing required features
     */
    public static function missingRequiredFeatures(array $missingFields): self
    {
        $message = 'Missing required features: '.implode(', ', $missingFields);

        return new self(
            message: $message,
            validationErrors: array_map(
                fn ($field) => "{$field} is required",
                $missingFields
            )
        );
    }

    /**
     * Create exception for invalid feature values
     */
    public static function invalidFeatureValues(array $errors): self
    {
        return new self(
            message: 'Invalid feature values: '.implode(', ', $errors),
            validationErrors: $errors
        );
    }

    /**
     * Create exception for insufficient data
     */
    public static function insufficientData(string $reason, array $context = []): self
    {
        return new self(
            message: "Insufficient data for prediction: {$reason}",
            validationErrors: [$reason],
            features: $context
        );
    }

    /**
     * Create exception for data out of range
     */
    public static function dataOutOfRange(string $field, $value, $min, $max): self
    {
        return new self(
            message: "{$field} value {$value} is out of range [{$min}, {$max}]",
            validationErrors: ["{$field} must be between {$min} and {$max}"]
        );
    }

    /**
     * Convert to array for logging
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'validation_errors' => $this->validationErrors,
            'features' => $this->features,
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}
