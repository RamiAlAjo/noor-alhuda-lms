<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAccommodation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'accommodation_type_id',
        'notes',
        'custom_settings',
        'start_date',
        'end_date',
        'status',
        'approved_by',
        'approved_at',
        'documentation_path',
    ];

    protected $casts = [
        'custom_settings' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    const STATUS_ACTIVE = 'active';

    const STATUS_EXPIRED = 'expired';

    const STATUS_SUSPENDED = 'suspended';

    const STATUS_PENDING = 'pending';

    /**
     * Get all statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => __('lms.active'),
            self::STATUS_EXPIRED => __('lms.expired'),
            self::STATUS_SUSPENDED => __('lms.suspended'),
            self::STATUS_PENDING => __('lms.pending'),
        ];
    }

    /**
     * Get the student.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the accommodation type.
     */
    public function accommodationType()
    {
        return $this->belongsTo(AccommodationType::class);
    }

    /**
     * Get the approver.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the quiz accommodations.
     */
    public function quizAccommodations()
    {
        return $this->hasMany(QuizAccommodation::class);
    }

    /**
     * Scope to get active accommodations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope to get expired accommodations.
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Check if accommodation is active.
     */
    public function isActive(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if accommodation is expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Activate the accommodation.
     */
    public function activate(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Suspend the accommodation.
     */
    public function suspend(): void
    {
        $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    /**
     * Get the effective setting value.
     */
    public function getEffectiveSetting(string $key, $default = null)
    {
        // Check custom settings first
        if (isset($this->custom_settings[$key])) {
            return $this->custom_settings[$key];
        }

        // Fall back to default settings from accommodation type
        return $this->accommodationType?->getDefaultSetting($key, $default);
    }

    /**
     * Get extended time for quizzes (in minutes).
     */
    public function getExtendedTimeMinutes(): ?int
    {
        return $this->getEffectiveSetting('extended_time_minutes');
    }

    /**
     * Get extended time percentage.
     */
    public function getExtendedTimePercentage(): ?float
    {
        return $this->getEffectiveSetting('extended_time_percentage');
    }

    /**
     * Get additional attempts allowed.
     */
    public function getAdditionalAttempts(): int
    {
        return $this->getEffectiveSetting('additional_attempts', 0);
    }

    /**
     * Check if breaks are allowed.
     */
    public function allowsBreaks(): bool
    {
        return $this->getEffectiveSetting('allow_breaks', false);
    }
}
