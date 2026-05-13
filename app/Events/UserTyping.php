<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $conversationId;

    public User $user;

    public bool $isTyping;

    /**
     * Create a new event instance.
     */
    public function __construct(int $conversationId, User $user, bool $isTyping)
    {
        $this->conversationId = $conversationId;
        $this->user = $user;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to conversation participants
        $conversation = \App\Models\Conversation::find($this->conversationId);
        if ($conversation) {
            foreach ($conversation->participants as $participant) {
                if ($participant->id !== $this->user->id) {
                    $channels[] = new PrivateChannel("messages.user.{$participant->id}");
                }
            }
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.typing';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'is_typing' => $this->isTyping,
        ];
    }
}
