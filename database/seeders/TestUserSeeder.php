<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create student role if not exists
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

        // Create a test student
        $student = User::firstOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password'),
            ]
        );
        $student->assignRole($studentRole);

        // Create a test teacher
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@test.com'],
            [
                'name' => 'Test Teacher',
                'password' => Hash::make('password'),
            ]
        );
        $teacher->assignRole($teacherRole);

        $this->command->info('Test users created successfully!');
        $this->command->info('Student: student@test.com / password');
        $this->command->info('Teacher: teacher@test.com / password');
    }
}
