<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_course_with_valid_data()
    {
        $department = Department::factory()->create();
        $faculty = Faculty::factory()->create();

        $course = Course::factory()->create([
            'code' => 'CS101',
            'name' => 'Introduction to Computer Science',
            'description' => 'Basic computer science concepts',
            'credits' => 3,
            'department_id' => $department->id,
        ]);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals('CS101', $course->code);
        $this->assertEquals('Introduction to Computer Science', $course->name);
        $this->assertEquals(3, $course->credits);
        $this->assertEquals($department->id, $course->department_id);
    }

    /** @test */
    public function it_has_majors_relationship()
    {
        $course = Course::factory()->create();
        $major = Major::factory()->create();
        $course->majors()->attach($major->id);

        $this->assertTrue($course->majors->contains($major->id));
    }

    /** @test */
    public function it_has_prerequisites_relationship()
    {
        $course = Course::factory()->create();
        $prerequisite = Course::factory()->create();

        \App\Models\CoursePrerequisite::create([
            'course_id' => $course->id,
            'prerequisite_course_id' => $prerequisite->id,
            'type' => 'required',
            'is_active' => true,
        ]);

        $this->assertTrue($course->prerequisites->contains('prerequisite_course_id', $prerequisite->id));
    }

    /** @test */
    public function it_can_check_if_course_is_active()
    {
        $activeCourse = Course::factory()->create(['is_active' => true]);
        $inactiveCourse = Course::factory()->create(['is_active' => false]);

        $this->assertTrue($activeCourse->is_active);
        $this->assertFalse($inactiveCourse->is_active);
    }
}
