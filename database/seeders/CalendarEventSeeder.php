<?php

namespace Database\Seeders;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class CalendarEventSeeder extends Seeder
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

        $events = [
            [
                'title' => 'Fall Semester Start',
                'description' => 'The fall semester begins today. Welcome back to all students!',
                'start_date' => now()->addDays(7),
                'event_type' => 'class',
            ],
            [
                'title' => 'Midterm Exams',
                'description' => 'Midterm examination period for all courses.',
                'start_date' => now()->addDays(30),
                'event_type' => 'exam',
            ],
            [
                'title' => 'Student Advisory Meeting',
                'description' => 'Advisory meeting for all students to discuss academic progress.',
                'start_date' => now()->addDays(14),
                'event_type' => 'meeting',
            ],
            [
                'title' => 'Assignment Deadline - CS101',
                'description' => 'First programming assignment due for CS101 students.',
                'start_date' => now()->addDays(21),
                'event_type' => 'assignment',
            ],
        ];

        foreach ($events as $event) {
            CalendarEvent::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'title' => $event['title'],
                ],
                [
                    'description' => $event['description'],
                    'start_date' => $event['start_date'],
                    'end_date' => $event['start_date']->copy()->addHours(2),
                    'event_type' => $event['event_type'],
                ]
            );
        }
    }
}
