<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
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
    public function admin_can_view_courses_list()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/courses');

        $response->assertStatus(200);
        $response->assertViewIs('pages.admin.courses.index');
    }

    /** @test */
    public function admin_can_create_course()
    {
        $department = Department::factory()->create();

        $courseData = [
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
            'description' => 'An introductory course to computer science',
            'credits' => 3,
            'department_id' => $department->id,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/courses', $courseData);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
        ]);
    }

    /** @test */
    public function admin_can_update_course()
    {
        $course = Course::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put("/admin/courses/{$course->id}", [
                'code' => 'CS102',
                'name' => 'Advanced Computer Science',
                'description' => 'An advanced course to computer science',
                'credits' => 4,
                'department_id' => $course->department_id,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'code' => 'CS102',
            'name' => 'Advanced Computer Science',
        ]);
    }

    /** @test */
    public function admin_can_delete_course()
    {
        $course = Course::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/courses/{$course->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('courses', [
            'id' => $course->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_course_management()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)
            ->get('/admin/courses');

        $response->assertStatus(403);
    }

    /** @test */
    public function course_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/courses', []);

        $response->assertSessionHasErrors([
            'code',
            'name',
            'credits',
            'department_id',
        ]);
    }

    /** @test */
    public function course_creation_validates_unique_code()
    {
        Course::factory()->create(['code' => 'CS101']);

        $department = Department::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/courses', [
                'code' => 'CS101',
                'name' => 'Introduction to Computer Science',
                'credits' => 3,
                'department_id' => $department->id,
            ]);

        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function course_creation_validates_credits_range()
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post('/admin/courses', [
                'code' => 'CS101',
                'name' => 'Introduction to Computer Science',
                'credits' => 15, // Invalid: max is 10
                'department_id' => $department->id,
            ]);

        $response->assertSessionHasErrors(['credits']);
    }
}
