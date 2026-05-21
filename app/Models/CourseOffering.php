<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseOffering extends Model
{
    use HasFactory;

    protected $table = 'course_offerings';

    protected $fillable = [
        'course_id',
        'semester_id',
        'teacher_id',
        'section_name',
        'schedule',
        'schedule_json',
        'room',
        'meeting_link',
        'meeting_id',
        'meeting_password',
        'max_students',
        'enrolled_count',
        'is_active',
        'is_visible_to_students',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_visible_to_students' => 'boolean',
        'schedule_json' => 'array',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get all teachers (many-to-many relationship).
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_teachers', 'course_offering_id', 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_offering_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class, 'course_offering_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'course_offering_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'target_offering_id');
    }

    /**
     * Get the competencies for this course offering.
     */
    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'course_competencies')
            ->withTimestamps();
    }

    /**
     * Get pre-quizzes for this offering.
     */
    public function preQuizzes()
    {
        return $this->assessments()->where('quiz_type', 'pre_quiz');
    }

    /**
     * Get post-quizzes for this offering.
     */
    public function postQuizzes()
    {
        return $this->assessments()->where('quiz_type', 'post_quiz');
    }

    /**
     * Get regular quizzes for this offering.
     */
    public function quizzes()
    {
        return $this->assessments()->where('quiz_type', 'quiz');
    }

    /**
     * Get the schedule as an array.
     */
    public function getScheduleArrayAttribute(): array
    {
        if ($this->schedule_json) {
            return $this->schedule_json;
        }

        // Parse from legacy schedule field if no JSON
        if ($this->schedule) {
            return [
                ['day' => '', 'time' => $this->schedule, 'room' => $this->room],
            ];
        }

        return [];
    }

    /**
     * Check if there's available capacity.
     */
    public function hasCapacity(): bool
    {
        return $this->enrolled_count < $this->max_students;
    }

    /**
     * Get available seats.
     */
    public function getAvailableSeatsAttribute(): int
    {
        return max(0, $this->max_students - $this->enrolled_count);
    }

    /**
     * Get section number (alias for section_name for backward compatibility).
     */
    public function getSectionNumberAttribute(): string
    {
        return $this->section_name ?? '';
    }

    /**
     * Get capacity (alias for max_students for backward compatibility).
     */
    public function getCapacityAttribute(): int
    {
        return $this->max_students ?? 0;
    }

    /**
     * Set capacity (alias for max_students for backward compatibility).
     */
    public function setCapacityAttribute(int $value): void
    {
        $this->attributes['max_students'] = $value;
    }

    /**
     * Generate a comprehensive report for this course offering.
     */
    public function generateCourseReport(): array
    {
        $enrollments = $this->enrollments()->with('grades')->get();
        $totalStudents = $enrollments->count();

        $passedStudents = 0;
        $failedStudents = 0;
        $totalGpa = 0;
        $gpaCount = 0;

        foreach ($enrollments as $enrollment) {
            $gpa = $enrollment->calculateGpa();
            if ($gpa !== null) {
                $totalGpa += $gpa;
                $gpaCount++;

                // Assuming passing GPA is 2.0 or higher
                if ($gpa >= 2.0) {
                    $passedStudents++;
                } else {
                    $failedStudents++;
                }
            }
        }

        $averageGpa = $gpaCount > 0 ? round($totalGpa / $gpaCount, 2) : 0;

        return [
            'total_students' => $totalStudents,
            'passed_students' => $passedStudents,
            'failed_students' => $failedStudents,
            'average_gpa' => $averageGpa,
            'enrollment_rate' => $this->max_students > 0 ? round(($totalStudents / $this->max_students) * 100, 2) : 0,
        ];
    }
}
