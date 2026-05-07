<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assessment;
use App\Models\CourseOffering;
use App\Models\StudentGrade;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard.
     */
    public function index(): View
    {
        $teacher = auth()->user();

        // Get courses with eager loading to prevent N+1 queries
        $courses = CourseOffering::where('teacher_id', $teacher->id)
            ->with([
                'course',
                'course.department',
                'semester',
                'semester.academicYear',
                'enrollments' => function ($query) {
                    $query->where('status', 'approved');
                },
                'materials',
                'assessments',
            ])
            ->get();

        $total_students = $courses->sum('enrolled_count');

        // Get upcoming assessments with caching (cache for 5 minutes)
        $upcomingAssessments = Cache::remember("teacher_upcoming_assessments_{$teacher->id}", now()->addMinutes(5), function () use ($teacher) {
            return Assessment::whereHas('offering', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
                ->where('due_date', '>=', now()->toDateString())
                ->where('is_published', true)
                ->with(['offering.course', 'assessmentType'])
                ->orderBy('due_date')
                ->take(5)
                ->get();
        });

        // Get pending grades count with caching (cache for 5 minutes)
        $pendingGradesCount = Cache::remember("teacher_pending_grades_{$teacher->id}", now()->addMinutes(5), function () use ($teacher) {
            return StudentGrade::whereHas('assessment.offering', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
                ->whereNull('grade')
                ->count();
        });

        // Get recent announcements with caching (cache for 5 minutes)
        $recentAnnouncements = Cache::remember("teacher_announcements_{$teacher->id}", now()->addMinutes(5), function () use ($teacher) {
            return Announcement::where('is_published', true)
                ->where(function ($query) use ($teacher) {
                    $query->where('target_offering_id', null)
                        ->orWhereHas('targetOffering', function ($q) use ($teacher) {
                            $q->where('teacher_id', $teacher->id);
                        });
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        });

        return view('pages.teacher.dashboard', compact(
            'courses',
            'total_students',
            'upcomingAssessments',
            'pendingGradesCount',
            'recentAnnouncements'
        ));
    }
}
