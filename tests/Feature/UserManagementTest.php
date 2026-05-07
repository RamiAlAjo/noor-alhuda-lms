<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function admin_can_view_users_list()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('pages.admin.users.index');
    }

    /** @test */
    public function admin_can_create_user()
    {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/users', $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    /** @test */
    public function admin_can_update_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put("/admin/users/{$user->id}", [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@example.com',
                'role' => 'student',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'first_name' => 'Jane',
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/users/{$user->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_user_management()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)
            ->get('/admin/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function user_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/users', []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'password',
            'role',
        ]);
    }

    /** @test */
    public function user_creation_validates_unique_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($this->admin)
            ->post('/admin/users', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'existing@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'student',
            ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_creation_validates_password_confirmation()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/users', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => 'password123',
                'password_confirmation' => 'differentpassword',
                'role' => 'student',
            ]);

        $response->assertSessionHasErrors(['password']);
    }
}
