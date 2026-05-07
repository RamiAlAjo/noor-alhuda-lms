<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Fee;
use App\Models\Major;
use App\Models\Semester;
use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
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

        $academicYear = AcademicYear::where('is_current', true)->first();

        if (! $academicYear) {
            $this->command->info('No current academic year found. Please run DatabaseSeeder first.');

            return;
        }

        $semester = Semester::where('is_active', true)->first();
        $majors = Major::all();

        if ($majors->isEmpty()) {
            $this->command->info('No majors found. Please run DatabaseSeeder first.');

            return;
        }

        $feeTypes = [
            ['name' => 'Tuition Fee', 'name_ar' => 'رسوم tuition', 'fee_type' => 'tuition', 'amount' => 500],
            ['name' => 'Registration Fee', 'name_ar' => 'رسوم التسجيل', 'fee_type' => 'registration', 'amount' => 100],
            ['name' => 'Lab Fee', 'name_ar' => 'رسوم المختبر', 'fee_type' => 'lab', 'amount' => 150],
            ['name' => 'Library Fee', 'name_ar' => 'رسوم المكتبة', 'fee_type' => 'library', 'amount' => 50],
            ['name' => 'Sports Fee', 'name_ar' => 'رسوم الرياضية', 'fee_type' => 'sports', 'amount' => 75],
            ['name' => 'Exam Fee', 'name_ar' => 'رسوم الامتحان', 'fee_type' => 'exam', 'amount' => 100],
        ];

        // Create base fees for majors
        foreach ($majors as $major) {
            foreach ($feeTypes as $feeData) {
                Fee::firstOrCreate(
                    [
                        'semester_id' => $semester?->id,
                        'name' => $feeData['name'],
                        'fee_type' => $feeData['fee_type'],
                        'major_id' => $major->id,
                        'academic_year' => $academicYear->start_year,
                    ],
                    [
                        'name_ar' => $feeData['name_ar'],
                        'amount' => $feeData['amount'],
                        'target' => 'student',
                        'due_date' => now()->addDays(rand(30, 90)),
                        'is_active' => true,
                        'description' => $feeData['name'].' for '.$major->name,
                    ]
                );
            }
        }

        // Assign fees to students
        $fees = Fee::where('academic_year', $academicYear->start_year)->get();

        foreach ($students as $student) {
            // Get student major from enrollment or use first major
            $major = $majors->random();

            // Assign random fees to each student
            $randomFees = $fees->where('major_id', $major->id)->random(min(3, $fees->count()));

            foreach ($randomFees as $fee) {
                StudentFee::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'fee_id' => $fee->id,
                    ],
                    [
                        'amount' => $fee->amount,
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                        'due_date' => $fee->due_date,
                    ]
                );
            }
        }

        $this->command->info('Fees seeded successfully!');
    }
}
