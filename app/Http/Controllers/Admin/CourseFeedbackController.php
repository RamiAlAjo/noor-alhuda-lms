<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseFeedback;
use App\Models\CourseOffering;
use App\Models\Semester;
use Illuminate\Http\Request;

class CourseFeedbackController extends Controller
{
    /**
     * Display a listing of all feedback.
     */
    public function index(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $courseId = $request->get('course_id');

        $semesters = Semester::orderBy('start_date', 'desc')->get();

        $query = CourseFeedback::with(['courseOffering.course', 'courseOffering.semester'])
            ->submitted();

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($courseId) {
            $query->whereHas('courseOffering', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $feedbacks = $query->orderBy('submitted_at', 'desc')
            ->paginate(20);

        // Get overall statistics
        $stats = $this->getOverallStatistics($semesterId);

        return view('pages.admin.feedback.index', compact('feedbacks', 'semesters', 'stats', 'semesterId', 'courseId'));
    }

    /**
     * Display aggregated feedback for a specific course.
     */
    public function showCourse(CourseOffering $courseOffering)
    {
        $feedbacks = CourseFeedback::where('course_offering_id', $courseOffering->id)
            ->submitted()
            ->get();

        $averages = CourseFeedback::getAverageRatingsForCourse($courseOffering->id);
        $ratingCategories = CourseFeedback::getRatingCategories();

        // Get qualitative feedback (comments)
        $qualitativeFeedback = $feedbacks->map(function ($f) {
            return [
                'strengths' => $f->strengths,
                'improvements' => $f->improvements,
                'additional_comments' => $f->additional_comments,
                'is_anonymous' => $f->is_anonymous,
                'student_name' => $f->is_anonymous ? 'Anonymous' : ($f->student?->name ?? 'Unknown'),
            ];
        })->filter(function ($f) {
            return ! empty($f['strengths']) || ! empty($f['improvements']) || ! empty($f['additional_comments']);
        });

        // Rating distribution
        $ratingDistribution = $this->getRatingDistribution($feedbacks);

        return view('pages.admin.feedback.course', compact(
            'courseOffering',
            'feedbacks',
            'averages',
            'ratingCategories',
            'qualitativeFeedback',
            'ratingDistribution'
        ));
    }

    /**
     * Export feedback to CSV.
     */
    public function export(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $courseId = $request->get('course_id');

        $query = CourseFeedback::with(['courseOffering.course', 'courseOffering.semester', 'student'])
            ->submitted();

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($courseId) {
            $query->whereHas('courseOffering', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        $feedbacks = $query->orderBy('submitted_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="course_feedback_'.date('Y-m-d').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($feedbacks) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Course Code',
                'Course Name',
                'Semester',
                'Student (or Anonymous)',
                'Overall Rating',
                'Content Quality',
                'Instructor Knowledge',
                'Instructor Communication',
                'Course Organization',
                'Materials Quality',
                'Workload Appropriateness',
                'Average Rating',
                'Strengths',
                'Improvements',
                'Additional Comments',
                'Submitted At',
            ]);

            foreach ($feedbacks as $feedback) {
                fputcsv($file, [
                    $feedback->courseOffering->course->code ?? '',
                    $feedback->courseOffering->course->name ?? '',
                    $feedback->courseOffering->semester->name ?? '',
                    $feedback->is_anonymous ? 'Anonymous' : ($feedback->student?->name ?? 'Unknown'),
                    $feedback->overall_rating,
                    $feedback->content_quality,
                    $feedback->instructor_knowledge,
                    $feedback->instructor_communication,
                    $feedback->course_organization,
                    $feedback->materials_quality,
                    $feedback->workload_appropriateness,
                    $feedback->average_rating,
                    $feedback->strengths,
                    $feedback->improvements,
                    $feedback->additional_comments,
                    $feedback->submitted_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display feedback reports.
     */
    public function reports(Request $request)
    {
        $semesterId = $request->get('semester_id');
        $reportType = $request->get('report_type', 'summary');

        $semesters = Semester::orderBy('start_date', 'desc')->get();

        $report = [];

        if ($semesterId) {
            switch ($reportType) {
                case 'summary':
                    $report = $this->getSummaryReport($semesterId);
                    break;
                case 'comparison':
                    $report = $this->getComparisonReport($semesterId);
                    break;
                case 'trends':
                    $report = $this->getTrendsReport($semesterId);
                    break;
            }
        }

        return view('pages.admin.feedback.reports', compact('semesters', 'report', 'semesterId', 'reportType'));
    }

    /**
     * Get overall statistics.
     */
    private function getOverallStatistics(?int $semesterId = null): array
    {
        $query = CourseFeedback::submitted();

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        $feedbacks = $query->get();

        if ($feedbacks->isEmpty()) {
            return [
                'total_feedback' => 0,
                'average_overall' => 0,
                'average_all_categories' => 0,
            ];
        }

        $categories = CourseFeedback::getRatingCategories();
        $averages = [];

        foreach (array_keys($categories) as $category) {
            $values = $feedbacks->pluck($category)->filter();
            $averages[$category] = $values->count() > 0 ? round($values->avg(), 2) : 0;
        }

        return [
            'total_feedback' => $feedbacks->count(),
            'average_overall' => $averages['overall_rating'] ?? 0,
            'average_all_categories' => round(array_sum($averages) / count($averages), 2),
            'category_averages' => $averages,
        ];
    }

    /**
     * Get rating distribution for a course.
     */
    private function getRatingDistribution($feedbacks): array
    {
        $distribution = [];

        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $feedbacks->where('overall_rating', $i)->count();
        }

        return $distribution;
    }

    /**
     * Get summary report for a semester.
     */
    private function getSummaryReport(int $semesterId): array
    {
        $feedbacks = CourseFeedback::where('semester_id', $semesterId)
            ->submitted()
            ->with('courseOffering.course')
            ->get();

        $courseStats = $feedbacks->groupBy('course_offering_id')->map(function ($group) {
            return [
                'course' => $group->first()->courseOffering->course,
                'feedback_count' => $group->count(),
                'average_rating' => round($group->avg('overall_rating'), 2),
            ];
        })->sortByDesc('average_rating');

        return [
            'type' => 'summary',
            'semester_id' => $semesterId,
            'total_feedback' => $feedbacks->count(),
            'total_courses' => $courseStats->count(),
            'course_stats' => $courseStats,
            'overall_average' => round($feedbacks->avg('overall_rating'), 2),
        ];
    }

    /**
     * Get comparison report between courses.
     */
    private function getComparisonReport(int $semesterId): array
    {
        $feedbacks = CourseFeedback::where('semester_id', $semesterId)
            ->submitted()
            ->with('courseOffering.course')
            ->get();

        $categories = CourseFeedback::getRatingCategories();

        $courseComparison = $feedbacks->groupBy('course_offering_id')->map(function ($group) use ($categories) {
            $averages = [];
            foreach (array_keys($categories) as $category) {
                $values = $group->pluck($category)->filter();
                $averages[$category] = $values->count() > 0 ? round($values->avg(), 2) : null;
            }

            return [
                'course' => $group->first()->courseOffering->course,
                'feedback_count' => $group->count(),
                'averages' => $averages,
            ];
        });

        return [
            'type' => 'comparison',
            'semester_id' => $semesterId,
            'categories' => $categories,
            'course_comparison' => $courseComparison,
        ];
    }

    /**
     * Get trends report across semesters.
     */
    private function getTrendsReport(int $semesterId): array
    {
        $currentSemester = Semester::find($semesterId);

        // Get previous semesters
        $previousSemesters = Semester::where('start_date', '<', $currentSemester->start_date)
            ->orderBy('start_date', 'desc')
            ->take(3)
            ->get();

        $allSemesters = $previousSemesters->prepend($currentSemester);

        $trends = $allSemesters->map(function ($semester) {
            $feedbacks = CourseFeedback::where('semester_id', $semester->id)
                ->submitted()
                ->get();

            return [
                'semester' => $semester,
                'total_feedback' => $feedbacks->count(),
                'average_rating' => $feedbacks->count() > 0
                    ? round($feedbacks->avg('overall_rating'), 2)
                    : null,
            ];
        });

        return [
            'type' => 'trends',
            'semester_id' => $semesterId,
            'trends' => $trends,
        ];
    }
}
