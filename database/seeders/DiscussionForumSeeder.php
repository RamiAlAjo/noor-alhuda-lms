<?php

namespace Database\Seeders;

use App\Models\CourseOffering;
use App\Models\DiscussionForum;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiscussionForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::where('email', 'john.smith@noorlms.com')->first();

        if (! $teacher) {
            return;
        }

        $offering = CourseOffering::first();

        if (! $offering) {
            return;
        }

        $forums = [
            [
                'title' => 'Course Introduction',
                'title_ar' => 'مقدمة المادة',
                'description' => 'Welcome to the course! Introduce yourself here.',
                'description_ar' => 'مرحباً بكم في المادة! قدم نفسك هنا.',
            ],
            [
                'title' => 'Homework Help',
                'title_ar' => 'مساعدة الواجبات',
                'description' => 'Discuss homework questions and solutions.',
                'description_ar' => 'ناقش أسئلة الواجبات والحلول.',
            ],
            [
                'title' => 'General Discussion',
                'title_ar' => 'نقاش عام',
                'description' => 'General course-related discussions.',
                'description_ar' => 'مناقشات عامة متعلقة بالمادة.',
            ],
        ];

        foreach ($forums as $forum) {
            DiscussionForum::firstOrCreate(
                [
                    'course_offering_id' => $offering->id,
                    'title' => $forum['title'],
                ],
                [
                    'created_by' => $teacher->id,
                    'title_ar' => $forum['title_ar'],
                    'description' => $forum['description'],
                    'description_ar' => $forum['description_ar'],
                    'is_locked' => false,
                    'is_pinned' => false,
                ]
            );
        }
    }
}
