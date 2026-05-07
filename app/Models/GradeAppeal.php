<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeAppeal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'grade_id',
        'enrollment_id',
        'assessment_id',
        'subject',
        'description',
        'student_justification',
        'current_grade',
        'requested_grade',
        'status',
        'reviewed_by',
        'reviewed_at',
        'teacher_response',
        'admin_notes',
        'escalated_to',
        'escalated_at',
        'attachments',
    ];

    protected $casts = [
        'current_grade' => 'decimal:2',
        'requested_grade' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'escalated_at' => 'datetime',
        'attachments' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_UNDER_REVIEW = 'under_review';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_ESCALATED = 'escalated';

    /**
     * Get the student who submitted the appeal.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the grade being appealed.
     */
    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    /**
     * Get the enrollment related to the appeal.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    /**
     * Get the assessment related to the appeal.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    /**
     * Get the user who reviewed the appeal.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the admin to whom the appeal was escalated.
     */
    public function escalatedTo()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    /**
     * Check if the appeal is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the appeal is under review.
     */
    public function isUnderReview(): bool
    {
        return $this->status === self::STATUS_UNDER_REVIEW;
    }

    /**
     * Check if the appeal is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the appeal is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the appeal is escalated.
     */
    public function isEscalated(): bool
    {
        return $this->status === self::STATUS_ESCALATED;
    }

    /**
     * Mark the appeal as under review.
     */
    public function markAsUnderReview(): void
    {
        $this->update(['status' => self::STATUS_UNDER_REVIEW]);
    }

    /**
     * Approve the appeal.
     */
    public function approve(int $reviewerId, ?string $response = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'teacher_response' => $response,
        ]);
    }

    /**
     * Reject the appeal.
     */
    public function reject(int $reviewerId, ?string $response = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'teacher_response' => $response,
        ]);
    }

    /**
     * Escalate the appeal to an admin.
     */
    public function escalate(int $adminId): void
    {
        $this->update([
            'status' => self::STATUS_ESCALATED,
            'escalated_to' => $adminId,
            'escalated_at' => now(),
        ]);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending appeals.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get appeals under review.
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', self::STATUS_UNDER_REVIEW);
    }

    /**
     * Scope to get escalated appeals.
     */
    public function scopeEscalated($query)
    {
        return $query->where('status', self::STATUS_ESCALATED);
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
            self::STATUS_UNDER_REVIEW => __('lms.under_review'),
            self::STATUS_APPROVED => __('lms.approved'),
            self::STATUS_REJECTED => __('lms.rejected'),
            self::STATUS_ESCALATED => __('lms.escalated'),
        ];
    }
}
