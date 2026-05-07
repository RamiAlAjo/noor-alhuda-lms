<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * QuizAttempt Model
 *
 * Represents a student's attempt at a quiz.
 *
 * @property int $id
 * @property int $student_id
 * @property int $course_id
 * @property int|null $course_offering_id
 * @property string|null $quiz_title
 * @property int $score
 * @property int $max_score
 * @property float|null $percentage
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $student
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\CourseOffering|null $courseOffering
 */
class QuizAttempt extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quiz_attempts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'course_offering_id',
        'quiz_title',
        'score',
        'max_score',
        'percentage',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'max_score' => 'integer',
            'percentage' => 'float',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the student who attempted the quiz.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the course for this quiz attempt.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the course offering for this quiz attempt.
     */
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Scope to filter completed quiz attempts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Scope to filter in-progress quiz attempts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProgress($query)
    {
        return $query->whereNotNull('started_at')
            ->whereNull('completed_at');
    }

    /**
     * Check if the quiz attempt is completed.
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Calculate and set the percentage before saving.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attempt) {
            if ($attempt->max_score > 0 && $attempt->score !== null) {
                $attempt->percentage = round(($attempt->score / $attempt->max_score) * 100, 2);
            }
        });
    }

    /**
     * Get the duration of the quiz attempt in minutes.
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if (! $this->started_at || ! $this->completed_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->completed_at);
    }

    /**
     * Scope to filter attempts by score range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScoreRange($query, int $min, int $max)
    {
        return $query->whereBetween('score', [$min, $max]);
    }
}
