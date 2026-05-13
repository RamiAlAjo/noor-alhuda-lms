<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EnrollmentHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all course offerings with their current enrollment data
        $offerings = \App\Models\CourseOffering::with(['course', 'enrollments'])->get();

        foreach ($offerings as $offering) {
            // Count approved enrollments
            $approvedCount = $offering->enrollments->where('status', 'approved')->count();

            // Count dropped enrollments
            $droppedCount = $offering->enrollments->where('status', 'dropped')->count();

            // Create historical record for today
            \DB::table('enrollment_histories')->insert([
                'course_id' => $offering->course_id,
                'enrolled_count' => $approvedCount,
                'drop_count' => $droppedCount,
                'max_capacity' => $offering->max_students,
                'enrollment_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create some historical data for the past 6 months
            for ($i = 1; $i <= 6; $i++) {
                $historicalDate = now()->subMonths($i);

                // Generate realistic historical data (slightly less than current)
                $historicalEnrolled = max(0, $approvedCount - rand(0, 5));
                $historicalDropped = max(0, min($historicalEnrolled, rand(0, 2)));

                // Use same capacity or slightly different for historical data
                $historicalCapacity = $offering->max_students + rand(-5, 5);
                $historicalCapacity = max(10, $historicalCapacity); // Minimum capacity of 10

                \DB::table('enrollment_histories')->insert([
                    'course_id' => $offering->course_id,
                    'enrolled_count' => $historicalEnrolled,
                    'drop_count' => $historicalDropped,
                    'max_capacity' => $historicalCapacity,
                    'enrollment_date' => $historicalDate->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Enrollment history data seeded successfully!');
    }
}
