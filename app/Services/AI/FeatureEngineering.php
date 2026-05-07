<?php

namespace App\Services\AI;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;

class FeatureEngineering
{
    /**
     * Generate comprehensive feature set for model input
     */
    public function generateFeatures(int $courseId, int $semesterId): array
    {
        $course = Course::findOrFail($courseId);

        return [
            // Course Features
            'course_credits' => (float) $course->credits,
            'course_year_level' => (int) $course->year_level,
            'course_theory_hours' => (int) ($course->theory_hours ?? 0),
            'course_lab_hours' => (int) ($course->lab_hours ?? 0),
            'is_required_course' => (int) $this->isRequiredCourse($courseId),
            'is_active' => (int) $course->is_active,

            // Historical Features
            'historical_avg_enrollment' => $this->getHistoricalAverage($courseId),
            'historical_max_enrollment' => $this->getHistoricalMax($courseId),
            'historical_min_enrollment' => $this->getHistoricalMin($courseId),
            'historical_drop_rate' => $this->getHistoricalDropRate($courseId),
            'historical_fill_rate' => $this->getHistoricalFillRate($courseId),
            'historical_variance' => $this->getHistoricalVariance($courseId),
            'semester_count' => $this->getSemesterOfferedCount($courseId),

            // Department Features
            'department_total_students' => $this->getDepartmentStudentCount($course->department_id),
            'department_avg_course_size' => $this->getDepartmentAverageSize($course->department_id),
            'department_enrollment_growth' => $this->getDepartmentGrowthRate($course->department_id),
            'department_course_count' => $this->getDepartmentCourseCount($course->department_id),

            // Temporal Features
            'days_until_registration_close' => $this->getDaysUntilRegistrationClose($semesterId),
            'semester_phase' => $this->getSemesterPhase($semesterId),
            'is_first_year_course' => (int) ($course->year_level === 1),
            'is_final_year_course' => (int) ($course->year_level >= 4),

            // Prerequisite Features
            'prerequisite_count' => $this->getPrerequisiteCount($courseId),
            'has_related_courses' => (int) $this->hasRelatedCourses($courseId),

            // Competition Features
            'total_sections_offered' => $this->getTotalSections($courseId, $semesterId),
            'average_section_capacity' => $this->getAverageSectionCapacity($courseId, $semesterId),

            // Time-based Features
            'registration_week' => (int) now()->format('W'),
            'is_peak_registration' => (int) $this->isPeakRegistration(),
        ];
    }

    /**
     * Calculate fill rate trends
     */
    public function getHistoricalFillRate(int $courseId): float
    {
        $history = DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->selectRaw('AVG(enrolled_count / NULLIF(max_capacity, 0)) as fill_rate')
            ->first();

        return ($history->fill_rate ?? 0) * 100;
    }

    /**
     * Get historical average enrollment
     */
    public function getHistoricalAverage(int $courseId): float
    {
        return DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->avg('enrolled_count') ?? 0;
    }

    /**
     * Get historical max enrollment
     */
    public function getHistoricalMax(int $courseId): int
    {
        return DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->max('enrolled_count') ?? 0;
    }

    /**
     * Get historical min enrollment
     */
    public function getHistoricalMin(int $courseId): int
    {
        return DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->min('enrolled_count') ?? 0;
    }

    /**
     * Get historical drop rate
     */
    public function getHistoricalDropRate(int $courseId): float
    {
        $total = DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->sum('enrolled_count');

        $dropped = DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->sum('drop_count');

        return $total > 0 ? ($dropped / $total) * 100 : 0;
    }

    /**
     * Get historical variance
     */
    public function getHistoricalVariance(int $courseId): float
    {
        $enrollments = DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->pluck('enrolled_count');

        if ($enrollments->count() < 2) {
            return 0;
        }

        $mean = $enrollments->avg();
        $variance = $enrollments->map(fn ($e) => pow($e - $mean, 2))->avg();

        return sqrt($variance);
    }

    /**
     * Get count of semesters course was offered
     */
    public function getSemesterOfferedCount(int $courseId): int
    {
        return DB::table('enrollment_histories')
            ->where('course_id', $courseId)
            ->distinct('semester_id')
            ->count('semester_id');
    }

    /**
     * Check if course is required in any major
     */
    public function isRequiredCourse(int $courseId): bool
    {
        return DB::table('course_prerequisites')
            ->where('course_id', $courseId)
            ->where('type', 'required')
            ->exists();
    }

    /**
     * Get department student count
     */
    public function getDepartmentStudentCount(int $departmentId): int
    {
        // Get students in majors belonging to this department
        return DB::table('users')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->join('majors', 'user_profiles.major_id', '=', 'majors.id')
            ->where('majors.department_id', $departmentId)
            ->where('users.status', 'active')
            ->count();
    }

    /**
     * Get department average course size
     */
    public function getDepartmentAverageSize(int $departmentId): float
    {
        return DB::table('enrollment_histories')
            ->join('courses', 'enrollment_histories.course_id', '=', 'courses.id')
            ->where('courses.department_id', $departmentId)
            ->avg('enrolled_count') ?? 0;
    }

    /**
     * Get department enrollment growth rate
     */
    public function getDepartmentGrowthRate(int $departmentId): float
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $currentEnrollments = DB::table('enrollment_histories')
            ->join('courses', 'enrollment_histories.course_id', '=', 'courses.id')
            ->where('courses.department_id', $departmentId)
            ->whereYear('enrollment_date', $currentYear)
            ->sum('enrolled_count');

        $lastYearEnrollments = DB::table('enrollment_histories')
            ->join('courses', 'enrollment_histories.course_id', '=', 'courses.id')
            ->where('courses.department_id', $departmentId)
            ->whereYear('enrollment_date', $lastYear)
            ->sum('enrolled_count');

        if ($lastYearEnrollments === 0) {
            return 0;
        }

        return (($currentEnrollments - $lastYearEnrollments) / $lastYearEnrollments) * 100;
    }

    /**
     * Get department course count
     */
    public function getDepartmentCourseCount(int $departmentId): int
    {
        return Course::where('department_id', $departmentId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get days until registration closes
     */
    public function getDaysUntilRegistrationClose(int $semesterId): int
    {
        $semester = Semester::find($semesterId);

        if (! $semester || ! $semester->registration_deadline) {
            return 30; // Default to 30 days if no deadline set
        }

        return max(0, now()->diffInDays($semester->registration_deadline));
    }

    /**
     * Get semester phase (early, mid, late)
     */
    public function getSemesterPhase(int $semesterId): string
    {
        $semester = Semester::find($semesterId);

        if (! $semester) {
            return 'unknown';
        }

        if (! $semester->start_date) {
            return 'pre_registration';
        }

        $daysSinceStart = now()->diffInDays($semester->start_date);

        if ($daysSinceStart < -30) {
            return 'pre_registration';
        } elseif ($daysSinceStart < 0) {
            return 'registration';
        } elseif ($daysSinceStart < 30) {
            return 'early';
        } elseif ($daysSinceStart < 60) {
            return 'mid';
        } else {
            return 'late';
        }
    }

    /**
     * Get prerequisite count
     */
    public function getPrerequisiteCount(int $courseId): int
    {
        return DB::table('course_prerequisites')
            ->where('course_id', $courseId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Check if course has related courses
     */
    public function hasRelatedCourses(int $courseId): bool
    {
        $course = Course::find($courseId);

        if (! $course || ! $course->major_id) {
            return false;
        }

        // Check for courses in same major and year level
        return Course::where('major_id', $course->major_id)
            ->where('year_level', $course->year_level)
            ->where('id', '!=', $courseId)
            ->exists();
    }

    /**
     * Get total sections offered for course in semester
     */
    public function getTotalSections(int $courseId, int $semesterId): int
    {
        return CourseOffering::where('course_id', $courseId)
            ->where('semester_id', $semesterId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get average section capacity
     */
    public function getAverageSectionCapacity(int $courseId, int $semesterId): float
    {
        return CourseOffering::where('course_id', $courseId)
            ->where('semester_id', $semesterId)
            ->where('is_active', true)
            ->avg('max_students') ?? 0;
    }

    /**
     * Check if currently in peak registration period
     */
    public function isPeakRegistration(): bool
    {
        $month = now()->month;

        // Peak months: August, September, January
        return in_array($month, [8, 9, 1]);
    }

    /**
     * Get all features with default values for fallback
     */
    public function getDefaultFeatures(): array
    {
        return [
            'course_credits' => 3.0,
            'course_year_level' => 2,
            'course_theory_hours' => 3,
            'course_lab_hours' => 0,
            'is_required_course' => 0,
            'is_active' => 1,
            'historical_avg_enrollment' => 25,
            'historical_max_enrollment' => 40,
            'historical_min_enrollment' => 10,
            'historical_drop_rate' => 5.0,
            'historical_fill_rate' => 75.0,
            'historical_variance' => 5.0,
            'semester_count' => 3,
            'department_total_students' => 100,
            'department_avg_course_size' => 25,
            'department_enrollment_growth' => 0,
            'department_course_count' => 10,
            'days_until_registration_close' => 30,
            'semester_phase' => 'registration',
            'is_first_year_course' => 0,
            'is_final_year_course' => 0,
            'prerequisite_count' => 0,
            'has_related_courses' => 1,
            'total_sections_offered' => 1,
            'average_section_capacity' => 30,
            'registration_week' => (int) now()->format('W'),
            'is_peak_registration' => 0,
        ];
    }
}
