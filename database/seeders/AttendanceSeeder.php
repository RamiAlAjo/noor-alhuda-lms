<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
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

        $sections = CourseOffering::all();

        if ($sections->isEmpty()) {
            $this->command->info('No course sections found. Please run CourseSectionSeeder first.');

            return;
        }

        $statuses = ['present', 'present', 'present', 'present', 'absent', 'excused', 'late'];

        foreach ($sections as $section) {
            // Get enrolled students for this section
            $enrolledStudents = $section->enrollments()->with('student')->get();

            if ($enrolledStudents->isEmpty()) {
                continue;
            }

            // Create attendance for the last 15 sessions
            for ($i = 0; $i < 15; $i++) {
                $date = Carbon::now()->subDays($i * 7); // Weekly sessions

                foreach ($enrolledStudents as $enrollment) {
                    $student = $enrollment->student;

                    // Check if attendance already exists
                    $exists = AttendanceRecord::where('student_id', $student->id)
                        ->where('course_offering_id', $section->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->exists();

                    if (! $exists) {
                        $status = $statuses[array_rand($statuses)];

                        AttendanceRecord::firstOrCreate(
                            [
                                'student_id' => $student->id,
                                'course_offering_id' => $section->id,
                                'date' => $date->format('Y-m-d'),
                            ],
                            [
                                'status' => $status,
                                'notes' => $status === 'excused' ? 'Doctor excuse' : null,
                                'marked_at' => now(),
                            ]
                        );
                    }
                }
            }
        }

        $this->command->info('Attendance records seeded successfully!');
    }
}
