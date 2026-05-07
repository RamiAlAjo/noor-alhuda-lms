<?php

namespace Tests\Unit\Models;

use App\Models\Announcement;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_announcement_with_valid_data()
    {
        $user = User::factory()->create();
        $faculty = Faculty::factory()->create();
        $department = Department::factory()->create();
        $offering = CourseOffering::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'Important Announcement',
            'content' => 'This is a test announcement content.',
            'target_type' => 'all',
            'target_faculty_id' => $faculty->id,
            'target_department_id' => $department->id,
            'target_offering_id' => $offering->id,
            'is_published' => true,
        ];

        $announcement = Announcement::create($data);

        $this->assertDatabaseHas('announcements', $data);
        $this->assertInstanceOf(Announcement::class, $announcement);
    }

    /** @test */
    public function it_belongs_to_an_author()
    {
        $user = User::factory()->create();
        $announcement = Announcement::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $announcement->author);
        $this->assertEquals($user->id, $announcement->author->id);
    }

    /** @test */
    public function it_belongs_to_a_target_faculty()
    {
        $faculty = Faculty::factory()->create();
        $announcement = Announcement::factory()->create(['target_faculty_id' => $faculty->id]);

        $this->assertInstanceOf(Faculty::class, $announcement->targetFaculty);
        $this->assertEquals($faculty->id, $announcement->targetFaculty->id);
    }

    /** @test */
    public function it_belongs_to_a_target_department()
    {
        $department = Department::factory()->create();
        $announcement = Announcement::factory()->create(['target_department_id' => $department->id]);

        $this->assertInstanceOf(Department::class, $announcement->targetDepartment);
        $this->assertEquals($department->id, $announcement->targetDepartment->id);
    }

    /** @test */
    public function it_belongs_to_a_target_offering()
    {
        $offering = CourseOffering::factory()->create();
        $announcement = Announcement::factory()->create(['target_offering_id' => $offering->id]);

        $this->assertInstanceOf(CourseOffering::class, $announcement->targetOffering);
        $this->assertEquals($offering->id, $announcement->targetOffering->id);
    }

    /** @test */
    public function it_can_be_published()
    {
        $announcement = Announcement::factory()->create(['is_published' => true]);

        $this->assertTrue($announcement->is_published);
        $this->assertNotNull($announcement->published_at);
    }

    /** @test */
    public function it_can_be_unpublished()
    {
        $announcement = Announcement::factory()->create(['is_published' => false]);

        $this->assertFalse($announcement->is_published);
    }
}