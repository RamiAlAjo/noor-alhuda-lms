<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'forum_id',
        'user_id',
        'title',
        'content',
        'is_locked',
        'is_pinned',
        'is_announcement',
        'views_count',
        'last_reply_at',
        'last_reply_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_pinned' => 'boolean',
        'is_announcement' => 'boolean',
        'views_count' => 'integer',
        'last_reply_at' => 'datetime',
    ];

    /**
     * Get the forum this topic belongs to.
     */
    public function forum()
    {
        return $this->belongsTo(DiscussionForum::class, 'forum_id');
    }

    /**
     * Get the user who created this topic.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who last replied.
     */
    public function lastReplyUser()
    {
        return $this->belongsTo(User::class, 'last_reply_by');
    }

    /**
     * Get the replies for this topic.
     */
    public function replies()
    {
        return $this->hasMany(DiscussionReply::class, 'topic_id');
    }

    /**
     * Get the root replies (no parent).
     */
    public function rootReplies()
    {
        return $this->replies()->whereNull('parent_id');
    }

    /**
     * Check if the topic is locked.
     */
    public function isLocked(): bool
    {
        return $this->is_locked || $this->forum->is_locked;
    }

    /**
     * Check if the topic is pinned.
     */
    public function isPinned(): bool
    {
        return $this->is_pinned;
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Update last reply info.
     */
    public function updateLastReply(int $userId): void
    {
        $this->update([
            'last_reply_at' => now(),
            'last_reply_by' => $userId,
        ]);
    }

    /**
     * Get the reply count.
     */
    public function getReplyCountAttribute(): int
    {
        return $this->replies()->count();
    }

    /**
     * Lock the topic.
     */
    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    /**
     * Unlock the topic.
     */
    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
    }

    /**
     * Pin the topic.
     */
    public function pin(): void
    {
        $this->update(['is_pinned' => true]);
    }

    /**
     * Unpin the topic.
     */
    public function unpin(): void
    {
        $this->update(['is_pinned' => false]);
    }

    /**
     * Scope to get pinned topics.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to get unlocked topics.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope to get announcements.
     */
    public function scopeAnnouncements($query)
    {
        return $query->where('is_announcement', true);
    }

    /**
     * Scope to order by last reply.
     */
    public function scopeOrderByLastReply($query)
    {
        return $query->orderByDesc('last_reply_at')->orderByDesc('created_at');
    }
}
