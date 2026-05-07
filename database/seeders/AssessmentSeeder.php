<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\CourseSection;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = CourseSection::all();

        if ($sections->isEmpty()) {
            $this->command->info('No course sections found. Please run CourseSectionSeeder first.');

            return;
        }

        $assessmentTypes = AssessmentType::where('is_active', true)->get();

        if ($assessmentTypes->isEmpty()) {
            $this->command->info('No assessment types found. Please run DatabaseSeeder first.');

            return;
        }

        $assessments = [
            ['title' => 'Quiz 1', 'description' => 'First quiz covering chapters 1-3'],
            ['title' => 'Quiz 2', 'description' => 'Second quiz covering chapters 4-6'],
            ['title' => 'Midterm Exam', 'description' => 'Midterm examination covering all topics'],
            ['title' => 'Assignment 1', 'description' => 'First programming assignment'],
            ['title' => 'Assignment 2', 'description' => 'Second programming assignment'],
            ['title' => 'Project', 'description' => 'Group project presentation'],
            ['title' => 'Final Exam', 'description' => 'Final comprehensive examination'],
        ];

        foreach ($sections as $section) {
            // Create 3-5 assessments per section
            $numAssessments = rand(3, 5);
            $randomAssessments = collect($assessments)->random($numAssessments);

            foreach ($randomAssessments as $index => $assessment) {
                $type = $assessmentTypes->random();

                Assessment::firstOrCreate(
                    [
                        'course_offering_id' => $section->id,
                        'title' => $assessment['title'],
                    ],
                    [
                        'assessment_type_id' => $type->id,
                        'max_grade' => 100,
                        'due_date' => now()->addDays(rand(7, 60)),
                        'description' => $assessment['description'],
                        'is_published' => true,
                    ]
                );
            }
        }

        $this->command->info('Assessments seeded successfully!');
    }
}
