<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@noorlms.com')->first();

        if (! $admin) {
            return;
        }

        $notifications = [
            [
                'type' => 'announcement',
                'title' => 'New Semester Started',
                'content' => 'Welcome to the new semester!',
            ],
            [
                'type' => 'reminder',
                'title' => 'Grade Submission Deadline',
                'content' => 'Please submit all grades by end of week.',
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'type' => $notification['type'],
                    'title' => $notification['title'],
                ],
                [
                    'content' => $notification['content'],
                    'is_read' => false,
                ]
            );
        }
    }
}
