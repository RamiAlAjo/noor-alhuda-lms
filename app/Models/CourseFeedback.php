<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseFeedback extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_feedback';

    protected $fillable = [
        'course_offering_id',
        'student_id',
        'semester_id',
        'overall_rating',
        'content_quality',
        'instructor_knowledge',
        'instructor_communication',
        'course_organization',
        'materials_quality',
        'workload_appropriateness',
        'strengths',
        'improvements',
        'additional_comments',
        'is_anonymous',
        'is_submitted',
        'submitted_at',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the course offering.
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the student (if not anonymous).
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the semester.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Check if feedback is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->is_submitted;
    }

    /**
     * Submit the feedback.
     */
    public function submit(): void
    {
        $this->update([
            'is_submitted' => true,
            'submitted_at' => now(),
        ]);
    }

    /**
     * Get the average rating.
     */
    public function getAverageRatingAttribute(): float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->content_quality,
            $this->instructor_knowledge,
            $this->instructor_communication,
            $this->course_organization,
            $this->materials_quality,
            $this->workload_appropriateness,
        ]);

        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 2) : 0;
    }

    /**
     * Scope to get submitted feedback.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('is_submitted', true);
    }

    /**
     * Scope to get feedback for a course.
     */
    public function scopeForCourse($query, int $courseOfferingId)
    {
        return $query->where('course_offering_id', $courseOfferingId);
    }

    /**
     * Get rating categories.
     */
    public static function getRatingCategories(): array
    {
        return [
            'overall_rating' => __('lms.overall_rating'),
            'content_quality' => __('lms.content_quality'),
            'instructor_knowledge' => __('lms.instructor_knowledge'),
            'instructor_communication' => __('lms.instructor_communication'),
            'course_organization' => __('lms.course_organization'),
            'materials_quality' => __('lms.materials_quality'),
            'workload_appropriateness' => __('lms.workload_appropriateness'),
        ];
    }

    /**
     * Get average ratings for a course.
     */
    public static function getAverageRatingsForCourse(int $courseOfferingId): array
    {
        $feedbacks = static::forCourse($courseOfferingId)->submitted()->get();

        if ($feedbacks->isEmpty()) {
            return [];
        }

        $categories = static::getRatingCategories();
        $averages = [];

        foreach (array_keys($categories) as $category) {
            $values = $feedbacks->pluck($category)->filter();
            $averages[$category] = $values->count() > 0
                ? round($values->avg(), 2)
                : null;
        }

        return $averages;
    }
}
