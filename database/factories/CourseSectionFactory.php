<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSectionFactory extends Factory
{
    protected $model = CourseSection::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'semester_id' => Semester::factory(),
            'section_name' => fake()->randomElement(['A', 'B', 'C', 'D', '1', '2', '3']),
            'capacity' => fake()->randomElement([20, 25, 30, 35, 40]),
            'enrolled_count' => 0,
            'room' => 'Room '.fake()->numberBetween(100, 500),
            'schedule' => fake()->randomElement([
                'MWF 08:00-09:30',
                'MWF 10:00-11:30',
                'TTh 11:00-12:30',
                'TTh 14:00-15:30',
            ]),
            'is_active' => true,
            'is_visible_to_students' => true,
        ];
    }
}
