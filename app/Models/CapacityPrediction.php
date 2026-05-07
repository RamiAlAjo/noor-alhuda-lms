<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CapacityPrediction Model
 *
 * Represents AI-generated predictions for course capacity planning.
 *
 * @property int $id
 * @property int $course_id
 * @property int $semester_id
 * @property string $prediction_horizon
 * @property int|null $predicted_students
 * @property int|null $recommended_capacity
 * @property int|null $minimum_viable
 * @property int|null $maximum_optimal
 * @property float|null $confidence_level
 * @property array|null $feature_importance
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\Semester $semester
 */
class CapacityPrediction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'capacity_predictions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'semester_id',
        'prediction_horizon',
        'predicted_students',
        'recommended_capacity',
        'minimum_viable',
        'maximum_optimal',
        'confidence_level',
        'feature_importance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'predicted_students' => 'integer',
            'recommended_capacity' => 'integer',
            'minimum_viable' => 'integer',
            'maximum_optimal' => 'integer',
            'confidence_level' => 'float',
            'feature_importance' => 'array',
        ];
    }

    /**
     * Get the course for this prediction.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the semester for this prediction.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Scope to filter by prediction horizon.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHorizon($query, string $horizon)
    {
        return $query->where('prediction_horizon', $horizon);
    }

    /**
     * Scope to filter by confidence level.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfident($query, float $minConfidence = 0.7)
    {
        return $query->where('confidence_level', '>=', $minConfidence);
    }

    /**
     * Scope to get the latest prediction for each course/semester combination.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if the prediction has high confidence.
     */
    public function isHighConfidence(): bool
    {
        return $this->confidence_level !== null && $this->confidence_level >= 0.8;
    }

    /**
     * Check if the prediction has low confidence.
     */
    public function isLowConfidence(): bool
    {
        return $this->confidence_level !== null && $this->confidence_level < 0.5;
    }

    /**
     * Get the capacity utilization percentage.
     */
    public function getUtilizationPercentage(int $currentEnrollment): ?float
    {
        if (! $this->recommended_capacity || $this->recommended_capacity === 0) {
            return null;
        }

        return round(($currentEnrollment / $this->recommended_capacity) * 100, 2);
    }

    /**
     * Get the top N most important features.
     */
    public function getTopFeatures(int $n = 5): array
    {
        if (! $this->feature_importance) {
            return [];
        }

        arsort($this->feature_importance);

        return array_slice($this->feature_importance, 0, $n, true);
    }

    /**
     * Get the prediction accuracy status.
     */
    public function getAccuracyStatus(int $actualStudents): string
    {
        if (! $this->predicted_students) {
            return 'unknown';
        }

        $error = abs($actualStudents - $this->predicted_students);
        $errorPercentage = ($error / $this->predicted_students) * 100;

        if ($errorPercentage <= 10) {
            return 'accurate';
        } elseif ($errorPercentage <= 20) {
            return 'moderate';
        } else {
            return 'inaccurate';
        }
    }
}
