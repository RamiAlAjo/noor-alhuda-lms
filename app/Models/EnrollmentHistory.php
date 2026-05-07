<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EnrollmentHistory Model
 *
 * Represents historical enrollment data for courses.
 *
 * @property int $id
 * @property int $course_id
 * @property int|null $semester_id
 * @property \Carbon\Carbon $enrollment_date
 * @property int $enrolled_count
 * @property int $max_capacity
 * @property int $drop_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\Semester|null $semester
 */
class EnrollmentHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'enrollment_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'semester_id',
        'enrollment_date',
        'enrolled_count',
        'max_capacity',
        'drop_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'enrolled_count' => 'integer',
            'max_capacity' => 'integer',
            'drop_count' => 'integer',
        ];
    }

    /**
     * Get the course for this enrollment history.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the semester for this enrollment history.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Scope to filter by date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('enrollment_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeYear($query, int $year)
    {
        return $query->whereYear('enrollment_date', $year);
    }

    /**
     * Scope to filter by month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMonth($query, int $month)
    {
        return $query->whereMonth('enrollment_date', $month);
    }

    /**
     * Get the fill rate percentage.
     */
    public function getFillRateAttribute(): float
    {
        if ($this->max_capacity === 0) {
            return 0;
        }

        return round(($this->enrolled_count / $this->max_capacity) * 100, 2);
    }

    /**
     * Get the drop rate percentage.
     */
    public function getDropRateAttribute(): float
    {
        if ($this->enrolled_count === 0) {
            return 0;
        }

        return round(($this->drop_count / $this->enrolled_count) * 100, 2);
    }

    /**
     * Check if the course is at capacity.
     */
    public function isAtCapacity(): bool
    {
        return $this->enrolled_count >= $this->max_capacity;
    }

    /**
     * Check if the course is underutilized.
     */
    public function isUnderutilized(float $threshold = 30.0): bool
    {
        return $this->fill_rate < $threshold;
    }

    /**
     * Get the net enrollment change.
     */
    public function getNetChangeAttribute(): int
    {
        return $this->enrolled_count - $this->drop_count;
    }

    /**
     * Scope to filter courses at capacity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAtCapacity($query)
    {
        return $query->whereRaw('enrolled_count >= max_capacity');
    }

    /**
     * Scope to filter underutilized courses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnderutilized($query, float $threshold = 30.0)
    {
        return $query->whereRaw('(enrolled_count / NULLIF(max_capacity, 0)) * 100 < ?', [$threshold]);
    }
}
