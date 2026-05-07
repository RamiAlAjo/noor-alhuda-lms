<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Major;
use Illuminate\Database\Eloquent\Factories\Factory;

class MajorFactory extends Factory
{
    protected $model = Major::class;

    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name' => fake()->randomElement([
                'Bachelor of Computer Science',
                'Bachelor of Information Technology',
                'Bachelor of Software Engineering',
                'Bachelor of Data Science',
                'Bachelor of Artificial Intelligence',
            ]),
            'name_ar' => fake()->randomElement([
                'بكالوريوس علوم الحاسب',
                'بكالوريوس تكنولوجيا المعلومات',
                'بكالوريوس هندسة البرمجيات',
                'بكالوريوس علم البيانات',
                'بكالوريوس الذكاء الاصطناعي',
            ]),
            'code' => strtoupper(fake()->unique()->lexify('????-???')),
            'description' => fake()->paragraph(),
            'description_ar' => fake()->paragraph(),
            'years_required' => fake()->randomElement([2, 3, 4, 5]),
            'credits_required' => fake()->randomElement([120, 126, 132, 140]),
            'is_active' => true,
        ];
    }
}
