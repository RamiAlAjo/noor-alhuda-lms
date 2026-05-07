<?php

namespace Database\Seeders;

use App\Models\CourseFeedback;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = User::role('student')->first();

        if (! $student) {
            return;
        }

        $offering = CourseOffering::first();

        if (! $offering) {
            return;
        }

        $feedbacks = [
            [
                'overall_rating' => 5,
                'content_quality' => 5,
                'instructor_knowledge' => 5,
                'instructor_communication' => 4,
                'course_organization' => 4,
                'materials_quality' => 5,
                'workload_appropriateness' => 4,
                'strengths' => 'Excellent instructor and materials',
                'improvements' => 'More practical exercises',
            ],
            [
                'overall_rating' => 4,
                'content_quality' => 4,
                'instructor_knowledge' => 5,
                'instructor_communication' => 4,
                'course_organization' => 4,
                'materials_quality' => 4,
                'workload_appropriateness' => 5,
                'strengths' => 'Good course content',
                'improvements' => 'Better organization',
            ],
        ];

        foreach ($feedbacks as $feedback) {
            CourseFeedback::firstOrCreate(
                [
                    'course_offering_id' => $offering->id,
                    'student_id' => $student->id,
                ],
                [
                    'overall_rating' => $feedback['overall_rating'],
                    'content_quality' => $feedback['content_quality'],
                    'instructor_knowledge' => $feedback['instructor_knowledge'],
                    'instructor_communication' => $feedback['instructor_communication'],
                    'course_organization' => $feedback['course_organization'],
                    'materials_quality' => $feedback['materials_quality'],
                    'workload_appropriateness' => $feedback['workload_appropriateness'],
                    'strengths' => $feedback['strengths'],
                    'improvements' => $feedback['improvements'],
                    'is_submitted' => true,
                ]
            );
        }
    }
}
