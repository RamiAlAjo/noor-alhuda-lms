<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'student_grades';

    protected $fillable = [
        'student_id',
        'enrollment_id',
        'assessment_id',
        'course_credits',
        'grade',
        'grade_points',
        'letter_grade',
        'notes',
        'is_locked',
        'locked_by',
        'locked_at',
        'lock_reason',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function locker()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Check if the grade is locked.
     */
    public function isLocked(): bool
    {
        return $this->is_locked;
    }

    /**
     * Lock the grade.
     */
    public function lock(int $userId, ?string $reason = null): void
    {
        $this->update([
            'is_locked' => true,
            'locked_by' => $userId,
            'locked_at' => now(),
            'lock_reason' => $reason,
        ]);

        // Record in history
        GradeLockHistory::create([
            'lockable_type' => self::class,
            'lockable_id' => $this->id,
            'locked' => true,
            'performed_by' => $userId,
            'reason' => $reason,
        ]);
    }

    /**
     * Unlock the grade.
     */
    public function unlock(int $userId, ?string $reason = null): void
    {
        $this->update([
            'is_locked' => false,
            'locked_by' => null,
            'locked_at' => null,
            'lock_reason' => null,
        ]);

        // Record in history
        GradeLockHistory::create([
            'lockable_type' => self::class,
            'lockable_id' => $this->id,
            'locked' => false,
            'performed_by' => $userId,
            'reason' => $reason,
        ]);
    }

    /**
     * Scope to get locked grades.
     */
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    /**
     * Scope to get unlocked grades.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }
}
