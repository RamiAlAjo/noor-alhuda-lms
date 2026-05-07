<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'major_id',
        'code',
        'name',
        'name_ar',
        'credits',
        'description',
        'description_ar',
        'theory_hours',
        'lab_hours',
        'year_level',
        'semester_available',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the major that the course belongs to.
     */
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * Alias for sections (offerings).
     */
    public function offerings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function majors(): BelongsToMany
    {
        return $this->belongsToMany(Major::class);
    }

    /**
     * Get the prerequisites for this course.
     */
    public function prerequisites(): HasMany
    {
        return $this->hasMany(CoursePrerequisite::class, 'course_id');
    }

    /**
     * Get courses that have this course as a prerequisite.
     */
    public function requiredBy(): HasMany
    {
        return $this->hasMany(CoursePrerequisite::class, 'prerequisite_course_id');
    }

    /**
     * Check if a student has completed all prerequisites for this course.
     */
    public function hasCompletedPrerequisites(int $studentId): bool
    {
        // Get all required active prerequisites
        $prerequisiteCourseIds = $this->prerequisites()
            ->where('is_active', true)
            ->where('type', 'required')
            ->pluck('prerequisite_course_id');

        // If no required prerequisites, student can enroll
        if ($prerequisiteCourseIds->isEmpty()) {
            return true;
        }

        // Get all offering IDs for the prerequisite courses
        $passedOfferingIds = CourseOffering::whereIn('course_id', $prerequisiteCourseIds)
            ->pluck('id');

        // If no offerings exist for prerequisites, return false (cannot verify)
        if ($passedOfferingIds->isEmpty()) {
            return false;
        }

        // Get assessments from prerequisite offerings and check if student passed
        $passedAssessments = StudentGrade::where('student_id', $studentId)
            ->whereHas('assessment', function ($q) use ($passedOfferingIds) {
                $q->whereIn('course_offering_id', $passedOfferingIds);
            })
            ->where('passed', true)
            ->exists();

        return $passedAssessments;
    }

    /**
     * Get the assignments for this course.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the quiz attempts for this course through course offerings.
     */
    public function quizAttempts(): HasManyThrough
    {
        return $this->hasManyThrough(
            QuizAttempt::class,
            CourseOffering::class,
            'course_id', // Foreign key on course_offerings table
            'course_offering_id', // Foreign key on quiz_attempts table
            'id', // Local key on courses table
            'id' // Local key on course_offerings table
        );
    }

    /**
     * Get the attendance records for this course through course offerings.
     */
    public function attendanceRecords(): HasManyThrough
    {
        return $this->hasManyThrough(
            AttendanceRecord::class,
            CourseOffering::class,
            'course_id', // Foreign key on course_offerings table
            'course_offering_id', // Foreign key on attendance_records table
            'id', // Local key on courses table
            'id' // Local key on course_offerings table
        );
    }

    /**
     * Get the submissions for this course through assignments.
     */
    public function submissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Submission::class,
            Assignment::class,
            'course_id', // Foreign key on assignments table
            'assignment_id', // Foreign key on submissions table
            'id', // Local key on courses table
            'id' // Local key on assignments table
        );
    }
}
