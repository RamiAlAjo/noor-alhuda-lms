<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'is_group',
        'created_by',
        'last_message_at',
        'is_archived',
        'metadata',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'is_archived' => 'boolean',
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Conversation types
     */
    const TYPE_DIRECT = 'direct';

    const TYPE_GROUP = 'group';

    const TYPE_SYSTEM = 'system';

    /**
     * Get the creator of the conversation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get all participants in this conversation.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('joined_at', 'last_read_at', 'is_admin', 'is_muted')
            ->withTimestamps();
    }

    /**
     * Get the latest message in the conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Get unread message count for a user.
     */
    public function getUnreadCountForUser(int $userId): int
    {
        $lastReadAt = $this->participants()
            ->where('user_id', $userId)
            ->value('last_read_at');

        if (! $lastReadAt) {
            return $this->messages()->count();
        }

        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('created_at', '>', $lastReadAt)
            ->count();
    }

    /**
     * Mark conversation as read for a user.
     */
    public function markAsReadForUser(int $userId): void
    {
        $this->participants()
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);
    }

    /**
     * Add a participant to the conversation.
     */
    public function addParticipant(User $user, bool $isAdmin = false): void
    {
        $this->participants()->attach($user->id, [
            'joined_at' => now(),
            'is_admin' => $isAdmin,
            'is_muted' => false,
        ]);
    }

    /**
     * Remove a participant from the conversation.
     */
    public function removeParticipant(User $user): void
    {
        $this->participants()->detach($user->id);
    }

    /**
     * Check if user is a participant.
     */
    public function hasParticipant(int $userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Get conversation title for display.
     */
    public function getDisplayTitleAttribute(): string
    {
        if ($this->title) {
            return $this->title;
        }

        if (! $this->is_group) {
            // For direct messages, show the other participant's name
            $otherParticipant = $this->participants()
                ->where('user_id', '!=', auth()->id())
                ->first();

            return $otherParticipant ? $otherParticipant->name : 'Unknown User';
        }

        return 'Group Conversation';
    }

    /**
     * Get conversation avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if (! $this->is_group && $this->participants()->count() === 2) {
            $otherParticipant = $this->participants()
                ->where('user_id', '!=', auth()->id())
                ->first();

            return $otherParticipant ? $otherParticipant->getAvatarUrl() : '/default-avatar.png';
        }

        return '/group-avatar.png';
    }

    /**
     * Update last message timestamp.
     */
    public function updateLastMessageAt(): void
    {
        $this->update(['last_message_at' => now()]);
    }

    /**
     * Scope for active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope for group conversations.
     */
    public function scopeGroups($query)
    {
        return $query->where('is_group', true);
    }

    /**
     * Scope for direct conversations.
     */
    public function scopeDirect($query)
    {
        return $query->where('is_group', false);
    }
}
