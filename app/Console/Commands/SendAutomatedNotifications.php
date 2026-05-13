<?php

namespace App\Console\Commands;

use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendAutomatedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-automated
                            {--type= : Specific notification type to send (assignments, completions, deadlines, cleanup, all)}
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automated notifications for assignments, course completions, and other events';

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type') ?? 'all';
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No notifications will actually be sent');
        }

        $this->info("Starting automated notifications for type: {$type}");

        $stats = [
            'assignments_due_soon' => 0,
            'assignments_overdue' => 0,
            'courses_completed' => 0,
            'enrollment_deadlines' => 0,
        ];

        switch ($type) {
            case 'assignments':
                $stats['assignments_due_soon'] = $this->sendAssignmentDueNotifications($dryRun);
                $stats['assignments_overdue'] = $this->sendAssignmentOverdueNotifications($dryRun);
                break;

            case 'completions':
                $stats['courses_completed'] = $this->sendCourseCompletionNotifications($dryRun);
                break;

            case 'deadlines':
                $stats['enrollment_deadlines'] = $this->sendEnrollmentDeadlineNotifications($dryRun);
                break;

            case 'cleanup':
                $stats['cleanup'] = $this->cleanupOldNotifications();
                break;

            case 'all':
            default:
                $stats['assignments_due_soon'] = $this->sendAssignmentDueNotifications($dryRun);
                $stats['assignments_overdue'] = $this->sendAssignmentOverdueNotifications($dryRun);
                $stats['courses_completed'] = $this->sendCourseCompletionNotifications($dryRun);
                $stats['enrollment_deadlines'] = $this->sendEnrollmentDeadlineNotifications($dryRun);
                $stats['cleanup'] = $this->cleanupOldNotifications();
                break;
        }

        $this->newLine();
        $this->info('Notification Summary:');
        foreach ($stats as $key => $count) {
            $this->line("  {$key}: {$count}");
        }

        $total = array_sum($stats);
        $this->newLine();
        $this->info("Total notifications processed: {$total}");

        return Command::SUCCESS;
    }

    /**
     * Send notifications for assignments due soon (within 3 days).
     */
    private function sendAssignmentDueNotifications(bool $dryRun): int
    {
        $this->info('Checking for assignments due soon...');

        $assessments = Assessment::where('due_date', '>', now())
            ->where('due_date', '<=', now()->addDays(3))
            ->where('is_published', true)
            ->with(['courseOffering.course', 'courseOffering.enrollments.student'])
            ->get();

        $count = 0;

        foreach ($assessments as $assessment) {
            $daysLeft = now()->diffInDays($assessment->due_date);

            foreach ($assessment->courseOffering->enrollments as $enrollment) {
                if ($enrollment->status === 'approved') {
                    $count++;

                    if (! $dryRun) {
                        $this->notificationService->sendToUser(
                            $enrollment->student,
                            'reminder',
                            "Assessment Due Soon: {$assessment->title}",
                            "Your assessment '{$assessment->title}' in {$assessment->courseOffering->course->name} is due in {$daysLeft} day(s).",
                            route('student.assessments.show', $assessment->id),
                            [
                                'assignment_name' => $assessment->title,
                                'course_name' => $assessment->courseOffering->course->name,
                                'days_left' => $daysLeft,
                                'due_date' => $assessment->due_date->format('M j, Y'),
                            ]
                        );
                    }
                }
            }
        }

        $this->line("Found {$count} assessments due soon notifications to send");

        return $count;
    }

    /**
     * Send notifications for overdue assignments.
     */
    private function sendAssignmentOverdueNotifications(bool $dryRun): int
    {
        $this->info('Checking for overdue assessments...');

        $assessments = Assessment::where('due_date', '<', now())
            ->where('is_published', true)
            ->with(['courseOffering.course', 'courseOffering.enrollments.student'])
            ->get();

        $count = 0;

        foreach ($assessments as $assessment) {
            foreach ($assessment->courseOffering->enrollments as $enrollment) {
                if ($enrollment->status === 'approved') {
                    // Check if student has submitted this assessment
                    $hasSubmitted = \DB::table('student_answers')
                        ->where('assessment_id', $assessment->id)
                        ->where('student_id', $enrollment->student_id)
                        ->exists();

                    if (! $hasSubmitted) {
                        $count++;

                        if (! $dryRun) {
                            $this->notificationService->sendToUser(
                                $enrollment->student,
                                'reminder',
                                "Assessment Overdue: {$assessment->title}",
                                "Your assessment '{$assessment->title}' in {$assessment->courseOffering->course->name} is overdue. Please submit it as soon as possible.",
                                route('student.assessments.show', $assessment->id),
                                [
                                    'assignment_name' => $assessment->title,
                                    'course_name' => $assessment->courseOffering->course->name,
                                    'days_overdue' => now()->diffInDays($assessment->due_date),
                                ]
                            );
                        }
                    }
                }
            }
        }

        $this->line("Found {$count} overdue assessment notifications to send");

        return $count;
    }

    /**
     * Send notifications for course completions.
     */
    private function sendCourseCompletionNotifications(bool $dryRun): int
    {
        $this->info('Checking for course completions...');

        // Find enrollments where the semester end date has passed and student hasn't been notified recently
        $completedEnrollments = Enrollment::whereHas('courseOffering.semester', function ($query) {
            $query->where('end_date', '<', now())
                ->where('is_active', true);
        })
            ->where('status', 'approved')
            ->where('completed_at', '>', now()->subDays(30)) // Completed within last 30 days
            ->with(['courseOffering.course', 'courseOffering.semester', 'student'])
            ->get();

        // Filter out students who have already been notified about this course completion
        $completedEnrollments = $completedEnrollments->filter(function ($enrollment) {
            $recentNotification = \DB::table('notifications')
                ->where('user_id', $enrollment->student_id)
                ->where('type', 'course_completion')
                ->where('created_at', '>', now()->subDays(30))
                ->where('content', 'like', '%'.$enrollment->courseOffering->course->name.'%')
                ->exists();

            return ! $recentNotification;
        });

        $count = 0;

        foreach ($completedEnrollments as $enrollment) {
            $count++;

            if (! $dryRun) {
                $this->notificationService->sendToUser(
                    $enrollment->student,
                    'success',
                    "Course Completed: {$enrollment->courseOffering->course->name}",
                    "Congratulations! You have successfully completed the course '{$enrollment->courseOffering->course->name}'. Your final grade and certificate will be available soon.",
                    route('student.courses.show', $enrollment->courseOffering->course_id),
                    [
                        'course_name' => $enrollment->courseOffering->course->name,
                        'completion_date' => now()->format('M j, Y'),
                    ]
                );
            }
        }

        $this->line("Found {$count} course completion notifications to send");

        return $count;
    }

    /**
     * Send notifications for enrollment deadlines.
     */
    private function sendEnrollmentDeadlineNotifications(bool $dryRun): int
    {
        $this->info('Checking for enrollment deadlines...');

        $semesters = \App\Models\Semester::where('enrollment_end_date', '>', now())
            ->where('enrollment_end_date', '<=', now()->addDays(7))
            ->where('is_active', true)
            ->get();

        $count = 0;

        foreach ($semesters as $semester) {
            $daysLeft = now()->diffInDays($semester->enrollment_deadline);

            // Get students who haven't enrolled in this semester yet
            $enrolledStudentIds = Enrollment::where('semester_id', $semester->id)
                ->pluck('student_id')
                ->toArray();

            $studentsToNotify = User::role('student')
                ->whereNotIn('id', $enrolledStudentIds)
                ->get();

            foreach ($studentsToNotify as $student) {
                $count++;

                if (! $dryRun) {
                    $this->notificationService->sendToUser(
                        $student,
                        'reminder',
                        'Enrollment Deadline Approaching',
                        "The enrollment deadline for {$semester->name} is in {$daysLeft} day(s). Please complete your course enrollment before the deadline.",
                        route('student.enrollment.index'),
                        [
                            'semester_name' => $semester->name,
                            'days_left' => $daysLeft,
                            'deadline' => $semester->enrollment_end_date->format('M j, Y'),
                        ]
                    );
                }
            }
        }

        $this->line("Found {$count} enrollment deadline notifications to send");

        return $count;
    }

    /**
     * Clean up old notifications.
     */
    private function cleanupOldNotifications(): int
    {
        $this->info('Cleaning up old notifications...');

        // Delete read notifications older than 90 days
        $deleted = \App\Models\Notification::where('is_read', true)
            ->where('read_at', '<', now()->subDays(90))
            ->delete();

        // Delete unread notifications older than 180 days (to avoid losing important notifications)
        $deleted += \App\Models\Notification::where('is_read', false)
            ->where('created_at', '<', now()->subDays(180))
            ->delete();

        $this->line("Cleaned up {$deleted} old notifications");

        return $deleted;
    }
}
