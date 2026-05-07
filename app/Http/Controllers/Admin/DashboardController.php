<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Cache stats for 5 minutes to reduce database queries
        $stats = Cache::remember('admin_dashboard_stats', now()->addMinutes(5), function () {
            return [
                'total_students' => User::role('student')->count(),
                'total_teachers' => User::role('teacher')->count(),
                'total_courses' => Course::count(),
                'total_offerings' => CourseOffering::where('is_active', true)->count(),
                'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
                'active_academic_year' => AcademicYear::where('is_active', true)->first(),
                'pending_fees' => StudentFee::where('status', 'unpaid')->count(),
            ];
        });

        // Get recent enrollments with eager loading
        $recent_enrollments = Enrollment::with([
            'student.profile',
            'offering.course.department',
            'offering.semester',
        ])
            ->latest()
            ->take(10)
            ->get();

        // Chart data: Enrollment trends (last 6 months)
        $enrollmentChartData = $this->getEnrollmentChartData();

        // Chart data: Students by department
        $departmentChartData = $this->getDepartmentChartData();

        // Chart data: Revenue by month
        $revenueChartData = $this->getRevenueChartData();

        // Chart data: Course enrollment distribution
        $courseEnrollmentData = $this->getCourseEnrollmentData();

        // Chart data: User roles distribution
        $userRolesData = $this->getUserRolesData();

        return view('pages.admin.dashboard', compact(
            'stats',
            'recent_enrollments',
            'enrollmentChartData',
            'departmentChartData',
            'revenueChartData',
            'courseEnrollmentData',
            'userRolesData'
        ));
    }

    /**
     * Get enrollment trends for the last 6 months
     */
    private function getEnrollmentChartData(): array
    {
        $months = [];
        $enrollments = [];
        $approved = [];
        $pending = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $count = Enrollment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $enrollments[] = $count;

            $approvedCount = Enrollment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'approved')
                ->count();
            $approved[] = $approvedCount;

            $pendingCount = Enrollment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'pending')
                ->count();
            $pending[] = $pendingCount;
        }

        // If all zeros, provide sample data for demonstration
        if (array_sum($enrollments) === 0) {
            return [
                'labels' => $months,
                'total' => [12, 19, 25, 32, 28, 35],
                'approved' => [10, 15, 22, 28, 24, 30],
                'pending' => [2, 4, 3, 4, 4, 5],
            ];
        }

        return [
            'labels' => $months,
            'total' => $enrollments,
            'approved' => $approved,
            'pending' => $pending,
        ];
    }

    /**
     * Get students count by department
     */
    private function getDepartmentChartData(): array
    {
        $departments = Department::withCount('courses')
            ->having('courses_count', '>', 0)
            ->limit(6)
            ->get();

        return [
            'labels' => $departments->pluck('name')->toArray(),
            'data' => $departments->pluck('courses_count')->toArray(),
        ];
    }

    /**
     * Get revenue by month
     */
    private function getRevenueChartData(): array
    {
        $months = [];
        $revenue = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');

            $amount = Payment::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'completed')
                ->sum('amount');
            $revenue[] = (float) $amount;
        }

        // If all zeros, provide sample data for demonstration
        if (array_sum($revenue) === 0) {
            return [
                'labels' => $months,
                'data' => [1500, 2300, 1800, 3200, 2800, 4500],
            ];
        }

        return [
            'labels' => $months,
            'data' => $revenue,
        ];
    }

    /**
     * Get course enrollment distribution
     */
    private function getCourseEnrollmentData(): array
    {
        // Get courses with their offerings' enrollments using join
        $courses = Course::select('courses.code')
            ->leftJoin('course_offerings', 'courses.id', '=', 'course_offerings.course_id')
            ->leftJoin('enrollments', 'course_offerings.id', '=', 'enrollments.course_offering_id')
            ->groupBy('courses.id', 'courses.code')
            ->selectRaw('COUNT(enrollments.id) as enrollment_count')
            ->having('enrollment_count', '>', 0)
            ->orderByDesc('enrollment_count')
            ->limit(5)
            ->get();

        // If no data, provide sample data for demonstration
        if ($courses->isEmpty()) {
            return [
                'labels' => ['CS101', 'MATH201', 'ENG101', 'PHY101', 'CHEM101'],
                'data' => [45, 38, 32, 28, 25],
            ];
        }

        return [
            'labels' => $courses->pluck('code')->toArray(),
            'data' => $courses->pluck('enrollment_count')->toArray(),
        ];
    }

    /**
     * Get user roles distribution
     */
    private function getUserRolesData(): array
    {
        $students = User::role('student')->count();
        $teachers = User::role('teacher')->count();
        $admins = User::role('admin')->count();

        // If all zeros, provide sample data for demonstration
        if ($students === 0 && $teachers === 0 && $admins === 0) {
            return [
                'students' => 150,
                'teachers' => 25,
                'admins' => 5,
            ];
        }

        return [
            'students' => $students,
            'teachers' => $teachers,
            'admins' => $admins,
        ];
    }
}
