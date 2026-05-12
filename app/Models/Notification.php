<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'content',
        'link',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Notification type configurations with icons and colors.
     */
    public static function getTypeConfig(string $type): array
    {
        $configs = [
            'grade' => ['icon' => 'academic-cap', 'color' => 'indigo', 'label' => 'Grade'],
            'enrollment' => ['icon' => 'user-group', 'color' => 'green', 'label' => 'Enrollment'],
            'payment' => ['icon' => 'currency-dollar', 'color' => 'emerald', 'label' => 'Payment'],
            'reminder' => ['icon' => 'clock', 'color' => 'amber', 'label' => 'Reminder'],
            'announcement' => ['icon' => 'megaphone', 'color' => 'purple', 'label' => 'Announcement'],
            'course' => ['icon' => 'book-open', 'color' => 'blue', 'label' => 'Course'],
            'quiz' => ['icon' => 'clipboard-document-check', 'color' => 'orange', 'label' => 'Quiz'],
            'message' => ['icon' => 'chat-bubble-left-right', 'color' => 'cyan', 'label' => 'Message'],
            'system' => ['icon' => 'cog', 'color' => 'zinc', 'label' => 'System'],
            'default' => ['icon' => 'bell', 'color' => 'slate', 'label' => 'Notification'],
        ];

        return $configs[$type] ?? $configs['default'];
    }

    /**
     * Get the icon name for this notification.
     */
    public function getIconAttribute(): string
    {
        $config = self::getTypeConfig($this->type);

        return $this->data['icon'] ?? $config['icon'];
    }

    /**
     * Get the color for this notification.
     */
    public function getColorAttribute(): string
    {
        $config = self::getTypeConfig($this->type);

        return $this->data['color'] ?? $config['color'];
    }

    /**
     * Get the label for this notification type.
     */
    public function getTypeLabelAttribute(): string
    {
        $config = self::getTypeConfig($this->type);

        return $config['label'];
    }

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to get notifications of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get recent notifications.
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Create a notification for a user.
     */
    public static function createForUser(
        User $user,
        string $type,
        string $title,
        string $content,
        ?string $link = null,
        ?array $data = null
    ): self {
        // Merge type config into data
        $typeConfig = self::getTypeConfig($type);
        $data = array_merge($data ?? [], [
            'icon' => $typeConfig['icon'],
            'color' => $typeConfig['color'],
        ]);

        return self::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'data' => $data,
        ]);
    }

    /**
     * Get a formatted time string.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if notification is new (created within last hour).
     */
    public function isNew(): bool
    {
        return $this->created_at->greaterThan(now()->subHour());
    }

    /**
     * Get notification preferences for a user
     */
    public static function getUserPreferences(int $userId): array
    {
        $settings = \App\Models\UserSetting::where('user_id', $userId)->first();

        return [
            'email_notifications' => $settings?->notification_email ?? true,
            'push_notifications' => $settings?->notification_push ?? true,
            'sound_notifications' => $settings?->notification_sound ?? true,
            'grade_notifications' => $settings?->notification_grades ?? true,
            'enrollment_notifications' => $settings?->notification_enrollment ?? true,
            'payment_notifications' => $settings?->notification_payments ?? true,
            'announcement_notifications' => $settings?->notification_announcements ?? true,
            'reminder_notifications' => $settings?->notification_reminders ?? true,
        ];
    }

    /**
     * Check if user wants this type of notification
     */
    public static function userWantsNotification(int $userId, string $type): bool
    {
        $preferences = self::getUserPreferences($userId);

        return match ($type) {
            'grade' => $preferences['grade_notifications'],
            'enrollment' => $preferences['enrollment_notifications'],
            'payment' => $preferences['payment_notifications'],
            'announcement' => $preferences['announcement_notifications'],
            'reminder' => $preferences['reminder_notifications'],
            default => true, // Allow other types by default
        };
    }
}
