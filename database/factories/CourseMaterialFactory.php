<?php

namespace Database\Factories;

use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseMaterial>
 */
class CourseMaterialFactory extends Factory
{
    protected $model = CourseMaterial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_offering_id' => CourseOffering::factory(),
            'uploaded_by' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'material_type' => $this->faker->randomElement(['lecture', 'assignment', 'resource', 'video']),
            'file_path' => $this->faker->optional()->filePath(),
            'file_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(10000, 5000000),
            'week' => $this->faker->numberBetween(1, 16),
            'is_published' => true,
        ];
    }

    /**
     * Indicate that the material is a lecture.
     */
    public function lecture(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => 'lecture',
            'file_type' => 'application/pdf',
        ]);
    }

    /**
     * Indicate that the material is a video.
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => 'video',
            'video_url' => $this->faker->url(),
        ]);
    }

    /**
     * Indicate that the material is an assignment.
     */
    public function assignment(): static
    {
        return $this->state(fn (array $attributes) => [
            'material_type' => 'assignment',
            'file_type' => 'application/pdf',
        ]);
    }
}
