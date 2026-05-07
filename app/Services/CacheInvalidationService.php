<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheInvalidationService
{
    /**
     * Cache tags for different entity types.
     */
    const TAG_USERS = 'users';

    const TAG_COURSES = 'courses';

    const TAG_ENROLLMENTS = 'enrollments';

    const TAG_GRADES = 'grades';

    const TAG_PAYMENTS = 'payments';

    const TAG_ANNOUNCEMENTS = 'announcements';

    const TAG_SETTINGS = 'settings';

    const TAG_DASHBOARD = 'dashboard';

    /**
     * Invalidate user-related caches.
     */
    public static function invalidateUser(int $userId): void
    {
        Cache::forget("user_{$userId}");
        Cache::forget("user_profile_{$userId}");
        Cache::forget("user_permissions_{$userId}");
        Cache::forget("user_roles_{$userId}");

        // Invalidate dashboard caches for this user
        Cache::forget("student_gpa_{$userId}");
        Cache::forget("student_financial_{$userId}");
        Cache::forget("teacher_upcoming_assessments_{$userId}");
        Cache::forget("teacher_pending_grades_{$userId}");
        Cache::forget("teacher_announcements_{$userId}");
    }

    /**
     * Invalidate course-related caches.
     */
    public static function invalidateCourse(int $courseId): void
    {
        Cache::forget("course_{$courseId}");
        Cache::forget("course_offerings_{$courseId}");
        Cache::forget("course_prerequisites_{$courseId}");

        // Invalidate dashboard stats
        Cache::forget('admin_dashboard_stats');
    }

    /**
     * Invalidate enrollment-related caches.
     */
    public static function invalidateEnrollment(int $enrollmentId): void
    {
        Cache::forget("enrollment_{$enrollmentId}");

        // Invalidate dashboard stats
        Cache::forget('admin_dashboard_stats');
    }

    /**
     * Invalidate grade-related caches.
     */
    public static function invalidateGrade(int $gradeId): void
    {
        Cache::forget("grade_{$gradeId}");

        // Invalidate student GPA cache
        $grade = \App\Models\StudentGrade::find($gradeId);
        if ($grade) {
            Cache::forget("student_gpa_{$grade->student_id}");
        }

        // Invalidate teacher pending grades cache
        if ($grade && $grade->assessment && $grade->assessment->offering) {
            Cache::forget("teacher_pending_grades_{$grade->assessment->offering->teacher_id}");
        }
    }

    /**
     * Invalidate payment-related caches.
     */
    public static function invalidatePayment(int $paymentId): void
    {
        Cache::forget("payment_{$paymentId}");

        // Invalidate student financial cache
        $payment = \App\Models\Payment::find($paymentId);
        if ($payment) {
            Cache::forget("student_financial_{$payment->student_id}");
        }

        // Invalidate dashboard stats
        Cache::forget('admin_dashboard_stats');
    }

    /**
     * Invalidate announcement-related caches.
     */
    public static function invalidateAnnouncement(int $announcementId): void
    {
        Cache::forget("announcement_{$announcementId}");
        Cache::forget('student_announcements');

        // Invalidate teacher announcements cache
        $teachers = \App\Models\User::role('teacher')->get();
        foreach ($teachers as $teacher) {
            Cache::forget("teacher_announcements_{$teacher->id}");
        }
    }

    /**
     * Invalidate settings-related caches.
     */
    public static function invalidateSettings(): void
    {
        Cache::forget('app_settings');
        Cache::forget('academic_years');
        Cache::forget('semesters');
        Cache::forget('departments');
        Cache::forget('majors');
    }

    /**
     * Invalidate all dashboard caches.
     */
    public static function invalidateAllDashboards(): void
    {
        Cache::forget('admin_dashboard_stats');

        // Invalidate student dashboards
        $students = \App\Models\User::role('student')->get();
        foreach ($students as $student) {
            Cache::forget("student_gpa_{$student->id}");
            Cache::forget("student_financial_{$student->id}");
        }

        // Invalidate teacher dashboards
        $teachers = \App\Models\User::role('teacher')->get();
        foreach ($teachers as $teacher) {
            Cache::forget("teacher_upcoming_assessments_{$teacher->id}");
            Cache::forget("teacher_pending_grades_{$teacher->id}");
            Cache::forget("teacher_announcements_{$teacher->id}");
        }
    }

    /**
     * Invalidate cache by tag.
     */
    public static function invalidateByTag(string $tag): void
    {
        // Laravel's file/cache driver doesn't support tags well
        // This is a simplified implementation
        // For production, consider using Redis or Memcached with proper tag support

        switch ($tag) {
            case self::TAG_USERS:
                // Invalidate all user-related caches
                $users = \App\Models\User::all();
                foreach ($users as $user) {
                    self::invalidateUser($user->id);
                }
                break;

            case self::TAG_COURSES:
                // Invalidate all course-related caches
                $courses = \App\Models\Course::all();
                foreach ($courses as $course) {
                    self::invalidateCourse($course->id);
                }
                break;

            case self::TAG_ENROLLMENTS:
                // Invalidate enrollment-related caches
                Cache::forget('admin_dashboard_stats');
                break;

            case self::TAG_GRADES:
                // Invalidate grade-related caches
                $grades = \App\Models\StudentGrade::all();
                foreach ($grades as $grade) {
                    self::invalidateGrade($grade->id);
                }
                break;

            case self::TAG_PAYMENTS:
                // Invalidate payment-related caches
                $payments = \App\Models\Payment::all();
                foreach ($payments as $payment) {
                    self::invalidatePayment($payment->id);
                }
                break;

            case self::TAG_ANNOUNCEMENTS:
                // Invalidate announcement-related caches
                $announcements = \App\Models\Announcement::all();
                foreach ($announcements as $announcement) {
                    self::invalidateAnnouncement($announcement->id);
                }
                break;

            case self::TAG_SETTINGS:
                self::invalidateSettings();
                break;

            case self::TAG_DASHBOARD:
                self::invalidateAllDashboards();
                break;
        }
    }

    /**
     * Clear all application caches.
     */
    public static function clearAll(): void
    {
        Cache::flush();
    }

    /**
     * Get cache statistics.
     */
    public static function getStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
            'store' => config('cache.stores.'.config('cache.default').'.driver'),
        ];
    }
}
