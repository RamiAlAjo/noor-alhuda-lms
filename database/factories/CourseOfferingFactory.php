<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseOffering>
 */
class CourseOfferingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = CourseOffering::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'semester_id' => Semester::factory(),
            'teacher_id' => User::factory(),
            'section_name' => $this->faker->randomElement(['A', 'B', 'C', 'D', '1', '2', '3']),
            'schedule' => $this->faker->randomElement([
                'Mon/Wed 08:00-10:00',
                'Mon/Wed 10:00-12:00',
                'Tue/Thu 08:00-10:00',
                'Tue/Thu 10:00-12:00',
                'Sun/Tue 09:00-11:00',
            ]),
            'room' => $this->faker->randomElement([
                'Room 101', 'Room 102', 'Room 103', 'Room 201', 'Room 202',
                'Lab 1', 'Lab 2', 'Lab 3', 'Lecture Hall A', 'Lecture Hall B',
            ]),
            'capacity' => $this->faker->numberBetween(15, 50),
            'enrolled_count' => 0,
            'is_active' => true,
            'is_visible_to_students' => true,
        ];
    }

    /**
     * Indicate that the offering is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the offering is hidden from students.
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible_to_students' => false,
        ]);
    }

    /**
     * Indicate that the offering has enrollments.
     */
    public function withEnrollments(int $count = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'enrolled_count' => min($count, $attributes['capacity']),
        ]);
    }

    /**
     * Create an offering for a specific course.
     */
    public function forCourse(Course $course): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => $course->id,
        ]);
    }

    /**
     * Create an offering for a specific semester.
     */
    public function forSemester(Semester $semester): static
    {
        return $this->state(fn (array $attributes) => [
            'semester_id' => $semester->id,
        ]);
    }

    /**
     * Create an offering for a specific teacher.
     */
    public function forTeacher(User $teacher): static
    {
        return $this->state(fn (array $attributes) => [
            'teacher_id' => $teacher->id,
        ]);
    }
}
