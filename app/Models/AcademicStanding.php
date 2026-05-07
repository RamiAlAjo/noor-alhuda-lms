<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicStanding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'semester_id',
        'standing',
        'standing_type',
        'gpa_at_time',
        'cumulative_gpa',
        'credits_attempted',
        'credits_earned',
        'reason',
        'notes',
        'is_active',
        'start_date',
        'end_date',
        'set_by',
        'set_at',
        'requirements',
    ];

    protected $casts = [
        'gpa_at_time' => 'decimal:2',
        'cumulative_gpa' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'set_at' => 'datetime',
        'requirements' => 'array',
    ];

    // Standing constants
    const STANDING_GOOD = 'good_standing';

    const STANDING_PROBATION = 'probation';

    const STANDING_SUSPENSION = 'suspension';

    const STANDING_DISMISSAL = 'dismissal';

    // Standing type constants
    const TYPE_ACADEMIC = 'academic';

    const TYPE_DISCIPLINARY = 'disciplinary';

    /**
     * Get the student.
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
     * Get the user who set the standing.
     */
    public function setter()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    /**
     * Check if standing is good.
     */
    public function isGoodStanding(): bool
    {
        return $this->standing === self::STANDING_GOOD;
    }

    /**
     * Check if on probation.
     */
    public function isProbation(): bool
    {
        return $this->standing === self::STANDING_PROBATION;
    }

    /**
     * Check if suspended.
     */
    public function isSuspended(): bool
    {
        return $this->standing === self::STANDING_SUSPENSION;
    }

    /**
     * Check if dismissed.
     */
    public function isDismissed(): bool
    {
        return $this->standing === self::STANDING_DISMISSAL;
    }

    /**
     * Check if standing is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Deactivate the standing.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false, 'end_date' => now()]);
    }

    /**
     * Scope to get active standings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by standing.
     */
    public function scopeWithStanding($query, string $standing)
    {
        return $query->where('standing', $standing);
    }

    /**
     * Scope to filter by student.
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Calculate academic standing based on GPA.
     */
    public static function calculateStandingFromGpa(float $gpa): string
    {
        // These thresholds can be configured
        if ($gpa >= 2.0) {
            return self::STANDING_GOOD;
        } elseif ($gpa >= 1.5) {
            return self::STANDING_PROBATION;
        } elseif ($gpa >= 1.0) {
            return self::STANDING_SUSPENSION;
        } else {
            return self::STANDING_DISMISSAL;
        }
    }

    /**
     * Get all available standings.
     */
    public static function getStandings(): array
    {
        return [
            self::STANDING_GOOD => __('lms.good_standing'),
            self::STANDING_PROBATION => __('lms.probation'),
            self::STANDING_SUSPENSION => __('lms.suspension'),
            self::STANDING_DISMISSAL => __('lms.dismissal'),
        ];
    }

    /**
     * Get all standing types.
     */
    public static function getStandingTypes(): array
    {
        return [
            self::TYPE_ACADEMIC => __('lms.academic'),
            self::TYPE_DISCIPLINARY => __('lms.disciplinary'),
        ];
    }

    /**
     * Get standing color for UI.
     */
    public function getColorAttribute(): string
    {
        return match ($this->standing) {
            self::STANDING_GOOD => 'green',
            self::STANDING_PROBATION => 'yellow',
            self::STANDING_SUSPENSION => 'orange',
            self::STANDING_DISMISSAL => 'red',
            default => 'gray',
        };
    }
}
