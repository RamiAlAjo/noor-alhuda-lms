<?php

namespace App\Services\AI\Contracts;

/**
 * Value object representing validation result
 */
class ValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly array $errors = [],
        public readonly array $warnings = []
    ) {}

    /**
     * Check if validation passed
     */
    public function passed(): bool
    {
        return $this->isValid;
    }

    /**
     * Check if validation failed
     */
    public function failed(): bool
    {
        return ! $this->isValid;
    }

    /**
     * Check if there are warnings
     */
    public function hasWarnings(): bool
    {
        return ! empty($this->warnings);
    }

    /**
     * Get first error message
     */
    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    /**
     * Get all error messages as string
     */
    public function getErrorsAsString(string $separator = ', '): string
    {
        return implode($separator, $this->errors);
    }

    /**
     * Merge with another validation result
     */
    public function merge(ValidationResult $other): self
    {
        return new self(
            isValid: $this->isValid && $other->isValid,
            errors: array_merge($this->errors, $other->errors),
            warnings: array_merge($this->warnings, $other->warnings)
        );
    }

    /**
     * Convert to array for serialization
     */
    public function toArray(): array
    {
        return [
            'is_valid' => $this->isValid,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
        ];
    }

    /**
     * Create a successful validation result
     */
    public static function success(array $warnings = []): self
    {
        return new self(isValid: true, warnings: $warnings);
    }

    /**
     * Create a failed validation result
     */
    public static function failure(array $errors, array $warnings = []): self
    {
        return new self(isValid: false, errors: $errors, warnings: $warnings);
    }
}
