<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'topic_id',
        'user_id',
        'parent_id',
        'content',
        'is_best_answer',
        'marked_best_by',
    ];

    protected $casts = [
        'is_best_answer' => 'boolean',
    ];

    /**
     * Get the topic this reply belongs to.
     */
    public function topic()
    {
        return $this->belongsTo(DiscussionTopic::class, 'topic_id');
    }

    /**
     * Get the user who wrote this reply.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent reply (if this is a nested reply).
     */
    public function parent()
    {
        return $this->belongsTo(DiscussionReply::class, 'parent_id');
    }

    /**
     * Get the child replies (nested replies).
     */
    public function children()
    {
        return $this->hasMany(DiscussionReply::class, 'parent_id');
    }

    /**
     * Get the user who marked this as best answer.
     */
    public function markedBestByUser()
    {
        return $this->belongsTo(User::class, 'marked_best_by');
    }

    /**
     * Check if this is a best answer.
     */
    public function isBestAnswer(): bool
    {
        return $this->is_best_answer;
    }

    /**
     * Mark as best answer.
     */
    public function markAsBest(int $userId): void
    {
        // Remove best answer from other replies in the same topic
        static::where('topic_id', $this->topic_id)
            ->where('is_best_answer', true)
            ->update(['is_best_answer' => false, 'marked_best_by' => null]);

        $this->update([
            'is_best_answer' => true,
            'marked_best_by' => $userId,
        ]);
    }

    /**
     * Unmark as best answer.
     */
    public function unmarkAsBest(): void
    {
        $this->update([
            'is_best_answer' => false,
            'marked_best_by' => null,
        ]);
    }

    /**
     * Scope to get best answers.
     */
    public function scopeBestAnswers($query)
    {
        return $query->where('is_best_answer', true);
    }

    /**
     * Scope to get root replies (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
