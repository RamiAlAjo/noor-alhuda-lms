<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assignment Model
 *
 * Represents an assignment within a course offering.
 *
 * @property int $id
 * @property int $course_id
 * @property int|null $course_offering_id
 * @property string $title
 * @property string|null $description
 * @property int $max_score
 * @property \Carbon\Carbon|null $due_date
 * @property bool $is_published
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\CourseOffering|null $courseOffering
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Submission[] $submissions
 */
class Assignment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assignments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'course_offering_id',
        'title',
        'description',
        'max_score',
        'due_date',
        'is_published',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'max_score' => 'integer',
            'is_published' => 'boolean',
            'due_date' => 'datetime',
        ];
    }

    /**
     * Get the course that this assignment belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the course offering for this assignment.
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get all submissions for this assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Scope to filter only published assignments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to filter assignments with upcoming due dates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('due_date', '>', now())
            ->orderBy('due_date', 'asc');
    }

    /**
     * Scope to filter overdue assignments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('is_published', true);
    }

    /**
     * Check if the assignment is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Get the average score for this assignment.
     */
    public function getAverageScoreAttribute(): ?float
    {
        $average = $this->submissions()->whereNotNull('score')->avg('score');

        return $average !== null ? round($average, 2) : null;
    }

    /**
     * Get the submission rate for this assignment.
     */
    public function getSubmissionRateAttribute(): float
    {
        if (! $this->courseOffering) {
            return 0;
        }

        $totalStudents = $this->courseOffering->enrollments()->count();
        if ($totalStudents === 0) {
            return 0;
        }

        $submittedCount = $this->submissions()->count();

        return round(($submittedCount / $totalStudents) * 100, 2);
    }
}
