<?php

namespace Tests\Feature;

use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function admin_can_view_enrollments_list()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/enrollments');

        $response->assertStatus(200);
        $response->assertViewIs('pages.admin.enrollments.index');
    }

    /** @test */
    public function admin_can_create_enrollment()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $courseOffering = CourseOffering::factory()->create();

        $enrollmentData = [
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
            'semester_id' => $courseOffering->semester_id,
            'enrollment_date' => now()->toDateString(),
            'status' => 'pending',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/enrollments', $enrollmentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_offering_id' => $courseOffering->id,
        ]);
    }

    /** @test */
    public function admin_can_update_enrollment()
    {
        $enrollment = Enrollment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put("/admin/enrollments/{$enrollment->id}", [
                'status' => 'approved',
                'notes' => 'Enrollment approved',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function admin_can_delete_enrollment()
    {
        $enrollment = Enrollment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/enrollments/{$enrollment->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('enrollments', [
            'id' => $enrollment->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_enrollment_management()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)
            ->get('/admin/enrollments');

        $response->assertStatus(403);
    }

    /** @test */
    public function enrollment_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/enrollments', []);

        $response->assertSessionHasErrors([
            'student_id',
            'course_offering_id',
            'enrollment_date',
            'status',
        ]);
    }

    /** @test */
    public function enrollment_creation_validates_status_values()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $courseOffering = CourseOffering::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/enrollments', [
                'student_id' => $student->id,
                'course_offering_id' => $courseOffering->id,
                'enrollment_date' => now()->toDateString(),
                'status' => 'invalid_status', // Invalid status
            ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function enrollment_creation_validates_student_exists()
    {
        $courseOffering = CourseOffering::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/enrollments', [
                'student_id' => 99999, // Non-existent student
                'course_offering_id' => $courseOffering->id,
                'enrollment_date' => now()->toDateString(),
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors(['student_id']);
    }

    /** @test */
    public function enrollment_creation_validates_course_offering_exists()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($this->admin)
            ->post('/admin/enrollments', [
                'student_id' => $student->id,
                'course_offering_id' => 99999, // Non-existent course offering
                'enrollment_date' => now()->toDateString(),
                'status' => 'pending',
            ]);

        $response->assertSessionHasErrors(['course_offering_id']);
    }
}
