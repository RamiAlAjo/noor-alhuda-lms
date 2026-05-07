<?php

use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseMaterialTest extends TestCase
{
    use RefreshDatabase;

    private CourseOffering $offering;
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $this->teacher = User::factory()->teacher()->create();
        $this->offering = CourseOffering::factory()->create();
    }

    /** @test */
    public function it_can_create_a_course_material()
    {
        $materialData = [
            'course_offering_id' => $this->offering->id,
            'uploaded_by' => $this->teacher->id,
            'title' => 'Introduction Lecture',
            'description' => 'Basic introduction to the course',
            'file_path' => 'materials/intro.pdf',
            'file_type' => 'application/pdf',
            'file_size' => 1024000,
            'material_type' => 'lecture',
            'week' => 1,
            'is_published' => true,
        ];

        $material = CourseMaterial::create($materialData);

        $this->assertInstanceOf(CourseMaterial::class, $material);
        $this->assertEquals('Introduction Lecture', $material->title);
        $this->assertEquals($this->offering->id, $material->course_offering_id);
        $this->assertEquals($this->teacher->id, $material->uploaded_by);
        $this->assertTrue($material->is_published);
    }

    /** @test */
    public function it_belongs_to_an_offering()
    {
        $material = CourseMaterial::factory()->create([
            'course_offering_id' => $this->offering->id,
            'uploaded_by' => $this->teacher->id,
        ]);

        $this->assertInstanceOf(CourseOffering::class, $material->offering);
        $this->assertEquals($this->offering->id, $material->offering->id);
    }

    /** @test */
    public function it_belongs_to_an_uploader()
    {
        $material = CourseMaterial::factory()->create([
            'uploaded_by' => $this->teacher->id,
        ]);

        $this->assertInstanceOf(User::class, $material->uploadedBy);
        $this->assertEquals($this->teacher->id, $material->uploadedBy->id);
    }

    /** @test */
    public function it_can_extract_youtube_video_id()
    {
        $material = CourseMaterial::factory()->create([
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->assertEquals('dQw4w9WgXcQ', $material->getYouTubeVideoId());
    }

    /** @test */
    public function it_can_generate_youtube_embed_url()
    {
        $material = CourseMaterial::factory()->create([
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
        ]);

        $this->assertEquals('https://www.youtube.com/embed/dQw4w9WgXcQ', $material->getYouTubeEmbedUrl());
    }

    /** @test */
    public function it_can_check_if_has_youtube_video()
    {
        $materialWithVideo = CourseMaterial::factory()->create([
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $materialWithoutVideo = CourseMaterial::factory()->create([
            'video_url' => null,
        ]);

        $this->assertTrue($materialWithVideo->hasYouTubeVideo());
        $this->assertFalse($materialWithoutVideo->hasYouTubeVideo());
    }

    /** @test */
    public function it_can_be_grouped_by_week()
    {
        CourseMaterial::factory()->create([
            'course_offering_id' => $this->offering->id,
            'week' => 1,
        ]);

        CourseMaterial::factory()->create([
            'course_offering_id' => $this->offering->id,
            'week' => 1,
        ]);

        CourseMaterial::factory()->create([
            'course_offering_id' => $this->offering->id,
            'week' => 2,
        ]);

        $materialsByWeek = $this->offering->materials()->get()->groupBy('week');

        $this->assertCount(2, $materialsByWeek[1]);
        $this->assertCount(1, $materialsByWeek[2]);
    }
}