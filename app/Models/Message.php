<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'conversation_id',
        'parent_id',
        'subject',
        'content',
        'message_type',
        'priority',
        'is_read',
        'read_at',
        'is_starred',
        'is_archived',
        'is_deleted',
        'deleted_at',
        'is_pinned',
        'mentions',
        'scheduled_at',
        'sent_at',
        'expires_at',
        'metadata',
        'attachments',
        'tags',
        'reactions',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'is_archived' => 'boolean',
        'is_deleted' => 'boolean',
        'read_at' => 'datetime',
        'deleted_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
        'attachments' => 'array',
        'tags' => 'array',
        'reactions' => 'array',
    ];

    /**
     * Message types
     */
    const TYPE_TEXT = 'text';
    const TYPE_SYSTEM = 'system';
    const TYPE_TEMPLATE = 'template';
    const TYPE_SCHEDULED = 'scheduled';
    const TYPE_BULK = 'bulk';

    /**
     * Priority levels
     */
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the conversation this message belongs to.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Get the parent message (for replies).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    /**
     * Get child messages (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    /**
     * Get message reactions.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    /**
     * Format content with highlighted mentions.
     */
    public function formatContentWithMentions(): string
    {
        $content = $this->content;

        if ($this->mentions && is_array($this->mentions)) {
            foreach ($this->mentions as $userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $mention = '@' . $user->name;
                    $highlighted = '<span class="bg-blue-100 text-blue-800 px-1 py-0.5 rounded text-xs font-medium dark:bg-blue-900 dark:text-blue-200">@' . $user->name . '</span>';
                    $content = str_replace($mention, $highlighted, $content);
                }
            }
        }

        return $content;
    }

    /**
     * Get message attachments.
     */
    public function messageAttachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    /**
     * Get message recipients (for bulk messages).
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_recipients', 'message_id', 'user_id')
                    ->withPivot('is_read', 'read_at', 'delivered_at')
                    ->withTimestamps();
    }

    /**
     * Scope for unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for starred messages.
     */
    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    /**
     * Scope for archived messages.
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope for inbox messages.
     */
    public function scopeInbox($query, $userId)
    {
        return $query->where('receiver_id', $userId)
                    ->where('is_deleted', false)
                    ->where('is_archived', false)
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for sent messages.
     */
    public function scopeSent($query, $userId)
    {
        return $query->where('sender_id', $userId)
                    ->where('is_deleted', false)
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for messages by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Scope for messages by priority.
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for scheduled messages.
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('sent_at', null);
    }

    /**
     * Scope for expired messages.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<', now());
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            // Clear cache
            Cache::forget("message_{$this->id}_read_status");
        }
    }

    /**
     * Mark message as unread.
     */
    public function markAsUnread(): void
    {
        if ($this->is_read) {
            $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);

            // Clear cache
            Cache::forget("message_{$this->id}_read_status");
        }
    }

    /**
     * Star/unstar message.
     */
    public function toggleStar(): void
    {
        $this->update(['is_starred' => !$this->is_starred]);
    }

    /**
     * Archive/unarchive message.
     */
    public function toggleArchive(): void
    {
        $this->update(['is_archived' => !$this->is_archived]);
    }

    /**
     * Soft delete message.
     */
    public function softDelete(): void
    {
        $this->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);
    }

    /**
     * Add reaction to message.
     */
    public function addReaction(string $emoji, User $user): void
    {
        $reactions = $this->reactions ?? [];
        $userId = $user->id;

        if (!isset($reactions[$emoji])) {
            $reactions[$emoji] = [];
        }

        if (!in_array($userId, $reactions[$emoji])) {
            $reactions[$emoji][] = $userId;
            $this->update(['reactions' => $reactions]);
        }
    }

    /**
     * Remove reaction from message.
     */
    public function removeReaction(string $emoji, User $user): void
    {
        $reactions = $this->reactions ?? [];
        $userId = $user->id;

        if (isset($reactions[$emoji])) {
            $reactions[$emoji] = array_filter($reactions[$emoji], fn($id) => $id != $userId);

            if (empty($reactions[$emoji])) {
                unset($reactions[$emoji]);
            }

            $this->update(['reactions' => $reactions]);
        }
    }

    /**
     * Get reaction summary.
     */
    public function getReactionSummary(): array
    {
        $reactions = $this->reactions ?? [];
        $summary = [];

        foreach ($reactions as $emoji => $userIds) {
            $summary[$emoji] = [
                'count' => count($userIds),
                'users' => User::whereIn('id', $userIds)->pluck('name')->toArray(),
            ];
        }

        return $summary;
    }

    /**
     * Check if message is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get formatted time for display.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get priority color for UI.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_NORMAL => 'blue',
            self::PRIORITY_HIGH => 'orange',
            self::PRIORITY_URGENT => 'red',
            default => 'blue',
        };
    }

    /**
     * Get message preview (first 100 characters).
     */
    public function getPreviewAttribute(): string
    {
        return \Str::limit(strip_tags($this->content), 100);
    }

    /**
     * Check if message has attachments.
     */
    public function hasAttachments(): bool
    {
        return !empty($this->attachments) || $this->messageAttachments()->exists();
    }

    /**
     * Get attachment count.
     */
    public function getAttachmentCountAttribute(): int
    {
        $count = count($this->attachments ?? []);
        $count += $this->messageAttachments()->count();
        return $count;
    }

    /**
     * Create a new message with enhanced features.
     */
    public static function createEnhanced(array $attributes = []): self
    {
        // Set defaults
        $defaults = [
            'message_type' => self::TYPE_TEXT,
            'priority' => self::PRIORITY_NORMAL,
            'is_read' => false,
            'is_starred' => false,
            'is_archived' => false,
            'is_deleted' => false,
            'metadata' => [],
            'attachments' => [],
            'tags' => [],
            'reactions' => [],
        ];

        // Handle scheduling
        if (isset($attributes['scheduled_at'])) {
            $attributes['sent_at'] = null;
        } else {
            $attributes['sent_at'] = now();
        }

        $attributes = array_merge($defaults, $attributes);

        return static::create($attributes);
    }

    /**
     * Send a message with delivery tracking.
     */
    public function send(): bool
    {
        try {
            // Broadcast to real-time listeners
            broadcast(new \App\Events\MessageSent($this));

            // Mark as sent
            $this->update(['sent_at' => now()]);

            // Handle scheduled messages
            if ($this->scheduled_at && $this->scheduled_at->isFuture()) {
                // Message will be sent later by a scheduled job
                return true;
            }

            // Send email notification if enabled
            if (\App\Models\SystemSetting::get('message_email_notifications', true)) {
                $this->sendEmailNotification();
            }

            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send message {$this->id}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send email notification for the message.
     */
    private function sendEmailNotification(): void
    {
        try {
            $receiver = $this->receiver;

            if ($receiver && filter_var($receiver->email, FILTER_VALIDATE_EMAIL)) {
                \Mail::send('emails.message-notification', [
                    'message' => $this,
                    'sender' => $this->sender,
                    'receiver' => $receiver,
                ], function ($message) use ($receiver) {
                    $message->to($receiver->email)
                            ->subject("New message from {$this->sender->name}: {$this->subject}");
                });
            }
        } catch (\Exception $e) {
            \Log::warning("Failed to send email notification for message {$this->id}: {$e->getMessage()}");
        }
    }
}
