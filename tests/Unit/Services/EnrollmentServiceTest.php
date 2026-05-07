<?php

namespace Tests\Unit\Services;

use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private EnrollmentService $service;

    private User $student;

    private CourseOffering $offering;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EnrollmentService::class);
        $this->student = User::factory()->student()->create();
        $this->offering = CourseOffering::factory()->create([
            'max_students' => 30,
            'current_students' => 0,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_can_enroll_a_student()
    {
        $enrollment = $this->service->enrollStudent(
            $this->student->id,
            $this->offering->id,
            null,
            'Test enrollment'
        );

        $this->assertInstanceOf(Enrollment::class, $enrollment);
        $this->assertEquals($this->student->id, $enrollment->student_id);
        $this->assertEquals($this->offering->id, $enrollment->course_offering_id);
        $this->assertEquals(Enrollment::STATUS_PENDING, $enrollment->status);
        $this->assertDatabaseHas('course_offerings', [
            'id' => $this->offering->id,
            'current_students' => 1,
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_enrollment()
    {
        $this->service->enrollStudent($this->student->id, $this->offering->id);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Student is already enrolled in this course offering.');

        $this->service->enrollStudent($this->student->id, $this->offering->id);
    }

    /** @test */
    public function it_prevents_enrollment_when_course_is_full()
    {
        $this->offering->update([
            'max_students' => 1,
            'current_students' => 1,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Course offering has reached maximum capacity.');

        $this->service->enrollStudent($this->student->id, $this->offering->id);
    }

    /** @test */
    public function it_prevents_enrollment_for_inactive_course()
    {
        $this->offering->update(['is_active' => false]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Course offering is not active for enrollment.');

        $this->service->enrollStudent($this->student->id, $this->offering->id);
    }

    /** @test */
    public function it_can_approve_enrollment()
    {
        $enrollment = $this->service->enrollStudent($this->student->id, $this->offering->id);

        $approved = $this->service->approveEnrollment($enrollment->id, 1);

        $this->assertEquals(Enrollment::STATUS_APPROVED, $approved->status);
        $this->assertNotNull($approved->approved_at);
        $this->assertEquals(1, $approved->approved_by);
    }

    /** @test */
    public function it_cannot_approve_non_pending_enrollment()
    {
        $enrollment = $this->service->enrollStudent($this->student->id, $this->offering->id);
        $this->service->approveEnrollment($enrollment->id, 1);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Only pending enrollments can be approved.');

        $this->service->approveEnrollment($enrollment->id, 1);
    }

    /** @test */
    public function it_can_drop_enrollment()
    {
        $enrollment = $this->service->enrollStudent($this->student->id, $this->offering->id);

        $dropped = $this->service->dropEnrollment($enrollment->id, 'Student request');

        $this->assertEquals(Enrollment::STATUS_DROPPED, $dropped->status);
        $this->assertNotNull($dropped->dropped_at);
        $this->assertDatabaseHas('course_offerings', [
            'id' => $this->offering->id,
            'current_students' => 0,
        ]);
    }

    /** @test */
    public function it_sanitizes_notes_input()
    {
        $enrollment = $this->service->enrollStudent(
            $this->student->id,
            $this->offering->id,
            null,
            '<script>alert("xss")</script>Test notes'
        );

        $this->assertEquals('alert("xss")Test notes', $enrollment->notes);
    }

    /** @test */
    public function it_can_get_student_enrollments()
    {
        $this->service->enrollStudent($this->student->id, $this->offering->id);

        $enrollments = $this->service->getStudentEnrollments($this->student->id);

        $this->assertCount(1, $enrollments);
        $this->assertEquals($this->offering->id, $enrollments->first()->course_offering_id);
    }

    /** @test */
    public function it_can_check_if_student_is_enrolled()
    {
        $this->assertFalse($this->service->isStudentEnrolled($this->student->id, $this->offering->id));

        $this->service->enrollStudent($this->student->id, $this->offering->id);

        $this->assertTrue($this->service->isStudentEnrolled($this->student->id, $this->offering->id));
    }

    /** @test */
    public function it_can_get_enrollment_statistics()
    {
        $this->service->enrollStudent($this->student->id, $this->offering->id);

        $stats = $this->service->getEnrollmentStatistics($this->offering->id);

        $this->assertEquals(1, $stats['total']);
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(0, $stats['approved']);
    }
}
