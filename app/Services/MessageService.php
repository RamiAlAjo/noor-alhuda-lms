<?php

namespace App\Services;

use App\Jobs\SendScheduledMessage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\MessageReaction;
use App\Models\MessageTemplate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class MessageService
{
    /**
     * Create a new conversation.
     */
    public function createConversation(array $participantIds, array $data = []): Conversation
    {
        $isGroup = count($participantIds) > 2;

        $conversation = Conversation::create([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'type' => $isGroup ? Conversation::TYPE_GROUP : Conversation::TYPE_DIRECT,
            'is_group' => $isGroup,
            'created_by' => auth()->id(),
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Add participants
        foreach ($participantIds as $userId) {
            $conversation->addParticipant(User::find($userId));
        }

        return $conversation;
    }

    /**
     * Send a message in a conversation.
     */
    public function sendMessage(
        int $conversationId,
        string $content,
        array $options = []
    ): Message {
        $conversation = Conversation::findOrFail($conversationId);
        $sender = auth()->user();

        // Check if user is participant
        if (! $conversation->hasParticipant($sender->id)) {
            throw new \Exception('You are not a participant in this conversation.');
        }

        // Parse mentions from content
        $mentions = $this->parseMentions($content, $conversationId);

        $message = Message::createEnhanced([
            'conversation_id' => $conversationId,
            'sender_id' => $sender->id,
            'content' => $content,
            'subject' => $options['subject'] ?? null,
            'message_type' => $options['message_type'] ?? Message::TYPE_TEXT,
            'priority' => $options['priority'] ?? Message::PRIORITY_NORMAL,
            'parent_id' => $options['parent_id'] ?? null,
            'scheduled_at' => $options['scheduled_at'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'tags' => $options['tags'] ?? [],
            'mentions' => $mentions,
        ]);

        // Handle attachments
        if (! empty($options['attachments'])) {
            $this->attachFiles($message, $options['attachments']);
        }

        // Update conversation last message
        $conversation->updateLastMessageAt();

        // Send mention notifications
        if (! empty($mentions)) {
            $this->sendMentionNotifications($message, $mentions);
        }

        // Send real-time notification
        $message->send();

        // Handle scheduled messages
        if ($message->scheduled_at && $message->scheduled_at->isFuture()) {
            SendScheduledMessage::dispatch($message)->delay($message->scheduled_at);
        }

        return $message;
    }

    /**
     * Send a direct message to a user.
     */
    public function sendDirectMessage(int $receiverId, string $content, array $options = []): Message
    {
        $sender = auth()->user();

        // Find or create direct conversation
        $conversation = Conversation::where('is_group', false)
            ->whereHas('participants', function ($query) use ($sender, $receiverId) {
                $query->whereIn('user_id', [$sender->id, $receiverId]);
            })
            ->first();

        if (! $conversation) {
            $conversation = $this->createConversation([$sender->id, $receiverId]);
        }

        return $this->sendMessage($conversation->id, $content, $options);
    }

    /**
     * Send a message using a template.
     */
    public function sendTemplatedMessage(
        int $receiverId,
        int $templateId,
        array $variables = []
    ): Message {
        $template = MessageTemplate::findOrFail($templateId);

        $content = $template->render($variables);
        $subject = $template->renderSubject($variables);

        $message = $this->sendDirectMessage($receiverId, $content, [
            'subject' => $subject,
            'message_type' => Message::TYPE_TEMPLATE,
            'metadata' => [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'variables' => $variables,
            ],
        ]);

        $template->incrementUsage();

        return $message;
    }

    /**
     * Send bulk messages to multiple users.
     */
    public function sendBulkMessage(
        array $receiverIds,
        string $content,
        array $options = []
    ): Collection {
        $messages = collect();

        foreach ($receiverIds as $receiverId) {
            try {
                $message = $this->sendDirectMessage($receiverId, $content, array_merge($options, [
                    'message_type' => Message::TYPE_BULK,
                ]));
                $messages->push($message);
            } catch (\Exception $e) {
                \Log::error("Failed to send bulk message to user {$receiverId}: {$e->getMessage()}");
            }
        }

        return $messages;
    }

    /**
     * Attach files to a message.
     */
    public function attachFiles(Message $message, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('message-attachments', 'public');

                MessageAttachment::create([
                    'message_id' => $message->id,
                    'filename' => uniqid().'_'.$file->getClientOriginalName(),
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'file_path' => $path,
                    'file_type' => $this->determineFileType($file->getMimeType()),
                    'metadata' => [
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_by' => auth()->id(),
                        'uploaded_at' => now(),
                    ],
                ]);
            }
        }

        // Update message attachments array
        $message->update([
            'attachments' => $message->messageAttachments->pluck('id')->toArray(),
        ]);
    }

    /**
     * Determine file type from MIME type.
     */
    private function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return MessageAttachment::TYPE_IMAGE;
        } elseif (str_starts_with($mimeType, 'video/')) {
            return MessageAttachment::TYPE_VIDEO;
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return MessageAttachment::TYPE_AUDIO;
        } elseif (in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'])) {
            return MessageAttachment::TYPE_DOCUMENT;
        }

        return MessageAttachment::TYPE_OTHER;
    }

    /**
     * Get conversation messages with pagination.
     */
    public function getConversationMessages(int $conversationId, int $perPage = 50): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $conversation = Conversation::findOrFail($conversationId);

        // Check if user is participant
        if (! auth()->check() || ! $conversation->hasParticipant(auth()->id())) {
            throw new \Exception('You do not have permission to view this conversation.');
        }

        return $conversation->messages()
            ->with(['sender', 'messageAttachments', 'parent.sender'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get user's conversations.
     */
    public function getUserConversations(int $userId, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('conversation_participants.user_id', $userId);
        })
            ->with(['latestMessage.sender', 'participants' => function ($query) use ($userId) {
                $query->where('conversation_participants.user_id', '!=', $userId);
            }])
            ->orderBy('last_message_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Mark conversation as read for user.
     */
    public function markConversationAsRead(int $conversationId, int $userId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->hasParticipant($userId)) {
            $conversation->markAsReadForUser($userId);
        }
    }

    /**
     * Add reaction to message.
     */
    public function addReaction(int $messageId, string $reactionType): MessageReaction
    {
        $userId = auth()->id();
        $message = Message::findOrFail($messageId);

        // Check if user can react to this message (must be in the same conversation)
        if (! $message->conversation || ! $message->conversation->hasParticipant($userId)) {
            throw new \Exception('You cannot react to this message.');
        }

        // Remove existing reaction of the same type by this user
        MessageReaction::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('reaction_type', $reactionType)
            ->delete();

        // Add new reaction
        return MessageReaction::create([
            'message_id' => $messageId,
            'user_id' => $userId,
            'reaction_type' => $reactionType,
        ]);
    }

    /**
     * Remove reaction from message.
     */
    public function removeReaction(int $messageId, string $reactionType): bool
    {
        $userId = auth()->id();

        return MessageReaction::where('message_id', $messageId)
            ->where('user_id', $userId)
            ->where('reaction_type', $reactionType)
            ->delete() > 0;
    }

    /**
     * Archive conversation.
     */
    public function archiveConversation(int $conversationId, int $userId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->hasParticipant($userId)) {
            $conversation->update(['is_archived' => true]);
        }
    }

    /**
     * Unarchive conversation.
     */
    public function unarchiveConversation(int $conversationId, int $userId): void
    {
        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->hasParticipant($userId)) {
            $conversation->update(['is_archived' => false]);
        }
    }

    /**
     * Delete message (soft delete).
     */
    public function deleteMessage(int $messageId, int $userId): void
    {
        $message = Message::findOrFail($messageId);

        // Only allow delete if user is sender or receiver
        if ($message->sender_id === $userId || $message->receiver_id === $userId) {
            $message->softDelete();
        } else {
            throw new \Exception('You do not have permission to delete this message.');
        }
    }

    /**
     * Search messages.
     */
    public function searchMessages(int $userId, string $query, array $filters = []): Collection
    {
        $messages = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->orWhereHas('conversation.participants', function ($p) use ($userId) {
                    $p->where('user_id', $userId);
                });
        })
            ->where(function ($q) use ($query) {
                $q->where('subject', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            });

        // Apply filters
        if (! empty($filters['type'])) {
            $messages->where('message_type', $filters['type']);
        }

        if (! empty($filters['priority'])) {
            $messages->where('priority', $filters['priority']);
        }

        if (! empty($filters['date_from'])) {
            $messages->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $messages->where('created_at', '<=', $filters['date_to']);
        }

        return $messages->with(['sender', 'receiver', 'conversation'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    /**
     * Search messages within a specific conversation.
     */
    public function searchMessagesInConversation(int $conversationId, string $query): Collection
    {
        return Message::where('conversation_id', $conversationId)
            ->where(function ($q) use ($query) {
                $q->where('content', 'like', "%{$query}%")
                    ->orWhere('subject', 'like', "%{$query}%");
            })
            ->with(['sender', 'messageAttachments'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Parse mentions from message content.
     */
    public function parseMentions(string $content, int $conversationId): array
    {
        preg_match_all('/@(\w+)/', $content, $matches);

        if (empty($matches[1])) {
            return [];
        }

        $usernames = array_unique($matches[1]);
        $conversation = Conversation::find($conversationId);

        if (! $conversation) {
            return [];
        }

        $mentionedUsers = [];
        foreach ($usernames as $username) {
            $user = $conversation->participants->first(function ($participant) use ($username) {
                return $participant->name === $username || $participant->email === $username;
            });

            if ($user) {
                $mentionedUsers[] = $user->id;
            }
        }

        return array_unique($mentionedUsers);
    }

    /**
     * Send mention notifications.
     */
    public function sendMentionNotifications(Message $message, array $mentionedUserIds): void
    {
        foreach ($mentionedUserIds as $userId) {
            if ($userId === $message->sender_id) {
                continue; // Don't notify sender about their own mentions
            }

            try {
                // Create mention notification
                $notification = \App\Models\Notification::create([
                    'user_id' => $userId,
                    'type' => 'message_mention',
                    'title' => __('You were mentioned in a message'),
                    'message' => __('{sender} mentioned you in {conversation}', [
                        'sender' => $message->sender->name,
                        'conversation' => $message->conversation->display_title,
                    ]),
                    'data' => [
                        'message_id' => $message->id,
                        'conversation_id' => $message->conversation_id,
                        'sender_id' => $message->sender_id,
                        'sender_name' => $message->sender->name,
                    ],
                    'action_url' => route('messages.conversation', $message->conversation_id),
                ]);

                // Send real-time notification if user is online
                broadcast(new \App\Events\NotificationSent($notification));
            } catch (\Exception $e) {
                // Log error but don't fail the message sending
                \Log::error('Failed to send mention notification: '.$e->getMessage());
            }
        }
    }

    /**
     * Pin a message in conversation.
     */
    public function pinMessage(int $messageId): bool
    {
        $userId = auth()->id();
        $message = Message::findOrFail($messageId);

        // Check if user can pin (must be conversation participant or admin)
        if (! $message->conversation || ! $message->conversation->hasParticipant($userId)) {
            throw new \Exception('You cannot pin messages in this conversation.');
        }

        // Check if user is admin for group conversations
        if ($message->conversation->is_group) {
            $participant = $message->conversation->participants()->where('users.id', $userId)->first();
            if (! $participant || ! $participant->pivot->is_admin) {
                throw new \Exception('Only admins can pin messages in group conversations.');
            }
        }

        return $message->update(['is_pinned' => true]);
    }

    /**
     * Unpin a message in conversation.
     */
    public function unpinMessage(int $messageId): bool
    {
        $userId = auth()->id();
        $message = Message::findOrFail($messageId);

        // Check if user can unpin (must be conversation participant or admin)
        if (! $message->conversation || ! $message->conversation->hasParticipant($userId)) {
            throw new \Exception('You cannot unpin messages in this conversation.');
        }

        // Check if user is admin for group conversations
        if ($message->conversation->is_group) {
            $participant = $message->conversation->participants()->where('users.id', $userId)->first();
            if (! $participant || ! $participant->pivot->is_admin) {
                throw new \Exception('Only admins can unpin messages in group conversations.');
            }
        }

        return $message->update(['is_pinned' => false]);
    }

    /**
     * Get pinned messages for a conversation.
     */
    public function getPinnedMessages(int $conversationId): Collection
    {
        return Message::where('conversation_id', $conversationId)
            ->where('is_pinned', true)
            ->with(['sender', 'messageAttachments'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Set typing status for a user in a conversation.
     */
    public function setTypingStatus(int $conversationId, int $userId, bool $isTyping): void
    {
        $key = "conversation:{$conversationId}:typing:{$userId}";

        if ($isTyping) {
            \Cache::put($key, true, 10); // Typing status expires in 10 seconds
        } else {
            \Cache::forget($key);
        }
    }

    /**
     * Get typing users for a conversation.
     */
    public function getTypingUsers(int $conversationId): Collection
    {
        $conversation = Conversation::find($conversationId);
        if (! $conversation) {
            return collect();
        }

        $typingUsers = collect();

        foreach ($conversation->participants as $participant) {
            $key = "conversation:{$conversationId}:typing:{$participant->id}";
            if (\Cache::has($key) && $participant->id !== auth()->id()) {
                $typingUsers->push($participant);
            }
        }

        return $typingUsers;
    }

    /**
     * Get message analytics.
     */
    public function getAnalytics(?\DateTime $startDate = null, ?\DateTime $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $messages = Message::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_messages' => $messages->count(),
            'messages_by_type' => $messages->selectRaw('message_type, COUNT(*) as count')
                ->groupBy('message_type')
                ->pluck('count', 'message_type')
                ->toArray(),
            'messages_by_priority' => $messages->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
            'read_rate' => $messages->count() > 0
                         ? ($messages->where('is_read', true)->count() / $messages->count()) * 100
                         : 0,
            'avg_response_time' => $this->calculateAverageResponseTime($startDate, $endDate),
            'most_active_users' => $this->getMostActiveUsers($startDate, $endDate),
            'attachment_stats' => $this->getAttachmentStats($startDate, $endDate),
        ];
    }

    /**
     * Calculate average response time.
     */
    private function calculateAverageResponseTime(\DateTime $startDate, \DateTime $endDate): ?float
    {
        // This is a simplified calculation - in reality, you'd need to track conversation threads
        $messages = Message::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('read_at')
            ->get();

        if ($messages->isEmpty()) {
            return null;
        }

        $totalTime = 0;
        foreach ($messages as $message) {
            $totalTime += $message->read_at->diffInMinutes($message->created_at);
        }

        return $totalTime / $messages->count();
    }

    /**
     * Get most active users.
     */
    private function getMostActiveUsers(\DateTime $startDate, \DateTime $endDate, int $limit = 10): array
    {
        return Message::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('sender_id, COUNT(*) as message_count')
            ->groupBy('sender_id')
            ->with('sender:id,name,email')
            ->orderBy('message_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->sender,
                    'message_count' => $item->message_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get attachment statistics.
     */
    private function getAttachmentStats(\DateTime $startDate, \DateTime $endDate): array
    {
        $attachments = MessageAttachment::whereHas('message', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        });

        return [
            'total_attachments' => $attachments->count(),
            'total_size' => $attachments->sum('file_size'),
            'by_type' => $attachments->selectRaw('file_type, COUNT(*) as count, SUM(file_size) as size')
                ->groupBy('file_type')
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => $item->file_type,
                        'count' => $item->count,
                        'size' => $item->size,
                    ];
                })
                ->toArray(),
        ];
    }

    /**
     * Clean up expired messages.
     */
    public function cleanupExpiredMessages(): int
    {
        $expiredMessages = Message::expired()->get();

        $count = 0;
        foreach ($expiredMessages as $message) {
            $message->delete();
            $count++;
        }

        return $count;
    }

    /**
     * Export conversation to PDF or other formats.
     */
    public function exportConversation(int $conversationId, string $format = 'pdf'): string
    {
        $conversation = Conversation::with('messages.sender')->findOrFail($conversationId);

        // This would implement PDF generation, for now return placeholder
        return "Export functionality would generate {$format} for conversation {$conversation->id}";
    }
}
