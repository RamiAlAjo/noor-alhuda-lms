<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assessment;
use App\Models\Conversation;
use App\Models\CourseOffering;
use App\Models\Message;
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

        // Get courses with counts only (much lighter than full relations)
        $courses = CourseOffering::where('teacher_id', $teacher->id)
            ->with([
                'course',
                'course.department',
                'semester',
                'semester.academicYear',
            ])
            ->withCount([
                'enrollments as enrolled_count' => function ($query) {
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

    /**
     * Display the teacher calendar.
     */
    public function calendar(): View
    {
        $teacher = auth()->user();

        // Get courses with their assessments and schedules
        $courses = CourseOffering::where('teacher_id', $teacher->id)
            ->with([
                'course',
                'semester',
                'semester.academicYear',
                'assessments' => function ($query) {
                    $query->where('is_published', true)
                        ->whereNotNull('due_date')
                        ->orderBy('due_date');
                },
            ])
            ->get();

        // Get all assessment deadlines
        $assessmentEvents = [];
        foreach ($courses as $course) {
            foreach ($course->assessments as $assessment) {
                $assessmentEvents[] = [
                    'id' => 'assessment_'.$assessment->id,
                    'title' => $assessment->title,
                    'description' => $assessment->description,
                    'start' => $assessment->due_date,
                    'end' => $assessment->due_date,
                    'type' => 'assessment',
                    'course' => $course->course->name,
                    'section' => $course->section_name,
                    'url' => route('teacher.courses.assessments.grade', [
                        'section' => $course->id,
                        'assessment' => $assessment->id,
                    ]),
                ];
            }
        }

        // Get course start/end dates as events
        $courseEvents = [];
        foreach ($courses as $course) {
            if ($course->semester) {
                $courseEvents[] = [
                    'id' => 'course_start_'.$course->id,
                    'title' => 'Course Start: '.$course->course->name,
                    'description' => 'Start of '.$course->section_name,
                    'start' => $course->semester->start_date,
                    'end' => $course->semester->start_date,
                    'type' => 'course',
                    'course' => $course->course->name,
                    'section' => $course->section_name,
                ];

                $courseEvents[] = [
                    'id' => 'course_end_'.$course->id,
                    'title' => 'Course End: '.$course->course->name,
                    'description' => 'End of '.$course->section_name,
                    'start' => $course->semester->end_date,
                    'end' => $course->semester->end_date,
                    'type' => 'course',
                    'course' => $course->course->name,
                    'section' => $course->section_name,
                ];
            }
        }

        // Combine all events
        $allEvents = array_merge($assessmentEvents, $courseEvents);

        return view('pages.teacher.calendar', compact('courses', 'allEvents'));
    }

    /**
     * Display teacher messaging interface.
     */
    public function messages(): View
    {
        $teacher = auth()->user();

        // Handle bulk student selection from query parameters
        $selectedStudents = [];
        if (request()->has('students')) {
            $studentIds = explode(',', request('students'));
            $selectedStudents = \App\Models\User::whereIn('id', $studentIds)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'student');
                })
                ->whereHas('enrollments', function ($q) use ($teacher) {
                    $q->whereHas('offering', function ($q2) use ($teacher) {
                        $q2->where('teacher_id', $teacher->id);
                    });
                })
                ->get();
        }

        // Get conversations involving the teacher and students from their courses
        $conversations = Conversation::whereHas('participants', function ($query) use ($teacher) {
            $query->where('user_id', $teacher->id);
        })
            ->whereHas('participants', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'student');
                });
            })
            ->with([
                'participants' => function ($query) use ($teacher) {
                    $query->where('user_id', '!=', $teacher->id);
                },
                'lastMessage',
                'unreadMessages' => function ($query) use ($teacher) {
                    $query->where('sender_id', '!=', $teacher->id)
                        ->whereDoesntHave('readBy', function ($q) use ($teacher) {
                            $q->where('user_id', $teacher->id);
                        });
                },
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->paginate(20);

        // Get courses for filtering conversations by course
        $courses = CourseOffering::where('teacher_id', $teacher->id)
            ->with('course')
            ->get();

        return view('pages.teacher.messages', compact('conversations', 'courses', 'selectedStudents'));
    }
}
