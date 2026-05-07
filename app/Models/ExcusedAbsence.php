<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExcusedAbsence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_offering_id',
        'enrollment_id',
        'absence_date',
        'absence_type',
        'end_date',
        'reason_type',
        'reason',
        'attachments',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'attendance_updated',
    ];

    protected $casts = [
        'absence_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'attachments' => 'array',
        'attendance_updated' => 'boolean',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    // Absence type constants
    const TYPE_SINGLE_DAY = 'single_day';

    const TYPE_MULTIPLE_DAYS = 'multiple_days';

    const TYPE_LATE_ARRIVAL = 'late_arrival';

    const TYPE_EARLY_DEPARTURE = 'early_departure';

    // Reason type constants
    const REASON_PERSONAL = 'personal';

    const REASON_FAMILY_EMERGENCY = 'family_emergency';

    const REASON_RELIGIOUS = 'religious';

    const REASON_MEDICAL_APPOINTMENT = 'medical_appointment';

    const REASON_OTHER = 'other';

    /**
     * Get the student who requested the excused absence.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course offering.
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the enrollment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Get the user who reviewed the request.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Approve the request.
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
     * Reject the request.
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
     * Get the duration in days.
     */
    public function getDurationDaysAttribute(): int
    {
        if ($this->absence_type === self::TYPE_SINGLE_DAY) {
            return 1;
        }
        if ($this->end_date) {
            return $this->absence_date->diffInDays($this->end_date) + 1;
        }

        return 1;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to filter by student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope to filter by course offering.
     */
    public function scopeForCourse($query, int $courseOfferingId)
    {
        return $query->where('course_offering_id', $courseOfferingId);
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
        ];
    }

    /**
     * Get all absence types.
     */
    public static function getAbsenceTypes(): array
    {
        return [
            self::TYPE_SINGLE_DAY => __('lms.single_day'),
            self::TYPE_MULTIPLE_DAYS => __('lms.multiple_days'),
            self::TYPE_LATE_ARRIVAL => __('lms.late_arrival'),
            self::TYPE_EARLY_DEPARTURE => __('lms.early_departure'),
        ];
    }

    /**
     * Get all reason types.
     */
    public static function getReasonTypes(): array
    {
        return [
            self::REASON_PERSONAL => __('lms.personal'),
            self::REASON_FAMILY_EMERGENCY => __('lms.family_emergency'),
            self::REASON_RELIGIOUS => __('lms.religious'),
            self::REASON_MEDICAL_APPOINTMENT => __('lms.medical_appointment'),
            self::REASON_OTHER => __('lms.other'),
        ];
    }
}
