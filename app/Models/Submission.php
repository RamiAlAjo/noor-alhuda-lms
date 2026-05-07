<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Submission Model
 *
 * Represents a student's submission for an assignment or quiz.
 *
 * @property int $id
 * @property int $student_id
 * @property int $assignment_id
 * @property string|null $content
 * @property string|null $file_path
 * @property int|null $score
 * @property string|null $feedback
 * @property bool $is_late
 * @property \Carbon\Carbon|null $submitted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $student
 * @property-read \App\Models\Assignment $assignment
 */
class Submission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'submissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'assignment_id',
        'content',
        'file_path',
        'score',
        'feedback',
        'is_late',
        'submitted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_late' => 'boolean',
            'submitted_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    /**
     * Get the student who made the submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the assignment for this submission.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Scope to filter only late submissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    /**
     * Scope to filter only on-time submissions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnTime($query)
    {
        return $query->where('is_late', false);
    }

    /**
     * Scope to filter submissions with scores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('score');
    }

    /**
     * Scope to filter submissions without scores.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUngraded($query)
    {
        return $query->whereNull('score');
    }

    /**
     * Check if the submission has been graded.
     */
    public function isGraded(): bool
    {
        return $this->score !== null;
    }

    /**
     * Get the percentage score for this submission.
     */
    public function getPercentageAttribute(): ?float
    {
        if ($this->score === null || ! $this->assignment || $this->assignment->max_score <= 0) {
            return null;
        }

        return round(($this->score / $this->assignment->max_score) * 100, 2);
    }
}
