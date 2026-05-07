<?php

namespace Database\Seeders;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
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

        $sections = CourseSection::all();

        if ($sections->isEmpty()) {
            $this->command->info('No course sections found. Please run CourseSectionSeeder first.');

            return;
        }

        $semester = Semester::where('is_active', true)->first();

        foreach ($students as $student) {
            // Enroll each student in 3-5 random sections
            $numEnrollments = rand(3, 5);
            $randomSections = $sections->random(min($numEnrollments, $sections->count()));

            foreach ($randomSections as $section) {
                // Check if already enrolled
                $exists = Enrollment::where('student_id', $student->id)
                    ->where('course_offering_id', $section->id)
                    ->where('semester_id', $semester?->id)
                    ->exists();

                if (! $exists && $section->enrolled_count < $section->capacity) {
                    Enrollment::firstOrCreate(
                        [
                            'student_id' => $student->id,
                            'course_offering_id' => $section->id,
                            'semester_id' => $semester?->id,
                        ],
                        [
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]
                    );

                    // Update section student count
                    $section->increment('enrolled_count');
                }
            }
        }

        $this->command->info('Enrollments seeded successfully!');
    }
}
