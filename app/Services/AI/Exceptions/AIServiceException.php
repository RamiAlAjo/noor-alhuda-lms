<?php

namespace App\Services\AI\Exceptions;

/**
 * Exception thrown when AI service operations fail
 */
class AIServiceException extends \RuntimeException
{
    private ?string $service;

    private ?string $operation;

    private array $context;

    public function __construct(
        string $message,
        ?string $service = null,
        ?string $operation = null,
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->service = $service;
        $this->operation = $operation;
        $this->context = $context;
    }

    /**
     * Get the service that failed
     */
    public function getService(): ?string
    {
        return $this->service;
    }

    /**
     * Get the operation that failed
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * Get additional context
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create exception for ML service unavailable
     */
    public static function mlServiceUnavailable(string $url, ?\Throwable $previous = null): self
    {
        return new self(
            message: "ML service is unavailable at {$url}",
            service: 'ml_service',
            operation: 'connect',
            context: ['url' => $url],
            previous: $previous
        );
    }

    /**
     * Create exception for prediction timeout
     */
    public static function predictionTimeout(string $endpoint, int $timeout): self
    {
        return new self(
            message: "Prediction request to {$endpoint} timed out after {$timeout}ms",
            service: 'ml_service',
            operation: 'predict',
            context: ['endpoint' => $endpoint, 'timeout_ms' => $timeout]
        );
    }

    /**
     * Create exception for model not found
     */
    public static function modelNotFound(string $modelName, ?string $version = null): self
    {
        $message = "Model '{$modelName}' not found";
        if ($version) {
            $message .= " (version: {$version})";
        }

        return new self(
            message: $message,
            service: 'ml_service',
            operation: 'load_model',
            context: ['model' => $modelName, 'version' => $version]
        );
    }

    /**
     * Convert to array for logging
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'service' => $this->service,
            'operation' => $this->operation,
            'context' => $this->context,
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}
