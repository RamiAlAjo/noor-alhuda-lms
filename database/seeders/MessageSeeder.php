<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@noorlms.com')->first();
        $teacher = User::where('email', 'teacher@noorlms.com')->first();

        if (! $admin || ! $teacher) {
            return;
        }

        $messages = [
            [
                'sender_id' => $teacher->id,
                'receiver_id' => $admin->id,
                'subject' => 'Question about course materials',
                'body' => 'Hello, I would like to ask about updating the course materials for CS101. Can we schedule a meeting to discuss?',
                'is_read' => true,
            ],
            [
                'sender_id' => $admin->id,
                'receiver_id' => $teacher->id,
                'subject' => 'Re: Question about course materials',
                'body' => 'Sure, let us meet tomorrow at 10 AM in my office.',
                'is_read' => false,
            ],
        ];

        foreach ($messages as $message) {
            Message::firstOrCreate(
                [
                    'sender_id' => $message['sender_id'],
                    'receiver_id' => $message['receiver_id'],
                    'subject' => $message['subject'],
                ],
                [
                    'body' => $message['body'],
                    'is_read' => $message['is_read'],
                ]
            );
        }
    }
}
