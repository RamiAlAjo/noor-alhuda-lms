<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => fake()->randomElement(['First Semester', 'Second Semester', 'Summer Semester']),
            'name_ar' => fake()->randomElement(['الفصل الدراسي الأول', 'الفصل الدراسي الثاني', 'الفصل الصيفي']),
            'start_date' => fake()->date('Y-m-d'),
            'end_date' => fake()->date('Y-m-d', '+5 months'),
            'is_current' => false,
            'is_active' => true,
        ];
    }

    public function first(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'First Semester',
            'name_ar' => 'الفصل الدراسي الأول',
        ]);
    }

    public function second(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Second Semester',
            'name_ar' => 'الفصل الدراسي الثاني',
        ]);
    }
}
