<?php

namespace Tests\Unit\Models;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_message_with_valid_data()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $data = [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'subject' => 'Test Message Subject',
            'content' => 'This is a test message content.',
            'is_read' => false,
        ];

        $message = Message::create($data);

        $this->assertDatabaseHas('messages', $data);
        $this->assertInstanceOf(Message::class, $message);
    }

    /** @test */
    public function it_belongs_to_a_sender()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $message = Message::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $this->assertInstanceOf(User::class, $message->sender);
        $this->assertEquals($sender->id, $message->sender->id);
    }

    /** @test */
    public function it_belongs_to_a_receiver()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $message = Message::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $this->assertInstanceOf(User::class, $message->receiver);
        $this->assertEquals($receiver->id, $message->receiver->id);
    }

    /** @test */
    public function it_can_be_marked_as_read()
    {
        $message = Message::factory()->create(['is_read' => false]);

        $message->update(['is_read' => true, 'read_at' => now()]);

        $this->assertTrue($message->fresh()->is_read);
        $this->assertNotNull($message->fresh()->read_at);
    }

    /** @test */
    public function it_can_filter_unread_messages()
    {
        Message::factory()->count(3)->create(['is_read' => false]);
        Message::factory()->count(2)->create(['is_read' => true]);

        $unreadMessages = Message::unread()->get();

        $this->assertCount(3, $unreadMessages);
        $unreadMessages->each(function ($message) {
            $this->assertFalse($message->is_read);
        });
    }

    /** @test */
    public function it_can_filter_inbox_messages_for_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Messages received by user1
        Message::factory()->count(2)->create(['receiver_id' => $user1->id]);
        // Messages sent by user1
        Message::factory()->count(1)->create(['sender_id' => $user1->id, 'receiver_id' => $user2->id]);
        // Messages for user2
        Message::factory()->count(1)->create(['receiver_id' => $user2->id]);

        $inboxMessages = Message::inbox($user1->id)->get();

        $this->assertCount(2, $inboxMessages);
        $inboxMessages->each(function ($message) use ($user1) {
            $this->assertEquals($user1->id, $message->receiver_id);
        });
    }

    /** @test */
    public function it_can_filter_sent_messages_for_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Messages sent by user1
        Message::factory()->count(2)->create(['sender_id' => $user1->id, 'receiver_id' => $user2->id]);
        // Messages received by user1
        Message::factory()->count(1)->create(['sender_id' => $user2->id, 'receiver_id' => $user1->id]);
        // Messages for user2
        Message::factory()->count(1)->create(['sender_id' => $user3->id, 'receiver_id' => $user2->id]);

        $sentMessages = Message::sent($user1->id)->get();

        $this->assertCount(2, $sentMessages);
        $sentMessages->each(function ($message) use ($user1) {
            $this->assertEquals($user1->id, $message->sender->id);
        });
    }
}