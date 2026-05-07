<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table indexes
        $this->createIndexIfNotExists('users', 'email');
        $this->createIndexIfNotExists('users', 'is_active');
        $this->createIndexIfNotExists('users', 'status');
        $this->createIndexIfNotExists('users', ['is_active', 'status']);

        if (Schema::hasColumn('users', 'user_id')) {
            $this->createIndexIfNotExists('users', 'user_id', 'users_user_id_index');
        }

        // Enrollments table indexes
        $this->createIndexIfNotExists('enrollments', 'student_id');
        $this->createIndexIfNotExists('enrollments', 'course_offering_id');
        $this->createIndexIfNotExists('enrollments', 'semester_id');
        $this->createIndexIfNotExists('enrollments', 'status');
        $this->createIndexIfNotExists('enrollments', ['student_id', 'status']);
        $this->createIndexIfNotExists('enrollments', ['course_offering_id', 'status']);
        $this->createIndexIfNotExists('enrollments', 'approved_at');
        $this->createIndexIfNotExists('enrollments', 'enrolled_at');

        // Course offerings table indexes
        $this->createIndexIfNotExists('course_offerings', 'course_id');
        $this->createIndexIfNotExists('course_offerings', 'semester_id');
        $this->createIndexIfNotExists('course_offerings', 'teacher_id');
        $this->createIndexIfNotExists('course_offerings', 'is_active');
        $this->createIndexIfNotExists('course_offerings', ['semester_id', 'is_active']);

        // Student grades table indexes
        $this->createIndexIfNotExists('student_grades', 'student_id');
        $this->createIndexIfNotExists('student_grades', 'assessment_id');
        $this->createIndexIfNotExists('student_grades', 'enrollment_id');
        $this->createIndexIfNotExists('student_grades', ['student_id', 'assessment_id']);
        $this->createIndexIfNotExists('student_grades', 'graded_at');

        // Assessments table indexes
        $this->createIndexIfNotExists('assessments', 'course_offering_id');
        $this->createIndexIfNotExists('assessments', 'assessment_type_id');
        $this->createIndexIfNotExists('assessments', 'due_date');
        $this->createIndexIfNotExists('assessments', 'is_published');
        $this->createIndexIfNotExists('assessments', ['course_offering_id', 'is_published']);

        // Attendance table indexes
        $this->createIndexIfNotExists('attendances', 'enrollment_id');
        $this->createIndexIfNotExists('attendances', 'student_id');
        $this->createIndexIfNotExists('attendances', 'course_offering_id');
        $this->createIndexIfNotExists('attendances', 'date');
        $this->createIndexIfNotExists('attendances', ['enrollment_id', 'date']);

        // Payments table indexes
        $this->createIndexIfNotExists('payments', 'student_id');
        $this->createIndexIfNotExists('payments', 'student_fee_id');
        $this->createIndexIfNotExists('payments', 'status');
        $this->createIndexIfNotExists('payments', 'payment_method');
        $this->createIndexIfNotExists('payments', 'gateway_transaction_id');
        $this->createIndexIfNotExists('payments', 'created_at');
        $this->createIndexIfNotExists('payments', ['student_id', 'status']);

        // Student fees table indexes
        $this->createIndexIfNotExists('student_fees', 'student_id');
        $this->createIndexIfNotExists('student_fees', 'fee_id');
        $this->createIndexIfNotExists('student_fees', 'status');
        $this->createIndexIfNotExists('student_fees', 'due_date');
        $this->createIndexIfNotExists('student_fees', ['student_id', 'status']);

        // Notifications table indexes
        $this->createIndexIfNotExists('notifications', 'user_id');
        $this->createIndexIfNotExists('notifications', 'read_at');
        $this->createIndexIfNotExists('notifications', 'created_at');
        $this->createIndexIfNotExists('notifications', ['user_id', 'read_at']);

        // Messages table indexes
        $this->createIndexIfNotExists('messages', 'sender_id');
        $this->createIndexIfNotExists('messages', 'receiver_id');
        $this->createIndexIfNotExists('messages', 'read_at');
        $this->createIndexIfNotExists('messages', 'created_at');
        $this->createIndexIfNotExists('messages', ['receiver_id', 'read_at']);

        // Quiz attempts table indexes
        $this->createIndexIfNotExists('quiz_attempts', 'student_id');
        $this->createIndexIfNotExists('quiz_attempts', 'assessment_id');
        $this->createIndexIfNotExists('quiz_attempts', 'status');
        $this->createIndexIfNotExists('quiz_attempts', ['student_id', 'assessment_id']);
        $this->createIndexIfNotExists('quiz_attempts', 'submitted_at');

        // Grade appeals table indexes
        $this->createIndexIfNotExists('grade_appeals', 'student_id');
        $this->createIndexIfNotExists('grade_appeals', 'grade_id');
        $this->createIndexIfNotExists('grade_appeals', 'status');
        $this->createIndexIfNotExists('grade_appeals', 'created_at');
        $this->createIndexIfNotExists('grade_appeals', ['student_id', 'status']);

        // Discussion topics table indexes
        $this->createIndexIfNotExists('discussion_topics', 'forum_id');
        $this->createIndexIfNotExists('discussion_topics', 'user_id');
        $this->createIndexIfNotExists('discussion_topics', 'is_pinned');
        $this->createIndexIfNotExists('discussion_topics', 'created_at');
        $this->createIndexIfNotExists('discussion_topics', ['forum_id', 'is_pinned']);

        // Discussion replies table indexes
        $this->createIndexIfNotExists('discussion_replies', 'topic_id');
        $this->createIndexIfNotExists('discussion_replies', 'user_id');
        $this->createIndexIfNotExists('discussion_replies', 'created_at');

        // Calendar events table indexes
        $this->createIndexIfNotExists('calendar_events', 'user_id');
        $this->createIndexIfNotExists('calendar_events', 'start_time');
        $this->createIndexIfNotExists('calendar_events', 'end_time');
        $this->createIndexIfNotExists('calendar_events', ['user_id', 'start_time']);

        // Tasks table indexes
        $this->createIndexIfNotExists('tasks', 'user_id');
        $this->createIndexIfNotExists('tasks', 'is_completed');
        $this->createIndexIfNotExists('tasks', 'due_date');
        $this->createIndexIfNotExists('tasks', ['user_id', 'is_completed']);

        // Activity logs table indexes
        $this->createIndexIfNotExists('activity_logs', 'user_id');
        $this->createIndexIfNotExists('activity_logs', 'created_at');
        $this->createIndexIfNotExists('activity_logs', ['user_id', 'created_at']);
    }

    /**
     * Create an index if it doesn't already exist.
     */
    private function createIndexIfNotExists(string $table, string|array $column, ?string $indexName = null): void
    {
        $columns = is_array($column) ? implode(',', $column) : $column;
        $indexName = $indexName ?? $table.'_'.(is_array($column) ? implode('_', $column) : $column).'_index';

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (empty($indexes)) {
                DB::statement("ALTER TABLE {$table} ADD INDEX {$indexName}({$columns})");
            }
        } elseif ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list({$table})");
            $indexNames = array_column($indexes, 'name');
            if (! in_array($indexName, $indexNames)) {
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columns})");
            }
        } elseif ($driver === 'pgsql') {
            $exists = DB::select('SELECT 1 FROM pg_indexes WHERE indexname = ?', [$indexName]);
            if (empty($exists)) {
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columns})");
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
        $this->dropIndexIfExists('users', 'users_email_index');
        $this->dropIndexIfExists('users', 'users_is_active_index');
        $this->dropIndexIfExists('users', 'users_status_index');
        $this->dropIndexIfExists('users', 'users_is_active_status_index');

        if (Schema::hasColumn('users', 'user_id')) {
            $this->dropIndexIfExists('users', 'users_user_id_index');
        }

        $this->dropIndexIfExists('enrollments', 'enrollments_student_id_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_course_offering_id_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_semester_id_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_status_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_student_id_status_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_course_offering_id_status_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_approved_at_index');
        $this->dropIndexIfExists('enrollments', 'enrollments_enrolled_at_index');

        $this->dropIndexIfExists('course_offerings', 'course_offerings_course_id_index');
        $this->dropIndexIfExists('course_offerings', 'course_offerings_semester_id_index');
        $this->dropIndexIfExists('course_offerings', 'course_offerings_teacher_id_index');
        $this->dropIndexIfExists('course_offerings', 'course_offerings_is_active_index');
        $this->dropIndexIfExists('course_offerings', 'course_offerings_semester_id_is_active_index');

        $this->dropIndexIfExists('student_grades', 'student_grades_student_id_index');
        $this->dropIndexIfExists('student_grades', 'student_grades_assessment_id_index');
        $this->dropIndexIfExists('student_grades', 'student_grades_enrollment_id_index');
        $this->dropIndexIfExists('student_grades', 'student_grades_student_id_assessment_id_index');
        $this->dropIndexIfExists('student_grades', 'student_grades_graded_at_index');

        $this->dropIndexIfExists('assessments', 'assessments_course_offering_id_index');
        $this->dropIndexIfExists('assessments', 'assessments_assessment_type_id_index');
        $this->dropIndexIfExists('assessments', 'assessments_due_date_index');
        $this->dropIndexIfExists('assessments', 'assessments_is_published_index');
        $this->dropIndexIfExists('assessments', 'assessments_course_offering_id_is_published_index');

        $this->dropIndexIfExists('attendances', 'attendances_enrollment_id_index');
        $this->dropIndexIfExists('attendances', 'attendances_student_id_index');
        $this->dropIndexIfExists('attendances', 'attendances_course_offering_id_index');
        $this->dropIndexIfExists('attendances', 'attendances_date_index');
        $this->dropIndexIfExists('attendances', 'attendances_enrollment_id_date_index');

        $this->dropIndexIfExists('payments', 'payments_student_id_index');
        $this->dropIndexIfExists('payments', 'payments_student_fee_id_index');
        $this->dropIndexIfExists('payments', 'payments_status_index');
        $this->dropIndexIfExists('payments', 'payments_payment_method_index');
        $this->dropIndexIfExists('payments', 'payments_gateway_transaction_id_index');
        $this->dropIndexIfExists('payments', 'payments_created_at_index');
        $this->dropIndexIfExists('payments', 'payments_student_id_status_index');

        $this->dropIndexIfExists('student_fees', 'student_fees_student_id_index');
        $this->dropIndexIfExists('student_fees', 'student_fees_fee_id_index');
        $this->dropIndexIfExists('student_fees', 'student_fees_status_index');
        $this->dropIndexIfExists('student_fees', 'student_fees_due_date_index');
        $this->dropIndexIfExists('student_fees', 'student_fees_student_id_status_index');

        $this->dropIndexIfExists('notifications', 'notifications_user_id_index');
        $this->dropIndexIfExists('notifications', 'notifications_read_at_index');
        $this->dropIndexIfExists('notifications', 'notifications_created_at_index');
        $this->dropIndexIfExists('notifications', 'notifications_user_id_read_at_index');

        $this->dropIndexIfExists('messages', 'messages_sender_id_index');
        $this->dropIndexIfExists('messages', 'messages_receiver_id_index');
        $this->dropIndexIfExists('messages', 'messages_read_at_index');
        $this->dropIndexIfExists('messages', 'messages_created_at_index');
        $this->dropIndexIfExists('messages', 'messages_receiver_id_read_at_index');

        $this->dropIndexIfExists('quiz_attempts', 'quiz_attempts_student_id_index');
        $this->dropIndexIfExists('quiz_attempts', 'quiz_attempts_assessment_id_index');
        $this->dropIndexIfExists('quiz_attempts', 'quiz_attempts_status_index');
        $this->dropIndexIfExists('quiz_attempts', 'quiz_attempts_student_id_assessment_id_index');
        $this->dropIndexIfExists('quiz_attempts', 'quiz_attempts_submitted_at_index');

        $this->dropIndexIfExists('grade_appeals', 'grade_appeals_student_id_index');
        $this->dropIndexIfExists('grade_appeals', 'grade_appeals_grade_id_index');
        $this->dropIndexIfExists('grade_appeals', 'grade_appeals_status_index');
        $this->dropIndexIfExists('grade_appeals', 'grade_appeals_created_at_index');
        $this->dropIndexIfExists('grade_appeals', 'grade_appeals_student_id_status_index');

        $this->dropIndexIfExists('discussion_topics', 'discussion_topics_forum_id_index');
        $this->dropIndexIfExists('discussion_topics', 'discussion_topics_user_id_index');
        $this->dropIndexIfExists('discussion_topics', 'discussion_topics_is_pinned_index');
        $this->dropIndexIfExists('discussion_topics', 'discussion_topics_created_at_index');
        $this->dropIndexIfExists('discussion_topics', 'discussion_topics_forum_id_is_pinned_index');

        $this->dropIndexIfExists('discussion_replies', 'discussion_replies_topic_id_index');
        $this->dropIndexIfExists('discussion_replies', 'discussion_replies_user_id_index');
        $this->dropIndexIfExists('discussion_replies', 'discussion_replies_created_at_index');

        $this->dropIndexIfExists('calendar_events', 'calendar_events_user_id_index');
        $this->dropIndexIfExists('calendar_events', 'calendar_events_start_time_index');
        $this->dropIndexIfExists('calendar_events', 'calendar_events_end_time_index');
        $this->dropIndexIfExists('calendar_events', 'calendar_events_user_id_start_time_index');

        $this->dropIndexIfExists('tasks', 'tasks_user_id_index');
        $this->dropIndexIfExists('tasks', 'tasks_is_completed_index');
        $this->dropIndexIfExists('tasks', 'tasks_due_date_index');
        $this->dropIndexIfExists('tasks', 'tasks_user_id_is_completed_index');

        $this->dropIndexIfExists('activity_logs', 'activity_logs_user_id_index');
        $this->dropIndexIfExists('activity_logs', 'activity_logs_created_at_index');
        $this->dropIndexIfExists('activity_logs', 'activity_logs_user_id_created_at_index');
    }
};
