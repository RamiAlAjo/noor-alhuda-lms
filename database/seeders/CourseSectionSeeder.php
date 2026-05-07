<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSectionSeeder extends Seeder
{
    public function run(): void
    {
        $semester = Semester::where('is_active', true)->first();

        if (! $semester) {
            $this->command->info('No active semester found. Please run DatabaseSeeder first.');

            return;
        }

        $courses = Course::where('is_active', true)->get();
        $teachers = User::role('teacher')->get();

        if ($teachers->isEmpty()) {
            $this->command->info('No teachers found. Please run DatabaseSeeder first.');

            return;
        }

        $sections = [
            ['section_name' => 'A', 'schedule' => 'Sun-Tue 08:00-10:00', 'room' => 'Room 101', 'capacity' => 30],
            ['section_name' => 'B', 'schedule' => 'Mon-Wed 10:00-12:00', 'room' => 'Room 102', 'capacity' => 30],
            ['section_name' => 'C', 'schedule' => 'Tue-Thu 14:00-16:00', 'room' => 'Room 103', 'capacity' => 25],
        ];

        $sectionIndex = 0;
        foreach ($courses as $course) {
            $numSections = rand(1, 2);

            for ($i = 0; $i < $numSections; $i++) {
                $sectionData = $sections[$sectionIndex % count($sections)];
                $teacher = $teachers->random();

                CourseOffering::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'semester_id' => $semester->id,
                        'section_name' => $sectionData['section_name'],
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'schedule' => $sectionData['schedule'],
                        'room' => $sectionData['room'],
                        'capacity' => $sectionData['capacity'],
                        'enrolled_count' => 0,
                        'is_active' => true,
                        'is_visible_to_students' => true,
                    ]
                );

                $sectionIndex++;
            }
        }

        $this->command->info('Course offerings seeded successfully!');
    }
}
