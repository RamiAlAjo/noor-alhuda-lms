<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'faculty_id' => Faculty::factory(),
            'name' => fake()->randomElement([
                'Computer Science',
                'Information Technology',
                'Mathematics',
                'Physics',
                'Chemistry',
                'Biology',
                'English',
                'Arabic',
            ]),
            'name_ar' => fake()->randomElement([
                'علوم الحاسب',
                'تكنولوجيا المعلومات',
                'الرياضيات',
                'الفيزياء',
                'الكيمياء',
                'الأحياء',
                'الإنجليزية',
                'العربية',
            ]),
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'head_name' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
