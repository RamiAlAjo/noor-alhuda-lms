<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'student_id',
        'assessment_id',
        'grade',
        'grade_points',
        'course_credits',
        'feedback',
        'graded_by',
        'graded_at',
        'submission_path',
        'submission_text',
        'submitted_at',
        'is_late',
        // Quiz-specific fields
        'percentage',
        'passed',
        'max_grade',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
        'graded_at' => 'datetime',
        'submitted_at' => 'datetime',
        'is_late' => 'boolean',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'max_grade' => 'decimal:2',
    ];

    /**
     * Get the student that owns the grade.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the assessment that owns the grade.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the user who graded the assessment.
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Get the enrollment that owns this grade.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Calculate the percentage grade.
     */
    public function getPercentageAttribute(): float
    {
        if (! $this->grade || ! $this->assessment->max_grade) {
            return 0;
        }

        return round(($this->grade / $this->assessment->max_grade) * 100, 2);
    }

    /**
     * Check if the student has submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    /**
     * Scope to get submitted grades.
     */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * Scope to get graded assessments.
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('grade');
    }
}
