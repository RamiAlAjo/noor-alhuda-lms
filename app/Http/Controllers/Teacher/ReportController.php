<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display teacher reports dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Get teacher's course offerings
        $offerings = CourseOffering::with(['course', 'semester'])
            ->where('teacher_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $offeringId = $request->get('offering_id');

        $report = null;

        if ($offeringId) {
            $report = $this->getClassPerformanceReport($offeringId, $user->id);
        }

        return view('pages.teacher.reports.index', compact('offerings', 'offeringId', 'report'));
    }

    /**
     * Get class performance report.
     */
    private function getClassPerformanceReport(int $offeringId, int $teacherId): array
    {
        $offering = CourseOffering::with(['course', 'semester'])
            ->where('teacher_id', $teacherId)
            ->findOrFail($offeringId);

        // Get enrolled students
        $enrollments = Enrollment::with('student')
            ->where('course_offering_id', $offeringId)
            ->where('status', 'approved')
            ->get();

        $studentIds = $enrollments->pluck('student_id');

        // Get assessments
        $assessments = Assessment::where('course_offering_id', $offeringId)
            ->orderBy('created_at')
            ->get();

        // Get grades
        $grades = StudentGrade::whereIn('student_id', $studentIds)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get();

        // Calculate statistics per assessment
        $assessmentStats = $assessments->map(function ($assessment) use ($grades) {
            $assessmentGrades = $grades->where('assessment_id', $assessment->id);

            return [
                'assessment' => $assessment,
                'count' => $assessmentGrades->count(),
                'average' => $assessmentGrades->avg('grade'),
                'min' => $assessmentGrades->min('grade'),
                'max' => $assessmentGrades->max('grade'),
                'std_dev' => $this->calculateStdDev($assessmentGrades->pluck('grade')->toArray()),
            ];
        });

        // Calculate student performance
        $studentPerformance = $enrollments->map(function ($enrollment) use ($grades, $assessments) {
            $studentGrades = $grades->where('student_id', $enrollment->student_id);

            $totalGrade = 0;
            $totalWeight = 0;

            foreach ($assessments as $assessment) {
                $grade = $studentGrades->where('assessment_id', $assessment->id)->first();
                if ($grade) {
                    $weight = $assessment->weight ?? 1;
                    $totalGrade += $grade->grade * $weight;
                    $totalWeight += $weight;
                }
            }

            $weightedAverage = $totalWeight > 0 ? $totalGrade / $totalWeight : null;

            return [
                'student' => $enrollment->student,
                'grades' => $studentGrades,
                'average' => $studentGrades->avg('grade'),
                'weighted_average' => $weightedAverage,
                'completed_assessments' => $studentGrades->count(),
                'total_assessments' => $assessments->count(),
            ];
        })->sortByDesc('weighted_average');

        // Grade distribution
        $gradeDistribution = [
            'A' => 0, 'A-' => 0, 'B+' => 0, 'B' => 0, 'B-' => 0,
            'C+' => 0, 'C' => 0, 'C-' => 0, 'D+' => 0, 'D' => 0, 'F' => 0,
        ];

        foreach ($studentPerformance as $performance) {
            if ($performance['weighted_average']) {
                $letterGrade = $this->getLetterGrade($performance['weighted_average']);
                $gradeDistribution[$letterGrade]++;
            }
        }

        // Overall class statistics
        $allAverages = $studentPerformance->pluck('weighted_average')->filter();
        $classAverage = $allAverages->avg();
        $classStdDev = $this->calculateStdDev($allAverages->toArray());
        $highestScore = $allAverages->max();
        $lowestScore = $allAverages->min();

        return [
            'offering' => $offering,
            'enrollments' => $enrollments,
            'assessments' => $assessments,
            'assessment_stats' => $assessmentStats,
            'student_performance' => $studentPerformance,
            'grade_distribution' => $gradeDistribution,
            'class_average' => $classAverage,
            'class_std_dev' => $classStdDev,
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,
            'total_students' => $enrollments->count(),
            'total_assessments' => $assessments->count(),
        ];
    }

    /**
     * Display student progress report.
     */
    public function studentProgress(Request $request, ?int $studentId = null): View
    {
        $user = Auth::user();

        // Get teacher's course offerings
        $offerings = CourseOffering::with(['course', 'semester'])
            ->where('teacher_id', $user->id)
            ->get();

        $offeringId = $request->get('offering_id');
        $selectedStudentId = $studentId ?? $request->get('student_id');

        $report = null;
        $students = collect();

        if ($offeringId) {
            // Get students in this offering
            $students = Enrollment::with('student')
                ->where('course_offering_id', $offeringId)
                ->where('status', 'approved')
                ->get()
                ->pluck('student');

            if ($selectedStudentId) {
                $report = $this->getStudentProgressReport($offeringId, $selectedStudentId, $user->id);
            }
        }

        return view('pages.teacher.reports.student-progress', compact('offerings', 'students', 'offeringId', 'selectedStudentId', 'report'));
    }

    /**
     * Get student progress report.
     */
    private function getStudentProgressReport(int $offeringId, int $studentId, int $teacherId): array
    {
        $offering = CourseOffering::with(['course', 'semester'])
            ->where('teacher_id', $teacherId)
            ->findOrFail($offeringId);

        $student = User::findOrFail($studentId);

        // Verify student is enrolled
        $enrollment = Enrollment::where('course_offering_id', $offeringId)
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->firstOrFail();

        // Get assessments
        $assessments = Assessment::where('course_offering_id', $offeringId)
            ->orderBy('created_at')
            ->get();

        // Get student grades
        $grades = StudentGrade::with('assessment')
            ->where('student_id', $studentId)
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');

        // Calculate progress
        $totalWeight = 0;
        $earnedWeight = 0;
        $assessmentProgress = [];

        foreach ($assessments as $assessment) {
            $grade = $grades->get($assessment->id);
            $weight = $assessment->weight ?? 1;
            $totalWeight += $weight;

            $assessmentProgress[] = [
                'assessment' => $assessment,
                'grade' => $grade,
                'weight' => $weight,
                'percentage' => $grade ? ($grade->grade / 100) * 100 : null,
                'weighted_grade' => $grade ? $grade->grade * $weight : null,
            ];

            if ($grade) {
                $earnedWeight += ($grade->grade / 100) * $weight;
            }
        }

        $overallProgress = $totalWeight > 0 ? ($earnedWeight / $totalWeight) * 100 : 0;

        // Get class averages for comparison
        $classAverages = [];
        foreach ($assessments as $assessment) {
            $classGrades = StudentGrade::where('assessment_id', $assessment->id)->get();
            $classAverages[$assessment->id] = $classGrades->avg('grade');
        }

        // Get attendance data
        $totalClasses = \App\Models\Attendance::where('course_offering_id', $offeringId)->distinct('date')->count();
        $studentAttendance = \App\Models\Attendance::where('course_offering_id', $offeringId)
            ->where('student_id', $studentId)
            ->where('status', 'present')
            ->count();
        $attendanceRate = $totalClasses > 0 ? ($studentAttendance / $totalClasses) * 100 : 0;

        // Get assignment trends (last 10 assessments)
        $recentAssessments = $assessments->take(-10);
        $gradeTrend = [];
        foreach ($recentAssessments as $assessment) {
            $grade = $grades->get($assessment->id);
            $gradeTrend[] = [
                'assessment' => $assessment->title,
                'date' => $assessment->created_at->format('M j'),
                'grade' => $grade ? $grade->grade : null,
            ];
        }

        // Calculate performance percentile
        $studentGrade = $overallProgress;
        $allStudentGrades = collect();
        $enrolledStudents = Enrollment::where('course_offering_id', $offeringId)
            ->where('status', 'approved')
            ->pluck('student_id');

        foreach ($enrolledStudents as $enrolledStudentId) {
            $studentGrades = StudentGrade::where('student_id', $enrolledStudentId)
                ->whereIn('assessment_id', $assessments->pluck('id'))
                ->get();

            if ($studentGrades->count() > 0) {
                $totalStudentWeight = 0;
                $earnedStudentWeight = 0;

                foreach ($studentGrades as $grade) {
                    $assessment = $assessments->find($grade->assessment_id);
                    $weight = $assessment ? ($assessment->weight ?? 1) : 1;
                    $totalStudentWeight += $weight;
                    $earnedStudentWeight += ($grade->grade / 100) * $weight;
                }

                $studentProgress = $totalStudentWeight > 0 ? ($earnedStudentWeight / $totalStudentWeight) * 100 : 0;
                $allStudentGrades->push($studentProgress);
            }
        }

        $percentile = 0;
        if ($allStudentGrades->count() > 0) {
            $sortedGrades = $allStudentGrades->sort();
            $rank = $sortedGrades->filter(fn($grade) => $grade <= $studentGrade)->count();
            $percentile = ($rank / $sortedGrades->count()) * 100;
        }

        return [
            'offering' => $offering,
            'student' => $student,
            'enrollment' => $enrollment,
            'assessments' => $assessments,
            'grades' => $grades,
            'assessment_progress' => $assessmentProgress,
            'overall_progress' => $overallProgress,
            'total_weight' => $totalWeight,
            'earned_weight' => $earnedWeight,
            'attendance_rate' => $attendanceRate,
            'total_classes' => $totalClasses,
            'grade_trend' => $gradeTrend,
            'percentile' => round($percentile, 1),
            'class_averages' => $classAverages,
        ];
    }

    /**
     * Export class performance report.
     */
    public function exportClassPerformance(int $offeringId)
    {
        $user = Auth::user();
        $report = $this->getClassPerformanceReport($offeringId, $user->id);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="class_performance_'.$report['offering']->course->code.'_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($report) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Class Performance Report']);
            fputcsv($file, ['Course', $report['offering']->course->name]);
            fputcsv($file, ['Semester', $report['offering']->semester->name ?? '']);
            fputcsv($file, []);

            // Summary
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Students', $report['total_students']]);
            fputcsv($file, ['Total Assessments', $report['total_assessments']]);
            fputcsv($file, ['Class Average', round($report['class_average'], 2)]);
            fputcsv($file, ['Standard Deviation', round($report['class_std_dev'], 2)]);
            fputcsv($file, ['Highest Score', round($report['highest_score'], 2)]);
            fputcsv($file, ['Lowest Score', round($report['lowest_score'], 2)]);
            fputcsv($file, []);

            // Grade Distribution
            fputcsv($file, ['Grade Distribution']);
            fputcsv($file, ['Grade', 'Count']);
            foreach ($report['grade_distribution'] as $grade => $count) {
                fputcsv($file, [$grade, $count]);
            }
            fputcsv($file, []);

            // Student Performance
            fputcsv($file, ['Student Performance']);
            fputcsv($file, ['Student ID', 'Student Name', 'Average', 'Completed Assessments']);

            foreach ($report['student_performance'] as $performance) {
                fputcsv($file, [
                    $performance['student']->id,
                    $performance['student']->name,
                    round($performance['weighted_average'], 2),
                    $performance['completed_assessments'].'/'.$performance['total_assessments'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export student progress report.
     */
    public function exportStudentProgress(int $offeringId, int $studentId)
    {
        $user = Auth::user();
        $report = $this->getStudentProgressReport($offeringId, $studentId, $user->id);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_progress_'.$report['student']->name.'_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($report) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Student Progress Report']);
            fputcsv($file, ['Student', $report['student']->name]);
            fputcsv($file, ['Course', $report['offering']->course->name]);
            fputcsv($file, ['Overall Progress', round($report['overall_progress'], 2).'%']);
            fputcsv($file, []);

            // Assessment Details
            fputcsv($file, ['Assessment', 'Grade', 'Weight', 'Class Average']);
            foreach ($report['assessment_progress'] as $progress) {
                fputcsv($file, [
                    $progress['assessment']->title,
                    $progress['grade']?->grade ?? 'N/A',
                    $progress['weight'],
                    round($report['class_averages'][$progress['assessment']->id] ?? 0, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate standard deviation.
     */
    private function calculateStdDev(array $values): float
    {
        $count = count($values);
        if ($count < 2) {
            return 0;
        }

        $mean = array_sum($values) / $count;
        $variance = 0;

        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }

        return sqrt($variance / $count);
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
}
