<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        // Broadcast to conversation participants
        if ($this->message->conversation) {
            foreach ($this->message->conversation->participants as $participant) {
                if ($participant->id !== $this->message->sender_id) {
                    $channels[] = new PrivateChannel("messages.user.{$participant->id}");
                }
            }
        } else {
            // Direct message
            $channels[] = new PrivateChannel("messages.user.{$this->message->receiver_id}");
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'message_type' => $this->message->message_type,
            'priority' => $this->message->priority,
            'has_attachments' => $this->message->hasAttachments(),
            'attachment_count' => $this->message->attachment_count,
            'created_at' => $this->message->created_at->diffForHumans(),
            'is_read' => false,
        ];
    }
}
