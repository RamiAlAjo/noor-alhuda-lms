<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAccommodation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_accommodation_id',
        'assessment_id',
        'extended_time_minutes',
        'extended_time_percentage',
        'additional_attempts',
        'allow_breaks',
        'special_instructions',
        'is_applied',
        'applied_at',
        'applied_by',
    ];

    protected $casts = [
        'extended_time_percentage' => 'decimal:2',
        'allow_breaks' => 'boolean',
        'is_applied' => 'boolean',
        'applied_at' => 'datetime',
    ];

    /**
     * Get the student accommodation.
     */
    public function studentAccommodation()
    {
        return $this->belongsTo(StudentAccommodation::class);
    }

    /**
     * Get the assessment.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the user who applied this accommodation.
     */
    public function applier()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    /**
     * Calculate the total extended time for a given base duration.
     */
    public function calculateExtendedTime(int $baseMinutes): int
    {
        // If percentage is set, calculate based on that
        if ($this->extended_time_percentage) {
            return (int) ($baseMinutes * (1 + $this->extended_time_percentage / 100));
        }

        // If fixed minutes are set, add them
        if ($this->extended_time_minutes) {
            return $baseMinutes + $this->extended_time_minutes;
        }

        // No extension
        return $baseMinutes;
    }

    /**
     * Apply the accommodation.
     */
    public function apply(int $userId): void
    {
        $this->update([
            'is_applied' => true,
            'applied_at' => now(),
            'applied_by' => $userId,
        ]);
    }

    /**
     * Check if this accommodation is applied.
     */
    public function isApplied(): bool
    {
        return $this->is_applied;
    }

    /**
     * Scope to get applied accommodations.
     */
    public function scopeApplied($query)
    {
        return $query->where('is_applied', true);
    }

    /**
     * Scope to get pending accommodations.
     */
    public function scopePending($query)
    {
        return $query->where('is_applied', false);
    }
}
