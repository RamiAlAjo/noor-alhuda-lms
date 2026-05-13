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
     * Send a notification to a specific user with enhanced features and retry logic.
     */
    public function sendToUser(
        User $user,
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null,
        bool $forceEmail = false,
        int $maxRetries = 3
    ): Notification {
        // Check user preferences
        if (! Notification::userWantsNotification($user->id, $type)) {
            // Create notification but don't send it
            return Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'link' => $link,
                'data' => array_merge($data ?? [], [
                    'icon' => self::TYPES[$type]['icon'] ?? 'bell',
                    'color' => self::TYPES[$type]['color'] ?? 'blue',
                    'suppressed' => true, // Mark as suppressed due to user preferences
                ]),
            ]);
        }

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'data' => array_merge($data ?? [], [
                'icon' => self::TYPES[$type]['icon'] ?? 'bell',
                'color' => self::TYPES[$type]['color'] ?? 'blue',
                'retry_count' => 0,
            ]),
        ]);

        // Try to broadcast the notification with retry logic
        $broadcastSuccess = false;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                broadcast(new NotificationSent($notification));
                $broadcastSuccess = true;
                break;
            } catch (\Exception $e) {
                \Log::warning("Notification broadcasting failed (attempt {$attempt}/{$maxRetries}): ".$e->getMessage());

                if ($attempt < $maxRetries) {
                    sleep(1); // Wait 1 second before retry
                }
            }
        }

        // Update retry count in notification data
        if (! $broadcastSuccess) {
            $notification->update([
                'data' => array_merge($notification->data ?? [], [
                    'broadcast_failed' => true,
                    'retry_count' => $maxRetries,
                ]),
            ]);
        }

        // Send email fallback for important notifications
        if ($this->shouldSendEmail($type) || $forceEmail) {
            $this->sendEmailNotification($user, $notification);
        }

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
     * Send a grade notification to a student using template.
     */
    public function sendGradeNotification(User $student, string $courseName, string $assessmentName, $grade): Notification
    {
        $template = \App\Models\NotificationTemplate::getByKey('grade_posted');

        if ($template) {
            $variables = [
                'student_name' => $student->name,
                'course_name' => $courseName,
                'assessment_name' => $assessmentName,
                'grade' => $grade,
            ];

            return $this->sendToUser(
                $student,
                'grade',
                $template->render(['course_name' => $courseName, 'assessment_name' => $assessmentName, 'grade' => $grade]),
                $template->render($variables),
                route('student.grades'),
                array_merge($variables, ['template_used' => true])
            );
        }

        // Fallback to original method
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

    /**
     * Determine if email fallback should be sent for this notification type.
     */
    private function shouldSendEmail(string $type): bool
    {
        $importantTypes = ['grade', 'payment', 'system', 'warning'];

        return in_array($type, $importantTypes);
    }

    /**
     * Send email notification fallback.
     */
    private function sendEmailNotification(User $user, Notification $notification): void
    {
        try {
            // Check if user wants email notifications
            $preferences = Notification::getUserPreferences($user->id);
            if (! $preferences['email_notifications']) {
                return;
            }

            \Mail::send([], [], function ($message) use ($user, $notification) {
                $message->to($user->email)
                    ->subject('Noor LMS: '.$notification->title)
                    ->html(
                        view('emails.notification', [
                            'user' => $user,
                            'notification' => $notification,
                        ])->render()
                    );
            });

            \Log::info("Email notification sent to {$user->email} for notification {$notification->id}");
        } catch (\Exception $e) {
            \Log::error("Failed to send email notification: {$e->getMessage()}");
        }
    }

    /**
     * Get notification analytics.
     */
    public function getAnalytics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $query = Notification::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_sent' => $query->count(),
            'by_type' => $query->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'read_rate' => $query->count() > 0
                         ? ($query->where('is_read', true)->count() / $query->count()) * 100
                         : 0,
            'avg_time_to_read' => $this->calculateAverageTimeToRead($startDate, $endDate),
            'top_senders' => $this->getTopSenders($startDate, $endDate),
        ];
    }

    /**
     * Calculate average time to read notifications.
     */
    private function calculateAverageTimeToRead(\DateTime $startDate, \DateTime $endDate): ?float
    {
        $notifications = Notification::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('read_at')
            ->get();

        if ($notifications->isEmpty()) {
            return null;
        }

        $totalTime = 0;
        foreach ($notifications as $notification) {
            $totalTime += $notification->read_at->diffInMinutes($notification->created_at);
        }

        return $totalTime / $notifications->count();
    }

    /**
     * Get top notification senders.
     */
    private function getTopSenders(\DateTime $startDate, \DateTime $endDate): array
    {
        // This would require tracking who sent notifications
        // For now, return types as proxy
        return Notification::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Send bulk notifications to administrators.
     */
    public function sendToAdmins(
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null
    ): int {
        $admins = User::role('admin')->get();
        $count = 0;

        foreach ($admins as $admin) {
            $this->sendToUser($admin, $type, $title, $content, $link, $data);
            $count++;
        }

        return $count;
    }

    /**
     * Send bulk notifications to all users with a specific role.
     */
    public function sendBulkToRole(
        string $role,
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null,
        array $excludeUserIds = []
    ): int {
        $users = User::role($role)->whereNotIn('id', $excludeUserIds)->get();
        $count = 0;

        foreach ($users as $user) {
            $this->sendToUser($user, $type, $title, $content, $link, $data);
            $count++;
        }

        return $count;
    }
}
