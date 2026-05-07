<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalLeave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'semester_id',
        'leave_type',
        'start_date',
        'end_date',
        'duration_days',
        'reason',
        'medical_notes',
        'attachments',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'affects_attendance',
        'requires_makeup',
        'makeup_instructions',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'attachments' => 'array',
        'affects_attendance' => 'boolean',
        'requires_makeup' => 'boolean',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_CANCELLED = 'cancelled';

    // Leave type constants
    const TYPE_SICK = 'sick';

    const TYPE_EMERGENCY = 'emergency';

    const TYPE_HOSPITALIZATION = 'hospitalization';

    const TYPE_CHRONIC = 'chronic';

    /**
     * Get the student who requested the leave.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the semester.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the user who reviewed the leave.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if the leave is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the leave is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the leave is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the leave is active (currently on leave).
     */
    public function isActive(): bool
    {
        return $this->isApproved()
            && $this->start_date->lte(now())
            && $this->end_date->gte(now());
    }

    /**
     * Check if the leave has ended.
     */
    public function hasEnded(): bool
    {
        return $this->end_date->lt(now());
    }

    /**
     * Approve the leave request.
     */
    public function approve(int $reviewerId, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    /**
     * Reject the leave request.
     */
    public function reject(int $reviewerId, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    /**
     * Cancel the leave request.
     */
    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    /**
     * Calculate duration in days.
     */
    public function calculateDuration(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending leaves.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get approved leaves.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope to get active leaves.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope to filter by student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => __('lms.pending'),
            self::STATUS_APPROVED => __('lms.approved'),
            self::STATUS_REJECTED => __('lms.rejected'),
            self::STATUS_CANCELLED => __('lms.cancelled'),
        ];
    }

    /**
     * Get all leave types.
     */
    public static function getLeaveTypes(): array
    {
        return [
            self::TYPE_SICK => __('lms.sick_leave'),
            self::TYPE_EMERGENCY => __('lms.emergency_leave'),
            self::TYPE_HOSPITALIZATION => __('lms.hospitalization'),
            self::TYPE_CHRONIC => __('lms.chronic_condition'),
        ];
    }
}
