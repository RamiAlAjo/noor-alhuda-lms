<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CourseOffering;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Dashboard with overview statistics.
     */
    public function dashboard(): View
    {
        $totalStudents = User::role('student')->count();
        $totalTeachers = User::role('teacher')->count();
        $totalAdmins = User::role('admin')->count();

        $totalEnrollments = Enrollment::count();
        $activeEnrollments = Enrollment::where('status', 'approved')->count();
        $pendingEnrollments = Enrollment::where('status', 'pending')->count();

        $totalSections = CourseSection::count();
        $openSections = CourseSection::where('is_active', true)->count();

        // Monthly enrollment stats
        $enrollmentStats = Enrollment::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->get();

        // Course distribution stats
        $courseDistribution = CourseOffering::with('enrollments')
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(10)
            ->get();

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['student', 'section.course'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pages.admin.reports.dashboard', compact(
            'totalStudents', 'totalTeachers', 'totalAdmins',
            'totalEnrollments', 'activeEnrollments', 'pendingEnrollments',
            'totalSections', 'openSections',
            'enrollmentStats', 'courseDistribution', 'recentEnrollments'
        ));
    }

    /**
     * Enrollment statistics.
     */
    public function enrollmentStats(): View
    {
        $enrollmentsByStatus = Enrollment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $enrollmentsByCourse = Enrollment::selectRaw('course_offering_id, COUNT(*) as count')
            ->groupBy('course_offering_id')
            ->with('section.course')
            ->get();

        $enrollmentsByMajor = Enrollment::selectRaw('semester_id, COUNT(*) as count')
            ->whereNotNull('semester_id')
            ->groupBy('semester_id')
            ->get();

        return view('pages.admin.reports.enrollment', compact(
            'enrollmentsByStatus', 'enrollmentsByCourse', 'enrollmentsByMajor'
        ));
    }

    /**
     * Attendance reports.
     */
    public function attendance(Request $request): View
    {
        $query = Attendance::with(['enrollment.student', 'enrollment.section.course']);

        if ($request->has('section_id') && $request->section_id) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->where('course_offering_id', $request->section_id);
            });
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(50);
        $sections = CourseSection::with('course')->get();

        // Summary statistics
        $total = Attendance::count();
        $present = Attendance::where('status', 'present')->count();
        $absent = Attendance::where('status', 'absent')->count();
        $excused = Attendance::where('status', 'excused')->count();
        $late = Attendance::where('status', 'late')->count();

        return view('pages.admin.reports.attendance', compact(
            'attendances', 'sections', 'total', 'present', 'absent', 'excused', 'late'
        ));
    }

    /**
     * GPA Reports.
     */
    public function gpa(Request $request): View
    {
        $query = User::role('student')->with(['enrollments' => function ($q) {
            $q->where('status', 'enrolled');
        }]);

        if ($request->has('major_id') && $request->major_id) {
            $query->where('major_id', $request->major_id);
        }

        $students = $query->get();

        // Calculate GPA for each student
        $gpaData = $students->map(function ($student) {
            $grades = Grade::where('student_id', $student->id)->get();
            $totalPoints = 0;
            $totalCredits = 0;

            foreach ($grades as $grade) {
                $totalPoints += $grade->grade_points * $grade->course_credits;
                $totalCredits += $grade->course_credits;
            }

            $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;

            return [
                'student' => $student,
                'gpa' => $gpa,
                'total_credits' => $totalCredits,
            ];
        });

        // Sort by GPA
        $gpaData = $gpaData->sortByDesc('gpa')->values();

        $averageGpa = $gpaData->count() > 0 ? round($gpaData->avg('gpa'), 2) : 0;

        return view('pages.admin.reports.gpa', compact('gpaData', 'averageGpa'));
    }

    /**
     * Export GPA report.
     */
    public function exportGpa(Request $request)
    {
        // This would generate an Excel/PDF file
        return back()->with('success', __('lms::messages.report_exported'));
    }

    /**
     * Custom report builder.
     */
    public function custom(): View
    {
        $students = User::role('student')->count();
        $teachers = User::role('teacher')->count();
        $courses = \App\Models\Course::count();
        $sections = CourseSection::count();

        return view('pages.admin.reports.custom', compact(
            'students', 'teachers', 'courses', 'sections'
        ));
    }
}
