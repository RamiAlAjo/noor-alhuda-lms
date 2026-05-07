<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'major_id' => null,
            'code' => strtoupper(fake()->unique()->lexify('???').fake()->numberBetween(100, 500)),
            'name' => fake()->randomElement([
                'Introduction to Programming',
                'Data Structures',
                'Algorithms',
                'Database Systems',
                'Operating Systems',
                'Computer Networks',
                'Software Engineering',
                'Web Development',
                'Machine Learning',
                'Artificial Intelligence',
            ]),
            'name_ar' => fake()->randomElement([
                'مقدمة في البرمجة',
                'هياكل البيانات',
                'الخوارزميات',
                'أنظمة قواعد البيانات',
                'أنظمة التشغيل',
                'شبكات الحاسب',
                'هندسة البرمجيات',
                'تطوير الويب',
                'تعلم الآلة',
                'الذكاء الاصطناعي',
            ]),
            'credits' => fake()->randomElement([2, 3, 4]),
            'description' => fake()->paragraph(),
            'description_ar' => fake()->paragraph(),
            'theory_hours' => fake()->randomElement([2, 3, 4]),
            'lab_hours' => fake()->randomElement([0, 1, 2]),
            'year_level' => fake()->randomElement([1, 2, 3, 4]),
            'semester_available' => fake()->randomElement(['first', 'second', 'summer', 'both']),
            'is_active' => true,
        ];
    }
}
