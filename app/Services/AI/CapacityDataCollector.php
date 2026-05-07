<?php

namespace App\Services\AI;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class CapacityDataCollector
{
    /**
     * Collect real-time enrollment data for analysis
     */
    public function collectOfferingMetrics(int $offeringId): array
    {
        $offering = CourseOffering::with(['course', 'semester'])->findOrFail($offeringId);

        return [
            'offering_id' => $offeringId,
            'current_enrollment' => $offering->enrolled_count,
            'max_capacity' => $offering->max_students,
            'available_seats' => $offering->available_seats,
            'fill_percentage' => $this->calculateFillRate($offering),
            'pending_enrollments' => $this->getPendingCount($offeringId),
            'recent_enrollment_velocity' => $this->calculateEnrollmentVelocity($offeringId),
            'historical_average' => $this->getHistoricalAverage($offering->course_id),
            'department_enrollment_trend' => $this->getDepartmentTrend($offering->course->department_id),
        ];
    }

    /**
     * Collect aggregated data for batch processing
     */
    public function collectBatchMetrics(int $semesterId): array
    {
        return CourseOffering::where('semester_id', $semesterId)
            ->with(['course.department', 'enrollments'])
            ->get()
            ->map(fn ($offering) => $this->collectOfferingMetrics($offering->id))
            ->toArray();
    }

    /**
     * Calculate enrollment velocity (enrollments per day)
     */
    public function calculateEnrollmentVelocity(int $offeringId): float
    {
        $recentEnrollments = Enrollment::where('course_offering_id', $offeringId)
            ->where('status', 'approved')
            ->where('enrolled_at', '>=', now()->subDays(14))
            ->count();

        return $recentEnrollments / 14;
    }

    /**
     * Get historical average enrollment for course
     */
    public function getHistoricalAverage(int $courseId): int
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
     * Calculate fill rate for an offering
     */
    private function calculateFillRate(CourseOffering $offering): float
    {
        if ($offering->max_students === 0) {
            return 0;
        }

        return ($offering->enrolled_count / $offering->max_students) * 100;
    }

    /**
     * Get pending enrollment count
     */
    private function getPendingCount(int $offeringId): int
    {
        return Enrollment::where('course_offering_id', $offeringId)
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Get department enrollment trend
     */
    private function getDepartmentTrend(int $departmentId): float
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $currentEnrollments = DB::table('enrollment_histories')
            ->whereHas('course', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->whereYear('enrollment_date', $currentYear)
            ->sum('enrolled_count');

        $lastYearEnrollments = DB::table('enrollment_histories')
            ->whereHas('course', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->whereYear('enrollment_date', $lastYear)
            ->sum('enrolled_count');

        if ($lastYearEnrollments === 0) {
            return 0;
        }

        return (($currentEnrollments - $lastYearEnrollments) / $lastYearEnrollments) * 100;
    }

    /**
     * Get enrollment data for a specific time period
     */
    public function getEnrollmentTimeSeries(int $courseId, int $months = 6): array
    {
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $enrollments = DB::table('enrollment_histories')
                ->where('course_id', $courseId)
                ->whereYear('enrollment_date', $date->year)
                ->whereMonth('enrollment_date', $date->month)
                ->sum('enrolled_count');

            $data[] = [
                'month' => $date->format('M Y'),
                'enrollments' => $enrollments,
            ];
        }

        return $data;
    }

    /**
     * Collect all courses with their current metrics
     */
    public function collectAllCoursesMetrics(int $semesterId): array
    {
        $offerings = CourseOffering::where('semester_id', $semesterId)
            ->with(['course.department', 'teacher'])
            ->get();

        return $offerings->map(function ($offering) {
            $metrics = $this->collectOfferingMetrics($offering->id);

            return array_merge($metrics, [
                'course_name' => $offering->course->name,
                'course_code' => $offering->course->code,
                'section_name' => $offering->section_name,
                'teacher_name' => $offering->teacher?->name,
                'department_name' => $offering->course->department?->name,
            ]);
        })->toArray();
    }

    /**
     * Get capacity utilization statistics
     */
    public function getCapacityStats(int $semesterId): array
    {
        $offerings = CourseOffering::where('semester_id', $semesterId)->get();

        $totalCapacity = $offerings->sum('max_students');
        $totalEnrolled = $offerings->sum('enrolled_count');

        $overCapacity = $offerings->filter(function ($o) {
            return $o->enrolled_count >= $o->max_students;
        })->count();

        $underUtilized = $offerings->filter(function ($o) {
            $rate = $o->max_students > 0 ? ($o->enrolled_count / $o->max_students) * 100 : 0;

            return $rate < 30;
        })->count();

        $optimal = $offerings->filter(function ($o) {
            $rate = $o->max_students > 0 ? ($o->enrolled_count / $o->max_students) * 100 : 0;

            return $rate >= 50 && $rate <= 85;
        })->count();

        return [
            'total_offerings' => $offerings->count(),
            'total_capacity' => $totalCapacity,
            'total_enrolled' => $totalEnrolled,
            'overall_utilization' => $totalCapacity > 0 ? ($totalEnrolled / $totalCapacity) * 100 : 0,
            'overcapacity_count' => $overCapacity,
            'underutilized_count' => $underUtilized,
            'optimal_count' => $optimal,
        ];
    }
}
