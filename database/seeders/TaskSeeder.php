<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
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

        $tasks = [
            [
                'title' => 'Review course materials',
                'description' => 'Review and update all course materials for the upcoming semester',
                'priority' => 3,
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Prepare exam questions',
                'description' => 'Prepare midterm exam questions for CS101',
                'priority' => 2,
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Update student records',
                'description' => 'Update student enrollment records',
                'priority' => 1,
                'due_date' => now()->addDays(5),
            ],
        ];

        foreach ($tasks as $task) {
            Task::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'title' => $task['title'],
                ],
                [
                    'description' => $task['description'],
                    'priority' => $task['priority'],
                    'due_date' => $task['due_date'],
                    'is_completed' => false,
                ]
            );
        }
    }
}
