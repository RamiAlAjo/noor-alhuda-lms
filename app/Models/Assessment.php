<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'assessment_type_id',
        'title',
        'title_ar',
        'description',
        'max_grade',
        'max_score',
        'weight',
        'due_date',
        'due_time',
        'duration_minutes',
        'duration',
        'is_published',
        // Quiz enhancements
        'quiz_type',
        'time_limit_minutes',
        'time_limit_seconds',
        'show_results_immediately',
        'shuffle_questions',
        'shuffle_options',
        'attempts_allowed',
        'passing_score',
        'available_from',
        'available_until',
        'show_correct_answers',
        'show_feedback',
        'total_points',
        // Grade locking
        'grades_locked',
        'grades_locked_by',
        'grades_locked_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_published' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'show_results_immediately' => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_correct_answers' => 'boolean',
        'show_feedback' => 'boolean',
        'passing_score' => 'decimal:2',
        'time_limit_minutes' => 'integer',
        'time_limit_seconds' => 'integer',
        'attempts_allowed' => 'integer',
        'grades_locked' => 'boolean',
        'grades_locked_at' => 'datetime',
    ];

    /**
     * Get the offering (main relationship).
     */
    public function offering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    /**
     * Alias for offering relationship (backward compatibility).
     */
    public function section()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    /**
     * Alias for offering relationship (backward compatibility).
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }

    public function assessmentType()
    {
        return $this->belongsTo(AssessmentType::class);
    }

    public function grades()
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Alias for grades() - used in several places for student grading.
     */
    public function studentGrades()
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Get the questions for this assessment.
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Get the quiz attempts for this assessment.
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the student answers for this assessment.
     */
    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Check if this is a quiz (has time limit or questions).
     */
    public function isQuiz(): bool
    {
        return $this->questions()->exists() ||
               $this->time_limit_minutes > 0 ||
               in_array($this->quiz_type, ['quiz', 'pre_quiz', 'post_quiz']);
    }

    /**
     * Check if this is a regular Quiz.
     */
    public function isRegularQuiz(): bool
    {
        return $this->quiz_type === 'quiz';
    }

    /**
     * Check if this is a Pre-Quiz.
     */
    public function isPreQuiz(): bool
    {
        return $this->quiz_type === 'pre_quiz';
    }

    /**
     * Check if this is a Post-Quiz.
     */
    public function isPostQuiz(): bool
    {
        return $this->quiz_type === 'post_quiz';
    }

    /**
     * Get total time limit in seconds.
     */
    public function getTotalTimeLimitInSeconds(): int
    {
        return ($this->time_limit_minutes ?? 0) * 60 + ($this->time_limit_seconds ?? 0);
    }

    /**
     * Get total points for the assessment.
     */
    public function getTotalPoints(): ?float
    {
        return $this->total_points ?? $this->max_grade;
    }

    /**
     * Check if quiz has time limit.
     */
    public function hasTimeLimit(): bool
    {
        return $this->getTotalTimeLimitInSeconds() > 0;
    }

    /**
     * Check if student can attempt quiz.
     */
    public function canAttempt(?int $attemptCount = null): bool
    {
        // If no limit, always can attempt
        if ($this->attempts_allowed === null) {
            return true;
        }

        return $attemptCount < $this->attempts_allowed;
    }

    /**
     * Get quiz type options.
     */
    public static function getQuizTypes(): array
    {
        return [
            'none' => __('No Quiz'),
            'quiz' => __('Quiz'),
            'pre_quiz' => __('Pre-Quiz'),
            'post_quiz' => __('Post-Quiz'),
        ];
    }

    /**
     * Get the user who locked grades.
     */
    public function gradesLocker()
    {
        return $this->belongsTo(User::class, 'grades_locked_by');
    }

    /**
     * Check if grades are locked.
     */
    public function areGradesLocked(): bool
    {
        return $this->grades_locked;
    }

    /**
     * Lock all grades for this assessment.
     */
    public function lockGrades(int $userId): void
    {
        $this->update([
            'grades_locked' => true,
            'grades_locked_by' => $userId,
            'grades_locked_at' => now(),
        ]);

        // Lock all individual grades
        foreach ($this->grades as $grade) {
            if (! $grade->is_locked) {
                $grade->lock($userId, 'Locked with assessment grades');
            }
        }

        // Record in history
        GradeLockHistory::create([
            'lockable_type' => self::class,
            'lockable_id' => $this->id,
            'locked' => true,
            'performed_by' => $userId,
            'reason' => 'Assessment grades locked',
        ]);
    }

    /**
     * Unlock all grades for this assessment.
     */
    public function unlockGrades(int $userId): void
    {
        $this->update([
            'grades_locked' => false,
            'grades_locked_by' => null,
            'grades_locked_at' => null,
        ]);

        // Record in history
        GradeLockHistory::create([
            'lockable_type' => self::class,
            'lockable_id' => $this->id,
            'locked' => false,
            'performed_by' => $userId,
            'reason' => 'Assessment grades unlocked',
        ]);
    }
}
