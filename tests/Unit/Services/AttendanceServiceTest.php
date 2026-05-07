<?php

namespace Tests\Unit\Services;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $service;

    private User $student;

    private Enrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $this->service = app(AttendanceService::class);
        $this->student = User::factory()->student()->create();
        $this->enrollment = Enrollment::factory()->create(['student_id' => $this->student->id]);
    }

    /** @test */
    public function it_can_record_attendance()
    {
        $record = $this->service->recordAttendance(
            $this->student->id,
            $this->enrollment->id,
            'present',
            'On time',
            1
        );

        $this->assertInstanceOf(Attendance::class, $record);
        $this->assertEquals($this->student->id, $record->student_id);
        $this->assertEquals($this->enrollment->id, $record->enrollment_id);
        $this->assertEquals('present', $record->status);
        $this->assertEquals('On time', $record->notes);
        $this->assertEquals(1, $record->marked_by);
    }

    /** @test */
    public function it_validates_attendance_status()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid attendance status');

        $this->service->recordAttendance(
            $this->student->id,
            $this->enrollment->id,
            'invalid_status'
        );
    }

    /** @test */
    public function it_sanitizes_notes_input()
    {
        $record = $this->service->recordAttendance(
            $this->student->id,
            $this->enrollment->id,
            'present',
            '<script>alert("xss")</script>On time'
        );

        $this->assertEquals('alert("xss")On time', $record->notes);
    }

    /** @test */
    public function it_can_get_student_attendance()
    {
        $this->service->recordAttendance($this->student->id, $this->enrollment->id, 'present');
        $this->service->recordAttendance($this->student->id, $this->enrollment->id, 'absent');

        $records = $this->service->getStudentAttendance($this->student->id, $this->enrollment->id);

        $this->assertCount(2, $records);
    }

    /** @test */
    public function it_calculates_attendance_statistics()
    {
        $this->service->recordAttendance($this->student->id, $this->enrollment->id, 'present');
        $this->service->recordAttendance($this->student->id, $this->enrollment->id, 'present');
        $this->service->recordAttendance($this->student->id, $this->enrollment->id, 'absent');
        $this->service->recordAttendance($this->student->id, $this->enrollment->id, 'late');

        $stats = $this->service->getStudentAttendanceStatistics($this->student->id, $this->enrollment->id);

        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['present']);
        $this->assertEquals(1, $stats['absent']);
        $this->assertEquals(1, $stats['late']);
        $this->assertEquals(75.0, $stats['attendance_rate']); // (2 present + 1 late) / 4 * 100
    }

    /** @test */
    public function it_returns_zero_for_no_attendance_records()
    {
        $stats = $this->service->getStudentAttendanceStatistics($this->student->id, $this->enrollment->id);

        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0, $stats['attendance_rate']);
    }

    /** @test */
    public function it_can_bulk_record_attendance()
    {
        $enrollment2 = Enrollment::factory()->create();

        $data = [
            ['student_id' => $this->student->id, 'enrollment_id' => $this->enrollment->id, 'status' => 'present'],
            ['student_id' => $this->student->id, 'enrollment_id' => $enrollment2->id, 'status' => 'absent'],
        ];

        $records = $this->service->bulkRecordAttendance($data, 1);

        $this->assertCount(2, $records);
        $this->assertEquals('present', $records->first()->status);
    }

    /** @test */
    public function it_can_get_enrollment_attendance()
    {
        $this->service->recordAttendance($this->student->id, $this->enrollment->id, 'present');

        $records = $this->service->getEnrollmentAttendance($this->enrollment->id);

        $this->assertCount(1, $records);
        $this->assertEquals($this->student->id, $records->first()->student_id);
    }
}
