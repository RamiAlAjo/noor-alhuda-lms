<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performance indexes to frequently queried columns.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        // Users table indexes
        if (Schema::hasColumn('users', 'user_id')) {
            $this->createIndexIfNotExists('users', 'user_id', 'users_user_id_index');
        }

        // Enrollments table indexes
        $this->createIndexIfNotExists('enrollments', 'student_id', 'enrollments_student_id_index');
        $this->createIndexIfNotExists('enrollments', 'course_offering_id', 'enrollments_course_offering_id_index');
        $this->createIndexIfNotExists('enrollments', 'semester_id', 'enrollments_semester_id_index');
        $this->createIndexIfNotExists('enrollments', 'status', 'enrollments_status_index');

        // Course sections table indexes (if exists)
        if (Schema::hasTable('course_sections')) {
            $this->createIndexIfNotExists('course_sections', 'course_id', 'course_sections_course_id_index');
            $this->createIndexIfNotExists('course_sections', 'semester_id', 'course_sections_semester_id_index');
            $this->createIndexIfNotExists('course_sections', 'teacher_id', 'course_sections_teacher_id_index');
        }

        // Course offerings table indexes (if exists)
        if (Schema::hasTable('course_offerings')) {
            $this->createIndexIfNotExists('course_offerings', 'course_id', 'course_offerings_course_id_index');
            $this->createIndexIfNotExists('course_offerings', 'semester_id', 'course_offerings_semester_id_index');
            $this->createIndexIfNotExists('course_offerings', 'teacher_id', 'course_offerings_teacher_id_index');
        }

        // Assessments table indexes
        $this->createIndexIfNotExists('assessments', 'course_offering_id', 'assessments_course_offering_id_index');
        $this->createIndexIfNotExists('assessments', 'assessment_type_id', 'assessments_assessment_type_id_index');

        // Student grades table indexes
        $this->createIndexIfNotExists('student_grades', 'student_id', 'student_grades_student_id_index');
        $this->createIndexIfNotExists('student_grades', 'assessment_id', 'student_grades_assessment_id_index');

        // Attendances table indexes (if columns exist)
        if (Schema::hasColumn('attendances', 'student_id')) {
            $this->createIndexIfNotExists('attendances', 'student_id', 'attendances_student_id_index');
        }
        if (Schema::hasColumn('attendances', 'course_offering_id')) {
            $this->createIndexIfNotExists('attendances', 'course_offering_id', 'attendances_course_offering_id_index');
        }

        // Student fees table indexes
        $this->createIndexIfNotExists('student_fees', 'student_id', 'student_fees_student_id_index');
        $this->createIndexIfNotExists('student_fees', 'status', 'student_fees_status_index');

        // Course materials
        $this->createIndexIfNotExists('course_materials', 'course_section_id', 'course_materials_course_section_id_index');

        // Course teachers
        $this->createIndexIfNotExists('course_teachers', 'teacher_id', 'course_teachers_teacher_id_index');

        // Attendance records
        $this->createIndexIfNotExists('attendance_records', 'student_id', 'attendance_records_student_id_index');
        $this->createIndexIfNotExists('attendance_records', 'course_section_id', 'attendance_records_course_section_id_index');

        // Announcements
        if (Schema::hasColumn('announcements', 'target_offering_id')) {
            $this->createIndexIfNotExists('announcements', 'target_offering_id', 'announcements_target_offering_id_index');
        }

        // Notifications
        $this->createIndexIfNotExists('notifications', 'user_id', 'notifications_user_id_index');
        $this->createIndexIfNotExists('notifications', 'read_at', 'notifications_read_at_index');

        // Messages
        $this->createIndexIfNotExists('messages', 'sender_id', 'messages_sender_id_index');
        $this->createIndexIfNotExists('messages', 'receiver_id', 'messages_receiver_id_index');

        // Activity logs
        $this->createIndexIfNotExists('activity_logs', 'user_id', 'activity_logs_user_id_index');
        $this->createIndexIfNotExists('activity_logs', 'created_at', 'activity_logs_created_at_index');

        // Payments
        $this->createIndexIfNotExists('payments', 'student_id', 'payments_student_id_index');
        $this->createIndexIfNotExists('payments', 'status', 'payments_status_index');

        // Questions
        $this->createIndexIfNotExists('questions', 'assessment_id', 'questions_assessment_id_index');

        // Student answers
        $this->createIndexIfNotExists('student_answers', 'student_id', 'student_answers_student_id_index');
        $this->createIndexIfNotExists('student_answers', 'question_id', 'student_answers_question_id_index');
    }

    /**
     * Create an index if it doesn't already exist.
     */
    private function createIndexIfNotExists(string $table, string $column, string $indexName): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (empty($indexes)) {
                DB::statement("ALTER TABLE {$table} ADD INDEX {$indexName}({$column})");
            }
        } elseif ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list({$table})");
            $indexNames = array_column($indexes, 'name');
            if (! in_array($indexName, $indexNames)) {
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column})");
            }
        } elseif ($driver === 'pgsql') {
            $exists = DB::select('SELECT 1 FROM pg_indexes WHERE indexname = ?', [$indexName]);
            if (empty($exists)) {
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column})");
            }
        }
    }

    /**
     * Drop an index if it exists.
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (! empty($indexes)) {
                DB::statement("ALTER TABLE {$table} DROP INDEX {$indexName}");
            }
        } elseif ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list({$table})");
            $indexNames = array_column($indexes, 'name');
            if (in_array($indexName, $indexNames)) {
                DB::statement("DROP INDEX {$indexName}");
            }
        } elseif ($driver === 'pgsql') {
            $exists = DB::select('SELECT 1 FROM pg_indexes WHERE indexname = ?', [$indexName]);
            if (! empty($exists)) {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table
        if (Schema::hasColumn('users', 'user_id')) {
            $this->dropIndexIfExists('users', 'users_user_id_index');
        }

        // Enrollments table
        $this->dropIndexIfExists('enrollments', 'enrollments_student_id_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_course_offering_id_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_semester_id_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_status_index');

        // Course sections table (if exists)
        if (Schema::hasTable('course_sections')) {
            $this->dropIndexIfExists('course_sections', 'course_sections_course_id_index');
            $this->dropIndexIfExists('course_sections', 'course_sections_semester_id_index');
            $this->dropIndexIfExists('course_sections', 'course_sections_teacher_id_index');
        }

        // Course offerings table (if exists)
        if (Schema::hasTable('course_offerings')) {
            $this->dropIndexIfExists('course_offerings', 'course_offerings_course_id_index');
            $this->dropIndexIfExists('course_offerings', 'course_offerings_semester_id_index');
            $this->dropIndexIfExists('course_offerings', 'course_offerings_teacher_id_index');
        }

        // Assessments table
        $this->dropIndexIfExists('assessments', 'assessments_course_offering_id_index');
        $this->dropIndexIfExists('assessments', 'assessments_assessment_type_id_index');

        // Student grades table
        $this->dropIndexIfExists('student_grades', 'student_grades_student_id_index');
        $this->dropIndexIfExists('student_grades', 'student_grades_assessment_id_index');

        // Attendances table
        if (Schema::hasColumn('attendances', 'student_id')) {
            $this->dropIndexIfExists('attendances', 'attendances_student_id_index');
        }
        if (Schema::hasColumn('attendances', 'course_offering_id')) {
            $this->dropIndexIfExists('attendances', 'attendances_course_offering_id_index');
        }

        // Student fees table
        $this->dropIndexIfExists('student_fees', 'student_fees_student_id_index');
        $this->dropIndexIfExists('student_fees', 'student_fees_status_index');

        // Course materials
        $this->dropIndexIfExists('course_materials', 'course_materials_course_section_id_index');

        // Course teachers
        $this->dropIndexIfExists('course_teachers', 'course_teachers_teacher_id_index');

        // Attendance records
        $this->dropIndexIfExists('attendance_records', 'attendance_records_student_id_index');
        $this->dropIndexIfExists('attendance_records', 'attendance_records_course_section_id_index');

        // Announcements
        if (Schema::hasColumn('announcements', 'target_offering_id')) {
            $this->dropIndexIfExists('announcements', 'announcements_target_offering_id_index');
        }

        // Notifications
        $this->dropIndexIfExists('notifications', 'notifications_user_id_index');
        $this->dropIndexIfExists('notifications', 'notifications_read_at_index');

        // Messages
        $this->dropIndexIfExists('messages', 'messages_sender_id_index');
        $this->dropIndexIfExists('messages', 'messages_receiver_id_index');

        // Activity logs
        $this->dropIndexIfExists('activity_logs', 'activity_logs_user_id_index');
        $this->dropIndexIfExists('activity_logs', 'activity_logs_created_at_index');

        // Payments
        $this->dropIndexIfExists('payments', 'payments_student_id_index');
        $this->dropIndexIfExists('payments', 'payments_status_index');

        // Questions
        $this->dropIndexIfExists('questions', 'questions_assessment_id_index');

        // Student answers
        $this->dropIndexIfExists('student_answers', 'student_answers_student_id_index');
        $this->dropIndexIfExists('student_answers', 'student_answers_question_id_index');
    }
};
