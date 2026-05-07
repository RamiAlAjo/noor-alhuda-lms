<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'start_date',
        'end_date',
        'enrollment_start_date',
        'enrollment_end_date',
        'drop_start_date',
        'drop_end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'enrollment_start_date' => 'date',
        'enrollment_end_date' => 'date',
        'drop_start_date' => 'date',
        'drop_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function sections()
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * Alias for sections.
     */
    public function offerings()
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * Check if enrollment period is currently open.
     */
    public function isEnrollmentOpen(): bool
    {
        if (! $this->enrollment_start_date || ! $this->enrollment_end_date) {
            return false;
        }

        $today = Carbon::today();

        return $today->gte($this->enrollment_start_date) && $today->lte($this->enrollment_end_date);
    }

    /**
     * Check if drop period is currently open.
     */
    public function isDropOpen(): bool
    {
        if (! $this->drop_start_date || ! $this->drop_end_date) {
            return false;
        }

        $today = Carbon::today();

        return $today->gte($this->drop_start_date) && $today->lte($this->drop_end_date);
    }

    /**
     * Get enrollment period status.
     */
    public function getEnrollmentStatus(): string
    {
        if (! $this->enrollment_start_date || ! $this->enrollment_end_date) {
            return 'not_configured';
        }

        $today = Carbon::today();

        if ($today->lt($this->enrollment_start_date)) {
            return 'upcoming';
        }

        if ($today->gt($this->enrollment_end_date)) {
            return 'closed';
        }

        return 'open';
    }

    /**
     * Get drop period status.
     */
    public function getDropStatus(): string
    {
        if (! $this->drop_start_date || ! $this->drop_end_date) {
            return 'not_configured';
        }

        $today = Carbon::today();

        if ($today->lt($this->drop_start_date)) {
            return 'upcoming';
        }

        if ($today->gt($this->drop_end_date)) {
            return 'closed';
        }

        return 'open';
    }

    /**
     * Get the current active semester.
     */
    public static function getCurrent(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
