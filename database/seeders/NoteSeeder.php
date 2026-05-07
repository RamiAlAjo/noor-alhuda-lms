<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
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

        $notes = [
            [
                'title' => 'Welcome to Noor Alhuda LMS',
                'content' => 'This is your learning management system. Explore the features and get started with your courses.',
                'color' => 'blue',
            ],
            [
                'title' => 'Course Planning Notes',
                'content' => 'Remember to prepare course materials before the semester starts.',
                'color' => 'yellow',
            ],
            [
                'title' => 'Student Support',
                'content' => 'Office hours: Sunday-Thursday, 9 AM - 2 PM',
                'color' => 'green',
            ],
        ];

        foreach ($notes as $note) {
            Note::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'title' => $note['title'],
                ],
                [
                    'content' => $note['content'],
                    'color' => $note['color'],
                ]
            );
        }
    }
}
