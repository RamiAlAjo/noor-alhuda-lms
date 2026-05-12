<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledNotification extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'recipients',
        'scheduled_at',
        'is_sent',
        'sent_at',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'recipients' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'metadata' => 'array',
        'is_sent' => 'boolean',
    ];

    /**
     * Get the user who created this scheduled notification.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for pending notifications.
     */
    public function scopePending($query)
    {
        return $query->where('is_sent', false)
                    ->where('scheduled_at', '<=', now());
    }

    /**
     * Scope for sent notifications.
     */
    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    /**
     * Send the scheduled notification.
     */
    public function send(): bool
    {
        $notificationService = app(\App\Services\NotificationService::class);

        try {
            $recipients = $this->getRecipientUsers();

            foreach ($recipients as $user) {
                $notificationService->sendToUser(
                    $user,
                    $this->type,
                    $this->title,
                    $this->content,
                    $this->metadata['link'] ?? null,
                    $this->metadata['data'] ?? [],
                    $this->metadata['force_email'] ?? false
                );
            }

            $this->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send scheduled notification {$this->id}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get the actual user recipients based on the recipients configuration.
     */
    private function getRecipientUsers()
    {
        $recipients = $this->recipients;

        if (isset($recipients['type'])) {
            switch ($recipients['type']) {
                case 'all':
                    return User::all();

                case 'role':
                    return User::role($recipients['role'])->get();

                case 'users':
                    return User::whereIn('id', $recipients['user_ids'])->get();

                case 'course':
                    return User::whereHas('enrollments', function ($query) use ($recipients) {
                        $query->whereHas('courseOffering', function ($q) use ($recipients) {
                            $q->where('course_id', $recipients['course_id']);
                        })->where('status', 'approved');
                    })->get();

                case 'semester':
                    return User::whereHas('enrollments', function ($query) use ($recipients) {
                        $query->where('semester_id', $recipients['semester_id'])
                              ->where('status', 'approved');
                    })->get();
            }
        }

        // Default: assume it's an array of user IDs
        return User::whereIn('id', $recipients)->get();
    }
}
