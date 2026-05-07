<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\StudentGrade;
use App\Models\User;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::role('student')->get();

        if ($students->isEmpty()) {
            $this->command->info('No students found. Please run DatabaseSeeder first.');

            return;
        }

        $assessments = Assessment::where('is_published', true)->get();

        if ($assessments->isEmpty()) {
            $this->command->info('No assessments found. Please run AssessmentSeeder first.');

            return;
        }

        foreach ($assessments as $assessment) {
            // Get enrolled students for this section
            $section = $assessment->courseSection;

            if (! $section) {
                continue;
            }

            $enrollments = $section->enrollments()->where('status', 'approved')->get();

            foreach ($enrollments as $enrollment) {
                $student = $enrollment->student;

                // Check if grade already exists
                $exists = StudentGrade::where('student_id', $student->id)
                    ->where('assessment_id', $assessment->id)
                    ->exists();

                if (! $exists) {
                    // Generate a grade between 50-100
                    $grade = rand(50, 100);

                    StudentGrade::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'assessment_id' => $assessment->id,
                        ],
                        [
                            'grade' => $grade,
                            'submitted_at' => now()->subDays(rand(1, 10)),
                            'feedback' => $grade >= 90 ? 'Excellent work!' : ($grade >= 70 ? 'Good job!' : null),
                        ]
                    );
                }
            }
        }

        $this->command->info('Student grades seeded successfully!');
    }
}
