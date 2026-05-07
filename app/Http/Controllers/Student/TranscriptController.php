<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TranscriptController extends Controller
{
    /**
     * Display student's academic transcript.
     */
    public function index(): View
    {
        $user = Auth::user();

        $transcriptData = $this->getTranscriptData($user);

        return view('pages.student.transcript', $transcriptData);
    }

    /**
     * Export transcript as PDF.
     */
    public function exportPdf()
    {
        $user = Auth::user();

        $transcriptData = $this->getTranscriptData($user);

        // For now, return a view that can be printed as PDF
        // In production, you would use a PDF library like dompdf or snappy
        return view('pages.student.transcript-pdf', $transcriptData);
    }

    /**
     * Get transcript data for a student.
     */
    private function getTranscriptData(User $user): array
    {
        // Get all completed enrollments with grades
        $enrollments = Enrollment::with([
            'offering.course.department',
            'offering.semester.academicYear',
            'offering.teacher',
        ])
            ->where('student_id', $user->id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by academic year and semester
        $semesters = $enrollments->groupBy(function ($enrollment) {
            return $enrollment->offering->semester?->academicYear?->name ?? 'Unknown Year';
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($enrollment) {
                return $enrollment->offering->semester?->name ?? 'Unknown Semester';
            });
        });

        // Get grades for each enrollment
        $grades = StudentGrade::with(['assessment.offering.course'])
            ->where('student_id', $user->id)
            ->get()
            ->groupBy('assessment.course_offering_id');

        // Calculate course grades and credits
        $courses = $enrollments->map(function ($enrollment) use ($grades) {
            // Skip if offering is null
            if (! $enrollment->offering) {
                return null;
            }
            $courseGrades = $grades->get($enrollment->course_offering_id, collect());
            $course = $enrollment->offering->course;

            // Skip if course is null
            if (! $course) {
                return null;
            }

            // Calculate average grade for the course
            $averageGrade = $courseGrades->isNotEmpty()
                ? $courseGrades->avg('grade')
                : null;

            // Get letter grade
            $letterGrade = $averageGrade ? $this->getLetterGrade($averageGrade) : null;

            // Get grade points
            $gradePoints = $letterGrade ? $this->getGradePoints($letterGrade) : null;

            return [
                'enrollment' => $enrollment,
                'course' => $course,
                'credits' => $course->credits ?? 3,
                'average_grade' => $averageGrade,
                'letter_grade' => $letterGrade,
                'grade_points' => $gradePoints,
                'quality_points' => $gradePoints ? $gradePoints * ($course->credits ?? 3) : 0,
            ];
        });

        // Calculate totals
        $totalCredits = $courses->filter()->sum('credits');
        $totalQualityPoints = $courses->filter()->sum('quality_points');
        $gpa = $totalCredits > 0 ? round($totalQualityPoints / $totalCredits, 2) : 0;

        // Calculate cumulative stats
        $completedCourses = $courses->filter()->where('letter_grade', '!=', null)->count();
        $passedCourses = $courses->filter()->whereIn('letter_grade', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D'])->count();

        return [
            'user' => $user,
            'semesters' => $semesters,
            'courses' => $courses,
            'totalCredits' => $totalCredits,
            'totalQualityPoints' => $totalQualityPoints,
            'gpa' => $gpa,
            'completedCourses' => $completedCourses,
            'passedCourses' => $passedCourses,
        ];
    }

    /**
     * Get letter grade from numeric grade.
     */
    private function getLetterGrade(float $numericGrade): string
    {
        if ($numericGrade >= 95) {
            return 'A';
        }
        if ($numericGrade >= 90) {
            return 'A-';
        }
        if ($numericGrade >= 87) {
            return 'B+';
        }
        if ($numericGrade >= 83) {
            return 'B';
        }
        if ($numericGrade >= 80) {
            return 'B-';
        }
        if ($numericGrade >= 77) {
            return 'C+';
        }
        if ($numericGrade >= 73) {
            return 'C';
        }
        if ($numericGrade >= 70) {
            return 'C-';
        }
        if ($numericGrade >= 67) {
            return 'D+';
        }
        if ($numericGrade >= 60) {
            return 'D';
        }

        return 'F';
    }

    /**
     * Get grade points from letter grade.
     */
    private function getGradePoints(string $letterGrade): float
    {
        $points = [
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
            'F' => 0.0,
        ];

        return $points[$letterGrade] ?? 0.0;
    }

    /**
     * Admin view of student transcript.
     */
    public function adminView(User $student): View
    {
        $transcriptData = $this->getTranscriptData($student);

        return view('pages.admin.transcript.show', $transcriptData);
    }

    /**
     * Admin export of student transcript.
     */
    public function adminExportPdf(User $student)
    {
        $transcriptData = $this->getTranscriptData($student);

        return view('pages.student.transcript-pdf', $transcriptData);
    }
}
