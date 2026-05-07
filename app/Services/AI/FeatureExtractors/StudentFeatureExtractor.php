<?php

namespace App\Services\AI\FeatureExtractors;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Extracts and normalizes student features for ML prediction
 *
 * This class is responsible for extracting academic, engagement, temporal,
 * and peer comparison features from student data for use in ML predictions.
 */
class StudentFeatureExtractor
{
    /**
     * Extract features from raw input
     *
     * @param  array  $rawFeatures  Raw input containing student_id and course_id
     * @return array Normalized features for ML prediction
     *
     * @throws \InvalidArgumentException If required input is missing
     */
    public function extract(array $rawFeatures): array
    {
        if (! isset($rawFeatures['student_id']) || ! isset($rawFeatures['course_id'])) {
            throw new \InvalidArgumentException('student_id and course_id are required');
        }

        $studentId = (int) $rawFeatures['student_id'];
        $courseId = (int) $rawFeatures['course_id'];

        if ($studentId <= 0 || $courseId <= 0) {
            throw new \InvalidArgumentException('student_id and course_id must be positive integers');
        }

        return [
            // Academic features
            'historical_gpa' => $this->calculateHistoricalGPA($studentId),
            'course_gpa' => $this->calculateCourseGPA($studentId, $courseId),
            'grade_trend' => $this->calculateGradeTrend($studentId, $courseId),
            'grade_consistency' => $this->calculateGradeConsistency($studentId),

            // Engagement features
            'attendance_rate' => $this->getAttendanceRate($studentId, $courseId),
            'assignment_completion' => $this->getAssignmentCompletion($studentId, $courseId),
            'quiz_average' => $this->getQuizAverage($studentId, $courseId),
            'late_submission_rate' => $this->getLateSubmissionRate($studentId, $courseId),

            // Temporal features
            'days_since_enrollment' => $this->getDaysSinceEnrollment($studentId, $courseId),
            'semester_progress' => $this->getSemesterProgress($courseId),
            'is_weekend' => (int) now()->isWeekend(),
            'hour_of_day' => now()->hour,

            // Course features
            'course_difficulty' => $this->getCourseDifficulty($courseId),
            'course_credits' => $this->getCourseCredits($courseId),
            'prerequisite_count' => $this->getPrerequisiteCount($courseId),

            // Peer comparison features
            'percentile_rank' => $this->getPercentileRank($studentId, $courseId),
            'class_average' => $this->getClassAverage($courseId),
            'deviation_from_mean' => $this->getDeviationFromMean($studentId, $courseId),
        ];
    }

    /**
     * Calculate historical GPA with eager loading to prevent N+1 queries
     */
    private function calculateHistoricalGPA(int $studentId): float
    {
        try {
            $grades = Grade::where('student_id', $studentId)
                ->where('status', 'completed')
                ->with('course:id,credits')
                ->get();

            if ($grades->isEmpty()) {
                return 0.0;
            }

            $totalPoints = 0.0;
            $totalCredits = 0;

            foreach ($grades as $grade) {
                $credits = $grade->course?->credits ?? 3;
                $points = $this->gradeToPoint($grade->letter_grade);

                $totalPoints += $points * $credits;
                $totalCredits += $credits;
            }

            return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
        } catch (\Exception $e) {
            Log::error('Error calculating historical GPA', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Calculate GPA for specific course
     */
    private function calculateCourseGPA(int $studentId, int $courseId): float
    {
        try {
            $grades = Grade::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('status', 'completed')
                ->get();

            if ($grades->isEmpty()) {
                return 0.0;
            }

            $totalPoints = 0.0;
            foreach ($grades as $grade) {
                $totalPoints += $this->gradeToPoint($grade->letter_grade);
            }

            return round($totalPoints / $grades->count(), 2);
        } catch (\Exception $e) {
            Log::error('Error calculating course GPA', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Calculate grade trend (improving/declining) with division by zero protection
     *
     * @return float Slope of linear regression (positive = improving, negative = declining)
     */
    private function calculateGradeTrend(int $studentId, int $courseId): float
    {
        try {
            $grades = Grade::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->pluck('numeric_grade')
                ->toArray();

            if (count($grades) < 2) {
                return 0.0;
            }

            // Calculate linear regression slope
            $n = count($grades);
            $sumX = 0.0;
            $sumY = 0.0;
            $sumXY = 0.0;
            $sumX2 = 0.0;

            for ($i = 0; $i < $n; $i++) {
                $sumX += $i;
                $sumY += $grades[$i];
                $sumXY += $i * $grades[$i];
                $sumX2 += $i * $i;
            }

            $denominator = ($n * $sumX2) - ($sumX * $sumX);

            // FIX: Prevent division by zero
            if (abs($denominator) < 0.0001) {
                return 0.0;
            }

            $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;

            return round($slope, 4);
        } catch (\Exception $e) {
            Log::error('Error calculating grade trend', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Calculate grade consistency (standard deviation) with division by zero protection
     *
     * @return float Standard deviation of grades
     */
    private function calculateGradeConsistency(int $studentId): float
    {
        try {
            $grades = Grade::where('student_id', $studentId)
                ->where('status', 'completed')
                ->pluck('numeric_grade')
                ->toArray();

            // FIX: Return 0.0 for insufficient data
            if (count($grades) < 2) {
                return 0.0;
            }

            $mean = array_sum($grades) / count($grades);
            $variance = array_sum(array_map(fn ($g) => pow($g - $mean, 2), $grades)) / count($grades);

            return round(sqrt($variance), 2);
        } catch (\Exception $e) {
            Log::error('Error calculating grade consistency', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Get attendance rate with parameterized queries
     */
    private function getAttendanceRate(int $studentId, int $courseId): float
    {
        try {
            $total = DB::table('attendance_records')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->count();

            if ($total === 0) {
                return 1.0;
            }

            $present = DB::table('attendance_records')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('status', 'present')
                ->count();

            return round($present / $total, 2);
        } catch (\Exception $e) {
            Log::error('Error getting attendance rate', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 1.0;
        }
    }

    /**
     * Get assignment completion rate
     */
    private function getAssignmentCompletion(int $studentId, int $courseId): float
    {
        try {
            $total = DB::table('assignments')
                ->where('course_id', $courseId)
                ->count();

            if ($total === 0) {
                return 1.0;
            }

            $submitted = DB::table('submissions')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->count();

            return round($submitted / $total, 2);
        } catch (\Exception $e) {
            Log::error('Error getting assignment completion', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 1.0;
        }
    }

    /**
     * Get quiz average
     */
    private function getQuizAverage(int $studentId, int $courseId): float
    {
        try {
            $average = DB::table('quiz_attempts')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->avg('score');

            return round($average ?? 0.0, 2);
        } catch (\Exception $e) {
            Log::error('Error getting quiz average', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Get late submission rate
     */
    private function getLateSubmissionRate(int $studentId, int $courseId): float
    {
        try {
            $total = DB::table('submissions')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->count();

            if ($total === 0) {
                return 0.0;
            }

            $late = DB::table('submissions')
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->where('is_late', true)
                ->count();

            return round($late / $total, 2);
        } catch (\Exception $e) {
            Log::error('Error getting late submission rate', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Get days since enrollment
     */
    private function getDaysSinceEnrollment(int $studentId, int $courseId): int
    {
        try {
            $enrollment = Enrollment::where('student_id', $studentId)
                ->whereHas('courseOffering', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->first();

            if (! $enrollment) {
                return 0;
            }

            return now()->diffInDays($enrollment->created_at);
        } catch (\Exception $e) {
            Log::error('Error getting days since enrollment', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get semester progress (0-1)
     */
    private function getSemesterProgress(int $courseId): float
    {
        try {
            $course = Course::find($courseId);

            if (! $course) {
                return 0.5;
            }

            $offering = $course->offerings()
                ->where('is_active', true)
                ->first();

            if (! $offering || ! $offering->semester) {
                return 0.5;
            }

            $semester = $offering->semester;
            $totalDays = $semester->start_date->diffInDays($semester->end_date);
            $elapsedDays = $semester->start_date->diffInDays(now());

            return $totalDays > 0 ? min(1.0, round($elapsedDays / $totalDays, 2)) : 0.5;
        } catch (\Exception $e) {
            Log::error('Error getting semester progress', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.5;
        }
    }

    /**
     * Get course difficulty score with division by zero protection
     */
    private function getCourseDifficulty(int $courseId): float
    {
        try {
            $course = Course::find($courseId);

            if (! $course) {
                return 0.5;
            }

            // FIX: Use single query to calculate pass rate
            $stats = DB::table('enrollments')
                ->where('course_id', $courseId)
                ->where('status', 'completed')
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN final_grade >= ? THEN 1 ELSE 0 END) as passed', ['C'])
                ->first();

            if (! $stats || $stats->total === 0) {
                return 0.5;
            }

            $passRate = $stats->passed / $stats->total;

            return round(1.0 - $passRate, 2); // Higher value = more difficult
        } catch (\Exception $e) {
            Log::error('Error getting course difficulty', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.5;
        }
    }

    /**
     * Get course credits
     */
    private function getCourseCredits(int $courseId): int
    {
        try {
            return Course::find($courseId)?->credits ?? 3;
        } catch (\Exception $e) {
            Log::error('Error getting course credits', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 3;
        }
    }

    /**
     * Get prerequisite count
     */
    private function getPrerequisiteCount(int $courseId): int
    {
        try {
            return DB::table('course_prerequisites')
                ->where('course_id', $courseId)
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting prerequisite count', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Get percentile rank in class
     */
    private function getPercentileRank(int $studentId, int $courseId): float
    {
        try {
            $studentGrade = Grade::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->value('numeric_grade');

            if (! $studentGrade) {
                return 0.5;
            }

            $totalStudents = Grade::where('course_id', $courseId)
                ->where('status', 'completed')
                ->count();

            if ($totalStudents === 0) {
                return 0.5;
            }

            $belowStudent = Grade::where('course_id', $courseId)
                ->where('status', 'completed')
                ->where('numeric_grade', '<', $studentGrade)
                ->count();

            return round($belowStudent / $totalStudents, 2);
        } catch (\Exception $e) {
            Log::error('Error getting percentile rank', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.5;
        }
    }

    /**
     * Get class average
     */
    private function getClassAverage(int $courseId): float
    {
        try {
            return round(Grade::where('course_id', $courseId)
                ->where('status', 'completed')
                ->avg('numeric_grade') ?? 0.0, 2);
        } catch (\Exception $e) {
            Log::error('Error getting class average', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Get deviation from class mean
     */
    private function getDeviationFromMean(int $studentId, int $courseId): float
    {
        try {
            $studentGrade = Grade::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->value('numeric_grade');

            $classAverage = $this->getClassAverage($courseId);

            return $studentGrade ? round($studentGrade - $classAverage, 2) : 0.0;
        } catch (\Exception $e) {
            Log::error('Error getting deviation from mean', [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }

    /**
     * Convert letter grade to points
     */
    private function gradeToPoint(string $letterGrade): float
    {
        return match (strtoupper(trim($letterGrade))) {
            'A', 'A+' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D+' => 1.3,
            'D' => 1.0,
            'D-' => 0.7,
            'F' => 0.0,
            default => 0.0,
        };
    }
}
