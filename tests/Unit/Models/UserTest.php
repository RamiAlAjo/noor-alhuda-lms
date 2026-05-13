<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
    }

    /** @test */
    public function it_can_create_a_user_with_valid_data()
    {
        $user = User::factory()->student()->create([
            'email' => 'john@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->profile->first_name);
        $this->assertEquals('Doe', $user->profile->last_name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue($user->isStudent());
    }

    /** @test */
    public function it_requires_first_name_last_name_and_email()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([]);
    }

    /** @test */
    public function it_has_full_name_attribute()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->assertEquals($user->profile->first_name.' '.$user->profile->last_name, $user->full_name);
    }

    /** @test */
    public function it_has_profile_relationship()
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(UserProfile::class, $user->profile);
        $this->assertEquals($profile->id, $user->profile->id);
    }

    /** @test */
    public function it_can_check_if_user_is_student()
    {
        $student = User::factory()->student()->create();
        $teacher = User::factory()->teacher()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($student->isStudent());
        $this->assertFalse($teacher->isStudent());
        $this->assertFalse($admin->isStudent());
    }

    /** @test */
    public function it_can_check_if_user_is_teacher()
    {
        $student = User::factory()->student()->create();
        $teacher = User::factory()->teacher()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($teacher->isTeacher());
        $this->assertFalse($student->isTeacher());
        $this->assertFalse($admin->isTeacher());
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        $student = User::factory()->student()->create();
        $teacher = User::factory()->teacher()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($student->isAdmin());
        $this->assertFalse($teacher->isAdmin());
    }
}
