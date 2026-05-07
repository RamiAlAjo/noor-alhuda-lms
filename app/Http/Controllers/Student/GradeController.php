<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\StudentGrade;
use Illuminate\View\View;

class GradeController extends Controller
{
    /**
     * Display student's all grades overview.
     */
    public function index(): View
    {
        $student = auth()->user();

        // Get all approved enrollments with their grades
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with(['offering.course', 'offering.semester'])
            ->get();

        // Get all grades for the student
        $allGrades = StudentGrade::where('student_id', $student->id)
            ->whereNotNull('grade')
            ->with(['assessment', 'assessment.offering.course'])
            ->get();

        // Calculate overall GPA
        $gpa = $this->calculateGpa($allGrades);

        // Calculate cumulative credits
        $totalCredits = $allGrades->sum(function ($grade) {
            if (! $grade->assessment || ! $grade->assessment->offering) {
                return 0;
            }

            return $grade->assessment->offering->course->credits ?? 3;
        });

        return view('pages.student.grades.index', compact(
            'enrollments',
            'allGrades',
            'gpa',
            'totalCredits'
        ));
    }

    /**
     * Calculate GPA from grades.
     */
    private function calculateGpa($grades): float
    {
        if ($grades->isEmpty()) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            // Skip if assessment or offering is null
            if (! $grade->assessment || ! $grade->assessment->offering) {
                continue;
            }
            $percentage = $grade->percentage;
            $credits = $grade->assessment->offering->course->credits ?? 3;

            $points = $this->getGradePoints($percentage);
            $totalPoints += $points * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    /**
     * Convert percentage to grade points.
     */
    private function getGradePoints(float $percentage): float
    {
        return match (true) {
            $percentage >= 90 => 4.0,
            $percentage >= 80 => 3.7,
            $percentage >= 75 => 3.3,
            $percentage >= 70 => 3.0,
            $percentage >= 65 => 2.7,
            $percentage >= 60 => 2.3,
            $percentage >= 55 => 2.0,
            $percentage >= 50 => 1.7,
            default => 0.0,
        };
    }
}
