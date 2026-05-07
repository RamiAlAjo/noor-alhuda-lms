<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionForum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_offering_id',
        'title',
        'description',
        'is_locked',
        'is_pinned',
        'created_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    /**
     * Get the course offering this forum belongs to.
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the user who created this forum.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the topics in this forum.
     */
    public function topics()
    {
        return $this->hasMany(DiscussionTopic::class, 'forum_id');
    }

    /**
     * Get all replies in this forum through topics.
     */
    public function replies()
    {
        return $this->hasManyThrough(DiscussionReply::class, DiscussionTopic::class, 'forum_id', 'topic_id');
    }

    /**
     * Get pinned topics.
     */
    public function pinnedTopics()
    {
        return $this->topics()->where('is_pinned', true);
    }

    /**
     * Check if the forum is locked.
     */
    public function isLocked(): bool
    {
        return $this->is_locked;
    }

    /**
     * Lock the forum.
     */
    public function lock(): void
    {
        $this->update(['is_locked' => true]);
    }

    /**
     * Unlock the forum.
     */
    public function unlock(): void
    {
        $this->update(['is_locked' => false]);
    }

    /**
     * Get the topic count.
     */
    public function getTopicCountAttribute(): int
    {
        return $this->topics_count ?? $this->topics()->count();
    }

    /**
     * Get the reply count.
     */
    public function getReplyCountAttribute(): int
    {
        return $this->replies_count ?? $this->replies()->count();
    }

    /**
     * Get the latest topic.
     */
    public function latestTopic()
    {
        return $this->topics()->latest()->first();
    }

    /**
     * Scope to get pinned forums.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to get unlocked forums.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }
}
