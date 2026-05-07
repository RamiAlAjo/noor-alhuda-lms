<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AssessmentType;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Semester;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create permissions
        $this->createPermissions();

        // Create roles
        $this->createRoles();

        // Create admin user
        $this->createAdminUser();

        // Create academic structure
        $this->createAcademicStructure();

        // Create assessment types
        $this->createAssessmentTypes();

        // Create sample teachers
        $this->createSampleTeachers();

        // Create sample students
        $this->createSampleStudents();

        // Call additional seeders
        $this->call([
            CourseSectionSeeder::class,
            EnrollmentSeeder::class,
            AssessmentSeeder::class,
            AttendanceSeeder::class,
            GradeSeeder::class,
            FeeSeeder::class,
            MaterialSeeder::class,
            AnnouncementSeeder::class,
            CalendarEventSeeder::class,
            TaskSeeder::class,
            NoteSeeder::class,
            ReminderSeeder::class,
            MessageSeeder::class,
            DiscussionForumSeeder::class,
            CourseFeedbackSeeder::class,
            NotificationSeeder::class,
            PaymentSeeder::class,
        ]);
    }

    /**
     * Create permissions for the LMS.
     */
    protected function createPermissions(): void
    {
        $permissions = [
            // User Management
            'users.view', 'users.create', 'users.edit', 'users.delete',

            // Role Management
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',

            // Academic Management
            'faculties.view', 'faculties.create', 'faculties.edit', 'faculties.delete',
            'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
            'majors.view', 'majors.create', 'majors.edit', 'majors.delete',
            'courses.view', 'courses.create', 'courses.edit', 'courses.delete',
            'sections.view', 'sections.create', 'sections.edit', 'sections.delete',

            // Enrollment Management
            'enrollments.view', 'enrollments.approve', 'enrollments.reject',

            // Fee Management
            'fees.view', 'fees.create', 'fees.edit', 'fees.delete',
            'payments.view', 'payments.approve',

            // Attendance Management
            'attendance.view', 'attendance.mark',

            // Assessment Management
            'assessments.view', 'assessments.create', 'assessments.edit', 'assessments.delete',
            'grades.view', 'grades.enter',

            // Announcements
            'announcements.view', 'announcements.create', 'announcements.edit', 'announcements.delete',

            // Reports
            'reports.view', 'reports.export',

            // Settings
            'settings.view', 'settings.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    /**
     * Create roles and assign permissions.
     */
    protected function createRoles(): void
    {
        // Admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        // Teacher role
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacherRole->givePermissionTo([
            'courses.view', 'sections.view', 'sections.create', 'sections.edit',
            'attendance.view', 'attendance.mark',
            'assessments.view', 'assessments.create', 'assessments.edit', 'assessments.delete',
            'grades.view', 'grades.enter',
            'announcements.view', 'announcements.create', 'announcements.edit', 'announcements.delete',
            'reports.view',
        ]);

        // Student role
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $studentRole->givePermissionTo([
            'courses.view', 'sections.view', 'enrollments.view',
            'attendance.view',
            'assessments.view',
            'grades.view',
            'announcements.view',
            'fees.view', 'payments.view',
            'reports.view',
        ]);
    }

    /**
     * Create admin user.
     */
    protected function createAdminUser(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@noorlms.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        // Create profile
        UserProfile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'user_id_unique' => 'ADM'.date('Y').'00001',
            ]
        );

        // Create settings
        UserSetting::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'theme' => 'light',
                'locale' => 'en',
            ]
        );
    }

    /**
     * Create academic structure.
     */
    protected function createAcademicStructure(): void
    {
        // Create academic year
        $academicYear = AcademicYear::firstOrCreate(
            ['start_year' => 2026],
            [
                'name' => '2026-2027',
                'end_year' => 2027,
                'is_current' => true,
                'is_active' => true,
            ]
        );

        // Create semesters
        $firstSemester = Semester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'name' => 'First Semester'],
            [
                'name_ar' => 'الفصل الدراسي الأول',
                'start_date' => '2026-09-01',
                'end_date' => '2027-01-15',
                'is_current' => true,
                'is_active' => true,
            ]
        );

        // Create faculty
        $faculty = Faculty::firstOrCreate(
            ['code' => 'SCI'],
            [
                'name' => 'Faculty of Science',
                'name_ar' => 'كلية العلوم',
                'description' => 'Faculty of Science at Noor University',
                'dean_name' => 'Dr. Ahmed Hassan',
                'email' => 'science@noorlms.com',
                'phone' => '+96265555555',
                'is_active' => true,
            ]
        );

        // Create department
        $department = Department::firstOrCreate(
            ['faculty_id' => $faculty->id, 'code' => 'CS'],
            [
                'name' => 'Computer Science',
                'name_ar' => 'علوم الحاسب',
                'head_name' => 'Dr. Mohammed Ali',
                'email' => 'cs@noorlms.com',
                'is_active' => true,
            ]
        );

        // Create major
        $major = Major::firstOrCreate(
            ['department_id' => $department->id, 'code' => 'CS-BSC'],
            [
                'name' => 'Bachelor of Computer Science',
                'name_ar' => 'بكالوريوس علوم الحاسب',
                'years_required' => 4,
                'credits_required' => 132,
                'is_active' => true,
            ]
        );

        // Create courses
        $courses = [
            ['CS101', 'Introduction to Programming', 'مقدمة في البرمجة', 3],
            ['CS102', 'Data Structures', 'هياكل البيانات', 3],
            ['CS201', 'Algorithms', 'الخوارزميات', 3],
            ['CS202', 'Database Systems', 'أنظمة قواعد البيانات', 3],
            ['CS301', 'Operating Systems', 'أنظمة التشغيل', 3],
            ['CS302', 'Computer Networks', 'شبكات الحاسب', 3],
            ['MATH101', 'Calculus I', 'تفاضل وتكامل I', 3],
            ['MATH201', 'Linear Algebra', 'الجبر الخطي', 3],
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['department_id' => $department->id, 'code' => $courseData[0]],
                [
                    'name' => $courseData[1],
                    'name_ar' => $courseData[2],
                    'credits' => $courseData[3],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Create assessment types.
     */
    protected function createAssessmentTypes(): void
    {
        $types = [
            ['Quiz', 'اختبار قصير', 'quiz', 10],
            ['Midterm Exam', 'امتحان نصفي', 'exam', 30],
            ['Assignment', 'واجب', 'assignment', 15],
            ['Project', 'مشروع', 'project', 20],
            ['Final Exam', 'امتحان نهائي', 'exam', 25],
        ];

        foreach ($types as $type) {
            AssessmentType::firstOrCreate(
                ['code' => $type[2]],
                [
                    'name' => $type[0],
                    'name_ar' => $type[1],
                    'weight' => $type[3],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Create sample teachers.
     */
    protected function createSampleTeachers(): void
    {
        $teachers = [
            ['John Smith', 'john.smith@noorlms.com', 'Dr. John'],
            ['Sarah Johnson', 'sarah.johnson@noorlms.com', 'Dr. Sarah'],
            ['Michael Brown', 'michael.brown@noorlms.com', 'Dr. Michael'],
        ];

        foreach ($teachers as $index => $teacher) {
            $user = User::firstOrCreate(
                ['email' => $teacher[1]],
                [
                    'name' => $teacher[0],
                    'password' => Hash::make('password'),
                ]
            );
            $user->assignRole('teacher');

            UserProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => explode(' ', $teacher[0])[0],
                    'last_name' => explode(' ', $teacher[0])[1] ?? 'Smith',
                    'user_id_unique' => 'TCH'.date('Y').str_pad($index + 2, 5, '0', STR_PAD_LEFT),
                ]
            );

            UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'theme' => 'light',
                    'locale' => 'en',
                ]
            );
        }
    }

    /**
     * Create sample students.
     */
    protected function createSampleStudents(): void
    {
        $students = [
            ['Ahmed Hassan', 'ahmed.hassan@noorlms.com'],
            ['Fatima Ali', 'fatima.ali@noorlms.com'],
            ['Omar Khaled', 'omar.khaled@noorlms.com'],
            ['Layla Mohammed', 'layla.mohammed@noorlms.com'],
            ['Youssef Ibrahim', 'youssef.ibrahim@noorlms.com'],
        ];

        foreach ($students as $index => $student) {
            $user = User::firstOrCreate(
                ['email' => $student[1]],
                [
                    'name' => $student[0],
                    'password' => Hash::make('password'),
                ]
            );
            $user->assignRole('student');

            UserProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => explode(' ', $student[0])[0],
                    'last_name' => explode(' ', $student[0])[1] ?? 'Student',
                    'user_id_unique' => 'STU'.date('Y').str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                ]
            );

            UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'theme' => 'light',
                    'locale' => 'en',
                ]
            );
        }
    }
}
