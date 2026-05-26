<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'question_id',
        'assessment_id',
        'attempt_id',
        'answer',
        'option_id',
        
        'is_correct',
        'points_earned',
        'feedback',
        'submitted_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
        'submitted_at' => 'datetime',
        
        'option_id' => 'integer',
    ];

    /**
     * Get the student that owns the answer.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the question that owns the answer.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the assessment that owns the answer.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the quiz attempt that owns the answer.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }
}
