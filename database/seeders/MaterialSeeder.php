<?php

namespace Database\Seeders;

use App\Models\CourseMaterial;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = CourseSection::all();

        if ($sections->isEmpty()) {
            $this->command->info('No course sections found. Please run CourseSectionSeeder first.');

            return;
        }

        // Get admin user for uploaded_by
        $admin = User::role('admin')->first();

        if (! $admin) {
            $this->command->info('No admin user found. Please run DatabaseSeeder first.');

            return;
        }

        $materialTypes = [
            ['material_type' => 'lecture', 'title' => 'Lecture Notes', 'description' => 'Chapter content and explanations'],
            ['material_type' => 'lecture', 'title' => 'Slides', 'description' => 'Presentation slides'],
            ['material_type' => 'resource', 'title' => 'Video Tutorial', 'description' => 'Video walkthrough of topics'],
            ['material_type' => 'resource', 'title' => 'External Resource', 'description' => 'Helpful external links'],
            ['material_type' => 'lecture', 'title' => 'Reading Material', 'description' => 'Additional reading content'],
            ['material_type' => 'assignment', 'title' => 'Exercise Sheet', 'description' => 'Practice problems and exercises'],
        ];

        $fileTypes = ['pdf', 'pptx', 'docx', 'video', 'link'];

        foreach ($sections as $section) {
            // Create 3-6 materials per section
            $numMaterials = rand(3, 6);

            for ($i = 0; $i < $numMaterials; $i++) {
                $material = $materialTypes[array_rand($materialTypes)];
                $week = $i + 1;
                $fileType = $fileTypes[array_rand($fileTypes)];

                CourseMaterial::firstOrCreate(
                    [
                        'course_offering_id' => $section->id,
                        'title' => "Week {$week}: {$material['title']}",
                    ],
                    [
                        'uploaded_by' => $admin->id,
                        'title_ar' => "الأسبوع {$week}: {$material['title']}",
                        'description' => $material['description'],
                        'file_path' => "/uploads/materials/{$section->id}/week_{$week}_{$material['material_type']}.{$fileType}",
                        'file_type' => $fileType,
                        'file_size' => rand(100, 5000) * 1024, // Random size between 100KB and 5MB
                        'material_type' => $material['material_type'],
                        'is_published' => true,
                    ]
                );
            }
        }

        $this->command->info('Course materials seeded successfully!');
    }
}
