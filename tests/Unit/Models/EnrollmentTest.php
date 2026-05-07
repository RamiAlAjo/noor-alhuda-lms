<?php

namespace Tests\Unit\Models;

use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_enrollment_with_valid_data()
    {
        $user = User::factory()->create();
        $courseOffering = CourseOffering::factory()->create();
        $semester = Semester::factory()->create();

        $enrollmentData = [
            'student_id' => $user->id,
            'course_offering_id' => $courseOffering->id,
            'semester_id' => $semester->id,
            'status' => 'approved',
            'enrolled_at' => now(),
            'approved_at' => now(),
            'final_grade' => null,
        ];

        $enrollment = Enrollment::create($enrollmentData);

        $this->assertInstanceOf(Enrollment::class, $enrollment);
        $this->assertEquals($user->id, $enrollment->student_id);
        $this->assertEquals($courseOffering->id, $enrollment->course_offering_id);
        $this->assertEquals('approved', $enrollment->status);
        $this->assertNotNull($enrollment->approved_at);
    }

    /** @test */
    public function it_requires_user_id_course_offering_id_and_status()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Enrollment::create([]);
    }

    /** @test */
    public function it_has_user_relationship()
    {
        $user = User::factory()->create();
        $enrollment = Enrollment::factory()->create(['student_id' => $user->id]);

        $this->assertInstanceOf(User::class, $enrollment->student);
        $this->assertEquals($user->id, $enrollment->student->id);
    }

    /** @test */
    public function it_has_course_offering_relationship()
    {
        $courseOffering = CourseOffering::factory()->create();
        $enrollment = Enrollment::factory()->create(['course_offering_id' => $courseOffering->id]);

        $this->assertInstanceOf(CourseOffering::class, $enrollment->courseOffering);
        $this->assertEquals($courseOffering->id, $enrollment->courseOffering->id);
    }

    /** @test */
    public function it_can_check_if_enrollment_is_active()
    {
        $activeEnrollment = Enrollment::factory()->create(['status' => 'approved']);
        $completedEnrollment = Enrollment::factory()->create(['status' => 'completed']);
        $droppedEnrollment = Enrollment::factory()->create(['status' => 'dropped']);

        $this->assertTrue($activeEnrollment->isActive());
        $this->assertFalse($completedEnrollment->isActive());
        $this->assertFalse($droppedEnrollment->isActive());
    }

    /** @test */
    public function it_can_check_if_enrollment_is_completed()
    {
        $completedEnrollment = Enrollment::factory()->create(['status' => 'completed']);
        $activeEnrollment = Enrollment::factory()->create(['status' => 'approved']);

        $this->assertTrue($completedEnrollment->isCompleted());
        $this->assertFalse($activeEnrollment->isCompleted());
    }

    /** @test */
    public function it_can_check_if_enrollment_is_dropped()
    {
        $droppedEnrollment = Enrollment::factory()->create(['status' => 'dropped']);
        $activeEnrollment = Enrollment::factory()->create(['status' => 'approved']);

        $this->assertTrue($droppedEnrollment->isDropped());
        $this->assertFalse($activeEnrollment->isDropped());
    }

    /** @test */
    public function it_can_get_enrollment_duration()
    {
        $enrollment = Enrollment::factory()->create([
            'approved_at' => now()->subDays(30),
            'completed_at' => now(),
        ]);

        $this->assertEquals(30, $enrollment->getDurationInDays());
    }

    /** @test */
    public function it_can_calculate_grade_points()
    {
        $enrollment = Enrollment::factory()->create([
            'final_grade' => 'A',
            'course_offering_id' => CourseOffering::factory()->create(['credits' => 3])->id,
        ]);

        $this->assertEquals(12, $enrollment->getGradePoints());

        $enrollment->final_grade = 'B+';
        $enrollment->save();
        $this->assertEquals(10.5, $enrollment->getGradePoints());
    }

    /** @test */
    public function it_can_calculate_gpa()
    {
        $enrollment1 = Enrollment::factory()->create([
            'final_grade' => 'A',
            'course_offering_id' => CourseOffering::factory()->create(['credits' => 3])->id,
        ]);

        $enrollment2 = Enrollment::factory()->create([
            'final_grade' => 'B',
            'course_offering_id' => CourseOffering::factory()->create(['credits' => 4])->id,
        ]);

        $this->assertEquals(3.4, Enrollment::calculateGPA([$enrollment1, $enrollment2]));
    }

    /** @test */
    public function it_can_check_if_course_is_completed_with_passing_grade()
    {
        $enrollment = Enrollment::factory()->create([
            'status' => 'completed',
            'final_grade' => 'C+',
        ]);

        $this->assertTrue($enrollment->hasPassed());

        $enrollment->final_grade = 'D';
        $enrollment->save();
        $this->assertFalse($enrollment->hasPassed());
    }

    /** @test */
    public function it_can_get_enrollment_status_text()
    {
        $enrollment1 = Enrollment::factory()->create(['status' => 'approved']);
        $enrollment2 = Enrollment::factory()->create(['status' => 'completed']);
        $enrollment3 = Enrollment::factory()->create(['status' => 'dropped']);

        $this->assertEquals('Enrolled', $enrollment1->getStatusText());
        $this->assertEquals('Completed', $enrollment2->getStatusText());
        $this->assertEquals('Dropped', $enrollment3->getStatusText());
    }

    /** @test */
    public function it_can_get_course_progress()
    {
        $enrollment = Enrollment::factory()->create([
            'status' => 'approved',
            'course_offering_id' => CourseOffering::factory()->create([
                'total_weeks' => 16,
                'current_week' => 8,
            ])->id,
        ]);

        $this->assertEquals(50, $enrollment->getProgressPercentage());
    }

    /** @test */
    public function it_can_check_if_enrollment_is_eligible_for_completion()
    {
        $enrollment = Enrollment::factory()->create([
            'status' => 'approved',
            'course_offering_id' => CourseOffering::factory()->create([
                'total_weeks' => 16,
                'current_week' => 16,
            ])->id,
        ]);

        $this->assertTrue($enrollment->isEligibleForCompletion());

        $enrollment->courseOffering->total_weeks = 16;
        $enrollment->courseOffering->current_week = 15;
        $enrollment->courseOffering->save();

        $this->assertFalse($enrollment->isEligibleForCompletion());
    }

    /** @test */
    public function it_can_get_enrollment_summary()
    {
        $enrollment = Enrollment::factory()->create([
            'status' => 'completed',
            'final_grade' => 'A-',
            'approved_at' => now()->subMonths(4),
            'completed_at' => now()->subMonths(1),
            'course_offering_id' => CourseOffering::factory()->create([
                'course_id' => Course::factory()->create([
                    'code' => 'CS101',
                    'name' => 'Intro to CS',
                ])->id,
                'academic_year' => '2024/2025',
                'semester' => '1',
            ])->id,
        ]);

        $summary = $enrollment->getSummary();

        $this->assertEquals('CS101 - Intro to CS', $summary['course']);
        $this->assertEquals('2024/2025 Fall', $summary['term']);
        $this->assertEquals('A-', $summary['grade']);
        $this->assertEquals('Completed', $summary['status']);
    }
}
