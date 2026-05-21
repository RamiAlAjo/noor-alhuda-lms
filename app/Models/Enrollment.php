<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    protected $fillable = [
        'student_id',
        'course_offering_id',
        'semester_id',
        'status',
        'approved_by',
        'approved_at',
        'enrolled_at',
        'dropped_at',
        'completed_at',
        'final_grade',
        'notes',
        'admin_notes',
        'completed_activities',
        'total_activities',
        'progress_percentage',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'approved_at' => 'datetime',
        'dropped_at' => 'datetime',
        'completed_at' => 'datetime',
        'final_grade' => 'decimal:2',
        'progress_percentage' => 'decimal:2',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_DROPPED = 'dropped';

    const STATUS_COMPLETED = 'completed';

    /**
     * Get the student that owns the enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course offering that owns the enrollment.
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    /**
     * Alias for offering relationship.
     */
    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    /**
     * Alias for courseOffering relationship.
     */
    public function offering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    /**
     * Alias for courseOffering relationship.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    /**
     * Get the semester that owns the enrollment.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the major that owns the enrollment.
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Get the attendance records for the enrollment.
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the grades for the enrollment.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Check if enrollment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Get the submissions for this enrollment through course offering.
     */
    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Submission::class,
            Assignment::class,
            'course_offering_id', // Foreign key on assignments table
            'assignment_id', // Foreign key on submissions table
            'course_offering_id', // Local key on enrollments table
            'id' // Local key on assignments table
        );
    }

    /**
     * Get the quiz attempts for this enrollment.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'enrollment_id');
    }

    /**
     * Get the attendance records for this enrollment.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'enrollment_id');
    }

    /**
     * Check if enrollment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if enrollment is dropped.
     */
    public function isDropped(): bool
    {
        return $this->status === self::STATUS_DROPPED;
    }

    /**
     * Check if enrollment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Drop the enrollment.
     */
    public function drop(): void
    {
        $this->update([
            'status' => self::STATUS_DROPPED,
            'dropped_at' => now(),
        ]);

        // Decrement enrollment count
        if ($this->offering) {
            $this->offering->decrement('current_students');
        }
    }

    /**
     * Complete the enrollment.
     */
    public function complete(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    /**
     * Update progress.
     */
    public function updateProgress(): void
    {
        if ($this->total_activities > 0) {
            $percentage = ($this->completed_activities / $this->total_activities) * 100;
            $this->update(['progress_percentage' => $percentage]);
        }
    }

    /**
     * Scope to filter by status.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to filter by approved status.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to filter by dropped status.
     */
    public function scopeDropped($query)
    {
        return $query->where('status', self::STATUS_DROPPED);
    }

    /**
     * Get enrollment duration in days.
     */
    public function getDurationInDays(): ?int
    {
        if (! $this->enrolled_at) {
            return null;
        }

        $endDate = $this->completed_at ?? $this->dropped_at ?? now();

        return $this->enrolled_at->diffInDays($endDate);
    }

    /**
     * Check if enrollment has a passing grade.
     */
    public function hasPassed(): bool
    {
        if (! $this->final_grade) {
            return false;
        }

        // Assuming A, B, C are passing grades
        return in_array($this->final_grade, ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-']);
    }

    /**
     * Boot the model and add prerequisite validation on creation for approved enrollments.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $enrollment) {
            if ($enrollment->status === 'approved' && $enrollment->course_offering_id) {
                $offering = CourseOffering::find($enrollment->course_offering_id);
                if ($offering && $offering->course) {
                    if (! $offering->course->hasCompletedPrerequisites($enrollment->student_id)) {
                        throw new \Exception('Prerequisite not met');
                    }
                }
            }
        });
    }
}
