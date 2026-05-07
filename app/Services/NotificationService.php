<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Notification types with their default icons and colors.
     */
    const TYPES = [
        'announcement' => ['icon' => 'megaphone', 'color' => 'blue'],
        'grade' => ['icon' => 'academic-cap', 'color' => 'green'],
        'enrollment' => ['icon' => 'user-plus', 'color' => 'indigo'],
        'payment' => ['icon' => 'currency-dollar', 'color' => 'yellow'],
        'message' => ['icon' => 'chat-bubble-left', 'color' => 'purple'],
        'reminder' => ['icon' => 'clock', 'color' => 'orange'],
        'course' => ['icon' => 'book-open', 'color' => 'cyan'],
        'system' => ['icon' => 'cog-6-tooth', 'color' => 'gray'],
        'warning' => ['icon' => 'exclamation-triangle', 'color' => 'red'],
        'success' => ['icon' => 'check-circle', 'color' => 'green'],
    ];

    /**
     * Send a notification to a specific user.
     */
    public function sendToUser(
        User $user,
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null
    ): Notification {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'data' => array_merge($data ?? [], [
                'icon' => self::TYPES[$type]['icon'] ?? 'bell',
                'color' => self::TYPES[$type]['color'] ?? 'blue',
            ]),
        ]);

        // Broadcast the notification
        broadcast(new NotificationSent($notification));

        return $notification;
    }

    /**
     * Send a notification to multiple users.
     */
    public function sendToUsers(
        iterable $users,
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null
    ): void {
        foreach ($users as $user) {
            $this->sendToUser($user, $type, $title, $content, $link, $data);
        }
    }

    /**
     * Send a notification to all users with a specific role.
     */
    public function sendToRole(
        string $role,
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null
    ): void {
        $users = User::role($role)->get();
        $this->sendToUsers($users, $type, $title, $content, $link, $data);
    }

    /**
     * Send a notification to all users.
     */
    public function sendToAll(
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null
    ): void {
        $users = User::all();
        $this->sendToUsers($users, $type, $title, $content, $link, $data);
    }

    /**
     * Send a grade notification to a student.
     */
    public function sendGradeNotification(User $student, string $courseName, string $assessmentName, $grade): Notification
    {
        return $this->sendToUser(
            $student,
            'grade',
            __('New Grade Posted'),
            __('Your grade for :assessment in :course has been posted: :grade', [
                'assessment' => $assessmentName,
                'course' => $courseName,
                'grade' => $grade,
            ]),
            route('student.grades'),
            ['course' => $courseName, 'assessment' => $assessmentName, 'grade' => $grade]
        );
    }

    /**
     * Send an enrollment notification.
     */
    public function sendEnrollmentNotification(User $user, string $courseName, string $status): Notification
    {
        return $this->sendToUser(
            $user,
            'enrollment',
            __('Enrollment :status', ['status' => ucfirst($status)]),
            __('Your enrollment request for :course has been :status.', [
                'course' => $courseName,
                'status' => $status,
            ]),
            route('student.courses.index'),
            ['course' => $courseName, 'status' => $status]
        );
    }

    /**
     * Send a payment notification.
     */
    public function sendPaymentNotification(User $user, string $amount, string $status): Notification
    {
        return $this->sendToUser(
            $user,
            'payment',
            __('Payment :status', ['status' => ucfirst($status)]),
            __('Your payment of :amount has been :status.', [
                'amount' => $amount,
                'status' => $status,
            ]),
            route('student.payments.index'),
            ['amount' => $amount, 'status' => $status]
        );
    }

    /**
     * Send a reminder notification.
     */
    public function sendReminderNotification(User $user, string $title, string $message, ?string $link = null): Notification
    {
        return $this->sendToUser(
            $user,
            'reminder',
            $title,
            $message,
            $link
        );
    }

    /**
     * Send a course announcement notification.
     */
    public function sendCourseAnnouncement(User $user, string $courseName, string $announcementTitle, int $courseId): Notification
    {
        return $this->sendToUser(
            $user,
            'course',
            __('New Announcement in :course', ['course' => $courseName]),
            $announcementTitle,
            route('student.courses.show', $courseId),
            ['course' => $courseName, 'course_id' => $courseId]
        );
    }

    /**
     * Send a system notification.
     */
    public function sendSystemNotification(User $user, string $title, string $message, ?string $link = null): Notification
    {
        return $this->sendToUser(
            $user,
            'system',
            $title,
            $message,
            $link
        );
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get unread notifications for a user.
     */
    public function getUnread(User $user, int $limit = 10)
    {
        return $user->notifications()->unread()->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    /**
     * Get all notifications for a user with pagination.
     */
    public function getAll(User $user, int $perPage = 15)
    {
        return $user->notifications()->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Delete old read notifications.
     */
    public function pruneOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('is_read', true)
            ->where('read_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
