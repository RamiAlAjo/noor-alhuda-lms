<?php

namespace Database\Seeders;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
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

        $reminders = [
            [
                'title' => 'Submit final grades',
                'description' => 'Deadline for submitting all final grades for the semester',
                'remind_at' => now()->addDays(14),
            ],
            [
                'title' => 'Faculty meeting',
                'description' => 'Monthly faculty meeting to discuss academic matters',
                'remind_at' => now()->addDays(3),
            ],
        ];

        foreach ($reminders as $reminder) {
            Reminder::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'title' => $reminder['title'],
                ],
                [
                    'description' => $reminder['description'],
                    'remind_at' => $reminder['remind_at'],
                    'is_read' => false,
                ]
            );
        }
    }
}
