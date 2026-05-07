<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log an audit event.
     */
    public static function log(string $action, string $description, array $properties = []): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log a user login event.
     */
    public static function logLogin(): void
    {
        self::log('login', 'User logged in');
    }

    /**
     * Log a user logout event.
     */
    public static function logLogout(): void
    {
        self::log('logout', 'User logged out');
    }

    /**
     * Log a user creation event.
     */
    public static function logUserCreated($user): void
    {
        self::log('user_created', "User created: {$user->email}", [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
        ]);
    }

    /**
     * Log a user update event.
     */
    public static function logUserUpdated($user, array $changes): void
    {
        self::log('user_updated', "User updated: {$user->email}", [
            'user_id' => $user->id,
            'changes' => $changes,
        ]);
    }

    /**
     * Log a user deletion event.
     */
    public static function logUserDeleted($user): void
    {
        self::log('user_deleted', "User deleted: {$user->email}", [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Log a course creation event.
     */
    public static function logCourseCreated($course): void
    {
        self::log('course_created', "Course created: {$course->name}", [
            'course_id' => $course->id,
            'name' => $course->name,
            'code' => $course->code,
        ]);
    }

    /**
     * Log a course update event.
     */
    public static function logCourseUpdated($course, array $changes): void
    {
        self::log('course_updated', "Course updated: {$course->name}", [
            'course_id' => $course->id,
            'changes' => $changes,
        ]);
    }

    /**
     * Log a course deletion event.
     */
    public static function logCourseDeleted($course): void
    {
        self::log('course_deleted', "Course deleted: {$course->name}", [
            'course_id' => $course->id,
            'name' => $course->name,
        ]);
    }

    /**
     * Log an enrollment event.
     */
    public static function logEnrollment($enrollment, string $action): void
    {
        self::log('enrollment_'.$action, "Enrollment {$action}: {$enrollment->student->email}", [
            'enrollment_id' => $enrollment->id,
            'student_id' => $enrollment->student_id,
            'course_offering_id' => $enrollment->course_offering_id,
            'status' => $enrollment->status,
        ]);
    }

    /**
     * Log a grade event.
     */
    public static function logGrade($grade, string $action): void
    {
        self::log('grade_'.$action, "Grade {$action} for student {$grade->student->email}", [
            'grade_id' => $grade->id,
            'student_id' => $grade->student_id,
            'assessment_id' => $grade->assessment_id,
            'marks_obtained' => $grade->marks_obtained,
        ]);
    }

    /**
     * Log a payment event.
     */
    public static function logPayment($payment, string $action): void
    {
        self::log('payment_'.$action, "Payment {$action}: {$payment->amount}", [
            'payment_id' => $payment->id,
            'student_id' => $payment->student_id,
            'amount' => $payment->amount,
            'status' => $payment->status,
        ]);
    }

    /**
     * Log a quiz attempt event.
     */
    public static function logQuizAttempt($attempt, string $action): void
    {
        self::log('quiz_attempt_'.$action, "Quiz attempt {$action} by student {$attempt->student->email}", [
            'attempt_id' => $attempt->id,
            'student_id' => $attempt->student_id,
            'quiz_id' => $attempt->quiz_id,
            'score' => $attempt->score,
        ]);
    }

    /**
     * Log a medical leave event.
     */
    public static function logMedicalLeave($leave, string $action): void
    {
        self::log('medical_leave_'.$action, "Medical leave {$action} for student {$leave->student->email}", [
            'leave_id' => $leave->id,
            'student_id' => $leave->student_id,
            'status' => $leave->status,
        ]);
    }

    /**
     * Log a grade appeal event.
     */
    public static function logGradeAppeal($appeal, string $action): void
    {
        self::log('grade_appeal_'.$action, "Grade appeal {$action} by student {$appeal->student->email}", [
            'appeal_id' => $appeal->id,
            'student_id' => $appeal->student_id,
            'grade_id' => $appeal->grade_id,
            'status' => $appeal->status,
        ]);
    }

    /**
     * Log a settings change event.
     */
    public static function logSettingsChanged(array $changes): void
    {
        self::log('settings_changed', 'System settings changed', [
            'changes' => $changes,
        ]);
    }

    /**
     * Log a bulk import event.
     */
    public static function logBulkImport(string $type, int $count, int $successCount, int $failureCount): void
    {
        self::log('bulk_import', "Bulk import of {$type}: {$successCount} successful, {$failureCount} failed", [
            'type' => $type,
            'total' => $count,
            'success' => $successCount,
            'failure' => $failureCount,
        ]);
    }
}
