<?php

namespace App\Services\AI\Validators;

use App\Services\AI\Contracts\ValidationResult;

/**
 * Validates input features for AI predictions
 */
class PredictionValidator
{
    /**
     * Validate features against rules
     */
    public function validate(array $features, array $rules): ValidationResult
    {
        $errors = [];
        $warnings = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $features[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $result = $this->validateRule($field, $value, $rule, $features);

                if ($result['error']) {
                    $errors[] = $result['error'];
                }

                if ($result['warning']) {
                    $warnings[] = $result['warning'];
                }
            }
        }

        if (! empty($errors)) {
            return ValidationResult::failure($errors, $warnings);
        }

        return ValidationResult::success($warnings);
    }

    /**
     * Validate a single rule
     */
    private function validateRule(string $field, $value, string $rule, array $allFeatures): array
    {
        $result = ['error' => null, 'warning' => null];

        // Parse rule parameters
        [$ruleName, $ruleParams] = $this->parseRule($rule);

        switch ($ruleName) {
            case 'required':
                if ($value === null || $value === '') {
                    $result['error'] = "{$field} is required";
                }
                break;

            case 'integer':
                if ($value !== null && ! is_int($value) && ! ctype_digit((string) $value)) {
                    $result['error'] = "{$field} must be an integer";
                }
                break;

            case 'numeric':
                if ($value !== null && ! is_numeric($value)) {
                    $result['error'] = "{$field} must be numeric";
                }
                break;

            case 'string':
                if ($value !== null && ! is_string($value)) {
                    $result['error'] = "{$field} must be a string";
                }
                break;

            case 'array':
                if ($value !== null && ! is_array($value)) {
                    $result['error'] = "{$field} must be an array";
                }
                break;

            case 'min':
                if ($value !== null) {
                    if (is_numeric($value) && $value < $ruleParams[0]) {
                        $result['error'] = "{$field} must be at least {$ruleParams[0]}";
                    } elseif (is_array($value) && count($value) < $ruleParams[0]) {
                        $result['error'] = "{$field} must have at least {$ruleParams[0]} items";
                    } elseif (is_string($value) && strlen($value) < $ruleParams[0]) {
                        $result['error'] = "{$field} must be at least {$ruleParams[0]} characters";
                    }
                }
                break;

            case 'max':
                if ($value !== null) {
                    if (is_numeric($value) && $value > $ruleParams[0]) {
                        $result['error'] = "{$field} must be at most {$ruleParams[0]}";
                    } elseif (is_array($value) && count($value) > $ruleParams[0]) {
                        $result['error'] = "{$field} must have at most {$ruleParams[0]} items";
                    } elseif (is_string($value) && strlen($value) > $ruleParams[0]) {
                        $result['error'] = "{$field} must be at most {$ruleParams[0]} characters";
                    }
                }
                break;

            case 'between':
                if ($value !== null && is_numeric($value)) {
                    if ($value < $ruleParams[0] || $value > $ruleParams[1]) {
                        $result['error'] = "{$field} must be between {$ruleParams[0]} and {$ruleParams[1]}";
                    }
                }
                break;

            case 'exists':
                // This would typically check database
                // For now, just check if value is not empty
                if ($value !== null && empty($value)) {
                    $result['error'] = "{$field} does not exist";
                }
                break;

            case 'in':
                if ($value !== null && ! in_array($value, $ruleParams)) {
                    $result['error'] = "{$field} must be one of: ".implode(', ', $ruleParams);
                }
                break;

            case 'nullable':
                // Nullable allows null values
                break;

            default:
                // Unknown rule, add warning
                $result['warning'] = "Unknown validation rule: {$ruleName}";
        }

        return $result;
    }

    /**
     * Parse rule string into name and parameters
     */
    private function parseRule(string $rule): array
    {
        if (str_contains($rule, ':')) {
            [$name, $params] = explode(':', $rule, 2);

            return [$name, explode(',', $params)];
        }

        return [$rule, []];
    }

    /**
     * Validate features for student performance prediction
     */
    public function validateStudentFeatures(array $features): ValidationResult
    {
        return $this->validate($features, [
            'student_id' => 'required|integer',
            'course_id' => 'required|integer',
            'historical_gpa' => 'numeric|between:0,4',
            'attendance_rate' => 'numeric|between:0,1',
            'assignment_completion' => 'numeric|between:0,1',
            'quiz_average' => 'numeric|between:0,100',
            'grade_trend' => 'numeric',
        ]);
    }

    /**
     * Validate features for capacity prediction
     */
    public function validateCapacityFeatures(array $features): ValidationResult
    {
        return $this->validate($features, [
            'course_id' => 'required|integer',
            'semester_id' => 'required|integer',
            'historical_avg_enrollment' => 'numeric|min:0',
            'historical_max_enrollment' => 'numeric|min:0',
            'department_total_students' => 'integer|min:0',
        ]);
    }
}
