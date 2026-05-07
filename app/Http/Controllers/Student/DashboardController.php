<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\CalendarEvent;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\Notification;
use App\Models\StudentFee;
use App\Models\StudentGrade;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(): View
    {
        $student = auth()->user();

        // Get enrolled courses with eager loading to prevent N+1 queries
        $enrolled_courses = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with([
                'offering.course.department',
                'offering.course.major',
                'offering.semester.academicYear',
                'offering.teacher.profile',
            ])
            ->get();

        // Get upcoming assessments with eager loading
        $upcoming_assessments = StudentGrade::where('student_id', $student->id)
            ->whereHas('assessment', function ($query) {
                $query->where('due_date', '>=', now()->toDateString())
                    ->where('is_published', true);
            })
            ->with([
                'assessment.offering.course',
                'assessment.assessmentType',
            ])
            ->get()
            ->sortBy('assessment.due_date')
            ->take(5);

        // Calculate GPA with caching (cache for 5 minutes)
        $gpa = Cache::remember("student_gpa_{$student->id}", now()->addMinutes(5), function () use ($student) {
            return $this->calculateGpa($student->id);
        });

        // Get level from enrollments
        $level = 1;
        if ($enrolled_courses->count() > 0) {
            $firstOffering = $enrolled_courses->first()->offering;
            $level = $firstOffering && $firstOffering->course ? $firstOffering->course->year_level ?? 1 : 1;
        }

        // Calculate progress percentage
        $progress = $this->calculateProgress($student->id);

        // Financial balance with caching (cache for 5 minutes)
        $financialBalance = Cache::remember("student_financial_{$student->id}", now()->addMinutes(5), function () use ($student) {
            return $this->calculateFinancialBalance($student->id);
        });

        // Get unread notifications count
        $unreadNotificationsCount = Notification::where('user_id', $student->id)
            ->unread()
            ->count();

        // Get unread messages count
        $unreadMessagesCount = Message::where('receiver_id', $student->id)
            ->unread()
            ->count();

        // Get recent notifications with eager loading
        $recentNotifications = Notification::where('user_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get upcoming calendar events
        $upcomingEvents = CalendarEvent::where('user_id', $student->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        // Get news/announcements with caching (cache for 5 minutes)
        $news = Cache::remember('student_announcements', now()->addMinutes(5), function () {
            return Announcement::where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        });

        // Weekly schedule from course offerings
        $weeklySchedule = $this->getWeeklySchedule($enrolled_courses);

        // Timeline activities - combine assessments and events
        $timeline = collect();

        foreach ($upcoming_assessments as $assessment) {
            // Skip if assessment or offering is null
            if (! $assessment->assessment || ! $assessment->assessment->offering) {
                continue;
            }
            $timeline->push([
                'type' => 'assessment',
                'title' => $assessment->assessment->title,
                'course' => $assessment->assessment->offering->course->name ?? '',
                'date' => $assessment->assessment->due_date,
                'icon' => 'clipboard',
                'color' => 'red',
            ]);
        }

        foreach ($upcomingEvents as $event) {
            $timeline->push([
                'type' => 'event',
                'title' => $event->title,
                'course' => '',
                'date' => $event->start_time,
                'icon' => 'calendar',
                'color' => 'blue',
            ]);
        }

        $timeline = $timeline->sortBy('date')->take(10);

        return view('pages.student.dashboard', compact(
            'enrolled_courses',
            'upcoming_assessments',
            'gpa',
            'level',
            'progress',
            'financialBalance',
            'unreadNotificationsCount',
            'unreadMessagesCount',
            'recentNotifications',
            'timeline',
            'news',
            'weeklySchedule'
        ));
    }

    /**
     * Calculate student GPA.
     */
    private function calculateGpa(int $studentId): float
    {
        $grades = StudentGrade::where('student_id', $studentId)
            ->whereNotNull('grade')
            ->with('assessment.offering.course')
            ->get();

        if ($grades->isEmpty()) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $percentage = $grade->percentage;
            $credits = 3;
            if ($grade->assessment && $grade->assessment->offering) {
                $credits = $grade->assessment->offering->course->credits ?? 3;
            }

            $points = $this->getGradePoints($percentage);
            $totalPoints += $points * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    /**
     * Calculate student progress.
     */
    private function calculateProgress(int $studentId): int
    {
        $enrollment = Enrollment::where('student_id', $studentId)
            ->where('status', 'approved')
            ->first();

        if (! $enrollment || $enrollment->total_activities == 0) {
            return 0;
        }

        return round(($enrollment->completed_activities / $enrollment->total_activities) * 100);
    }

    /**
     * Calculate financial balance.
     */
    private function calculateFinancialBalance(int $studentId): array
    {
        $fees = StudentFee::where('student_id', $studentId)->get();

        $totalDue = $fees->sum('amount');
        $totalPaid = $fees->sum('paid_amount');
        $balance = $totalDue - $totalPaid;

        return [
            'total_due' => $totalDue,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'is_pending' => $fees->where('status', 'pending')->count() > 0,
        ];
    }

    /**
     * Get weekly schedule from enrolled courses.
     */
    private function getWeeklySchedule($enrolledCourses): array
    {
        $schedule = [];
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($enrolledCourses as $enrollment) {
            $offering = $enrollment->offering;

            // Skip if offering is null
            if (! $offering) {
                continue;
            }

            // Try to get schedule from schedule_json
            if ($offering->schedule_json) {
                foreach ($offering->schedule_json as $slot) {
                    $dayIndex = array_search($slot['day'] ?? '', $days);
                    if ($dayIndex !== false) {
                        $schedule[$dayIndex][] = [
                            'time' => $slot['time'] ?? $offering->schedule,
                            'room' => $slot['room'] ?? $offering->room,
                            'course' => $offering->course->name ?? '',
                            'is_online' => isset($offering->meeting_link) && $offering->meeting_link !== '',
                        ];
                    }
                }
            } elseif ($offering->schedule) {
                // Fallback to old schedule field
                $schedule[0][] = [
                    'time' => $offering->schedule,
                    'room' => $offering->room,
                    'course' => $offering->course->name ?? '',
                    'is_online' => isset($offering->meeting_link) && $offering->meeting_link !== '',
                ];
            }
        }

        return $schedule;
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
