<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * GradeService
 *
 * Handles grading business logic including grade calculation,
 * GPA computation, and grade management operations.
 */
class GradeService
{
    /**
     * Record a grade for a student.
     *
     *
     * @throws \Exception
     */
    public function recordGrade(
        int $studentId,
        int $assessmentId,
        float $score,
        ?int $gradedBy = null,
        ?string $feedback = null
    ): StudentGrade {
        return DB::transaction(function () use ($studentId, $assessmentId, $score, $gradedBy, $feedback) {
            // Validate student exists
            $student = User::findOrFail($studentId);

            // Validate assessment exists
            $assessment = Assessment::findOrFail($assessmentId);

            // Validate score is within range
            if ($score < 0 || $score > $assessment->max_score) {
                throw new \Exception("Score must be between 0 and {$assessment->max_score}.");
            }

            // Check if grade already exists
            $existingGrade = StudentGrade::where('student_id', $studentId)
                ->where('assessment_id', $assessmentId)
                ->first();

            if ($existingGrade) {
                // Update existing grade
                $existingGrade->update([
                    'grade' => $score,
                    'graded_by' => $gradedBy,
                    'feedback' => $feedback ? strip_tags($feedback) : null,
                    'graded_at' => now(),
                ]);

                Log::info('Grade updated', [
                    'student_id' => $studentId,
                    'assessment_id' => $assessmentId,
                    'score' => $score,
                ]);

                return $existingGrade->fresh();
            }

            // Create new grade
            $grade = StudentGrade::create([
                'student_id' => $studentId,
                'assessment_id' => $assessmentId,
                'grade' => $score,
                'graded_by' => $gradedBy,
                'feedback' => $feedback ? strip_tags($feedback) : null,
                'graded_at' => now(),
            ]);

            Log::info('Grade recorded', [
                'student_id' => $studentId,
                'assessment_id' => $assessmentId,
                'score' => $score,
            ]);

            return $grade;
        }, 5);
    }

    /**
     * Calculate GPA for a student.
     */
    public function calculateGPA(int $studentId, ?int $semesterId = null): float
    {
        $query = StudentGrade::where('student_id', $studentId)
            ->whereHas('assessment', function ($q) {
                $q->where('is_published', true);
            });

        if ($semesterId) {
            $query->whereHas('assessment.courseOffering', function ($q) use ($semesterId) {
                $q->where('semester_id', $semesterId);
            });
        }

        $grades = $query->with('assessment.courseOffering.course')->get();

        if ($grades->isEmpty()) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $course = $grade->assessment->courseOffering->course ?? null;
            if (! $course) {
                continue;
            }

            $credits = $course->credits ?? 3;
            $percentage = ($grade->score / $grade->assessment->max_score) * 100;
            $gradePoints = $this->percentageToGradePoints($percentage);

            $totalPoints += $gradePoints * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    /**
     * Get student grades for a course offering.
     */
    public function getStudentGradesForCourse(int $studentId, int $courseOfferingId): Collection
    {
        return StudentGrade::where('student_id', $studentId)
            ->whereHas('assessment', function ($q) use ($courseOfferingId) {
                $q->where('course_offering_id', $courseOfferingId);
            })
            ->with(['assessment', 'assessment.assessmentType'])
            ->get();
    }

    /**
     * Calculate final grade for a student in a course.
     */
    public function calculateFinalGrade(int $studentId, int $courseOfferingId): array
    {
        $grades = $this->getStudentGradesForCourse($studentId, $courseOfferingId);

        if ($grades->isEmpty()) {
            return [
                'percentage' => 0,
                'letter_grade' => 'N/A',
                'grade_points' => 0,
                'total_score' => 0,
                'max_possible' => 0,
            ];
        }

        $totalScore = 0;
        $maxPossible = 0;

        foreach ($grades as $grade) {
            $weight = $grade->assessment->assessmentType->weight ?? 1;
            $totalScore += $grade->score * $weight;
            $maxPossible += $grade->assessment->max_score * $weight;
        }

        $percentage = $maxPossible > 0 ? round(($totalScore / $maxPossible) * 100, 2) : 0;
        $letterGrade = $this->percentageToLetterGrade($percentage);
        $gradePoints = $this->percentageToGradePoints($percentage);

        return [
            'percentage' => $percentage,
            'letter_grade' => $letterGrade,
            'grade_points' => $gradePoints,
            'total_score' => $totalScore,
            'max_possible' => $maxPossible,
        ];
    }

    /**
     * Get grade statistics for an assessment.
     */
    public function getAssessmentStatistics(int $assessmentId): array
    {
        $grades = StudentGrade::where('assessment_id', $assessmentId)->get();

        if ($grades->isEmpty()) {
            return [
                'count' => 0,
                'average' => 0,
                'median' => 0,
                'min' => 0,
                'max' => 0,
                'std_dev' => 0,
            ];
        }

        $scores = $grades->pluck('score')->toArray();
        sort($scores);

        $count = count($scores);
        $sum = array_sum($scores);
        $average = $sum / $count;
        $median = $this->calculateMedian($scores);
        $stdDev = $this->calculateStdDev($scores, $average);

        return [
            'count' => $count,
            'average' => round($average, 2),
            'median' => round($median, 2),
            'min' => min($scores),
            'max' => max($scores),
            'std_dev' => round($stdDev, 2),
        ];
    }

    /**
     * Convert percentage to letter grade.
     */
    private function percentageToLetterGrade(float $percentage): string
    {
        $scale = config('grading.scale', [
            'A' => 93,
            'A-' => 90,
            'B+' => 87,
            'B' => 83,
            'B-' => 80,
            'C+' => 77,
            'C' => 73,
            'C-' => 70,
            'D+' => 67,
            'D' => 63,
            'D-' => 60,
        ]);

        foreach ($scale as $grade => $threshold) {
            if ($percentage >= $threshold) {
                return $grade;
            }
        }

        return 'F';
    }

    /**
     * Convert percentage to grade points.
     */
    private function percentageToGradePoints(float $percentage): float
    {
        $gradePoints = config('grading.grade_points', [
            'A' => 4.0,
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
        ]);

        $letterGrade = $this->percentageToLetterGrade($percentage);

        return $gradePoints[$letterGrade] ?? 0.0;
    }

    /**
     * Calculate median of an array.
     */
    private function calculateMedian(array $scores): float
    {
        $count = count($scores);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return ($scores[$middle - 1] + $scores[$middle]) / 2;
        }

        return $scores[$middle];
    }

    /**
     * Calculate standard deviation.
     */
    private function calculateStdDev(array $scores, float $mean): float
    {
        $count = count($scores);
        if ($count <= 1) {
            return 0.0;
        }

        $variance = array_sum(array_map(fn ($x) => pow($x - $mean, 2), $scores)) / $count;

        return sqrt($variance);
    }
}
