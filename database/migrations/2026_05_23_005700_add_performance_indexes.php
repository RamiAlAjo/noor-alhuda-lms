<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add performance indexes for high-traffic queries in the LMS.
     */
    public function up(): void
    {
        // Enrollments (use correct column name: course_offering_id)
        if (Schema::hasTable('enrollments')) {
            $this->createIndexIfNotExists('enrollments', ['student_id', 'status'], 'enrollments_student_status_idx');
            if (Schema::hasColumn('enrollments', 'course_offering_id')) {
                $this->createIndexIfNotExists('enrollments', ['course_offering_id', 'status'], 'enrollments_offering_status_idx');
            }
            $this->createIndexIfNotExists('enrollments', 'created_at', 'enrollments_created_at_idx');
        }

        // Student grades
        if (Schema::hasTable('student_grades')) {
            $this->createIndexIfNotExists('student_grades', 'student_id', 'student_grades_student_idx');
            $this->createIndexIfNotExists('student_grades', 'assessment_id', 'student_grades_assessment_idx');
            if (Schema::hasColumn('student_grades', 'grade')) {
                $this->createIndexIfNotExists('student_grades', ['student_id', 'grade'], 'student_grades_student_grade_idx');
            }
        }

        // Attendances
        if (Schema::hasTable('attendances')) {
            $this->createIndexIfNotExists('attendances', ['student_id', 'date'], 'attendances_student_date_idx');
            if (Schema::hasColumn('attendances', 'session_id')) {
                $this->createIndexIfNotExists('attendances', 'session_id', 'attendances_session_idx');
            }
        }

        // Notifications
        if (Schema::hasTable('notifications')) {
            $this->createIndexIfNotExists('notifications', ['user_id', 'read_at'], 'notifications_user_read_idx');
            $this->createIndexIfNotExists('notifications', 'created_at', 'notifications_created_at_idx');
        }

        // Messages
        if (Schema::hasTable('messages')) {
            if (Schema::hasColumn('messages', 'conversation_id')) {
                $this->createIndexIfNotExists('messages', ['conversation_id', 'created_at'], 'messages_conversation_created_idx');
            }
            $this->createIndexIfNotExists('messages', 'sender_id', 'messages_sender_idx');
        }

        // Student fees
        if (Schema::hasTable('student_fees')) {
            $this->createIndexIfNotExists('student_fees', ['student_id', 'status'], 'student_fees_student_status_idx');
        }

        // Grade appeals (no offering_id column exists)
        if (Schema::hasTable('grade_appeals')) {
            $this->createIndexIfNotExists('grade_appeals', ['student_id', 'status'], 'grade_appeals_student_status_idx');
            // offering_id does not exist on this table — intentionally skipped
        }

        // Medical leaves
        if (Schema::hasTable('medical_leaves')) {
            $this->createIndexIfNotExists('medical_leaves', ['student_id', 'status'], 'medical_leaves_student_status_idx');
        }

        // Calendar events
        if (Schema::hasTable('calendar_events') && Schema::hasColumn('calendar_events', 'start_date')) {
            $this->createIndexIfNotExists('calendar_events', ['user_id', 'start_date'], 'calendar_events_user_start_idx');
        }

        // Assessments (use correct column: course_offering_id)
        if (Schema::hasTable('assessments')) {
            if (Schema::hasColumn('assessments', 'course_offering_id')) {
                $this->createIndexIfNotExists('assessments', ['course_offering_id', 'due_date', 'is_published'], 'assessments_offering_due_idx');
            }
        }

        // Discussion replies
        if (Schema::hasTable('discussion_replies')) {
            $this->createIndexIfNotExists('discussion_replies', ['topic_id', 'created_at'], 'discussion_replies_topic_created_idx');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('enrollments', 'enrollments_student_status_idx');
        $this->dropIndexIfExists('enrollments', 'enrollments_offering_status_idx');
        $this->dropIndexIfExists('enrollments', 'enrollments_created_at_idx');

        $this->dropIndexIfExists('student_grades', 'student_grades_student_idx');
        $this->dropIndexIfExists('student_grades', 'student_grades_assessment_idx');
        $this->dropIndexIfExists('student_grades', 'student_grades_student_grade_idx');

        $this->dropIndexIfExists('attendances', 'attendances_student_date_idx');
        $this->dropIndexIfExists('attendances', 'attendances_session_idx');

        $this->dropIndexIfExists('notifications', 'notifications_user_read_idx');
        $this->dropIndexIfExists('notifications', 'notifications_created_at_idx');

        $this->dropIndexIfExists('messages', 'messages_conversation_created_idx');
        $this->dropIndexIfExists('messages', 'messages_sender_idx');

        $this->dropIndexIfExists('student_fees', 'student_fees_student_status_idx');

        $this->dropIndexIfExists('grade_appeals', 'grade_appeals_student_status_idx');
        $this->dropIndexIfExists('grade_appeals', 'grade_appeals_offering_idx');

        $this->dropIndexIfExists('medical_leaves', 'medical_leaves_student_status_idx');

        $this->dropIndexIfExists('calendar_events', 'calendar_events_user_start_idx');

        $this->dropIndexIfExists('assessments', 'assessments_offering_due_idx');

        $this->dropIndexIfExists('discussion_replies', 'discussion_replies_topic_created_idx');
    }

    /**
     * Create an index only if it does not already exist.
     */
    private function createIndexIfNotExists(string $table, string|array $column, ?string $indexName = null): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $columns = is_array($column) ? implode(',', $column) : $column;
        $indexName = $indexName ?? $table.'_'.(is_array($column) ? implode('_', $column) : $column).'_index';

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (empty($indexes)) {
                DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}`({$columns})");
            }
        } elseif ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list(`{$table}`)");
            $indexNames = array_column($indexes, 'name');
            if (! in_array($indexName, $indexNames)) {
                DB::statement("CREATE INDEX `{$indexName}` ON `{$table}` ({$columns})");
            }
        } elseif ($driver === 'pgsql') {
            $exists = DB::select('SELECT 1 FROM pg_indexes WHERE indexname = ?', [$indexName]);
            if (empty($exists)) {
                DB::statement("CREATE INDEX `{$indexName}` ON `{$table}` ({$columns})");
            }
        }
    }

    /**
     * Drop an index if it exists.
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (! empty($indexes)) {
                DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$indexName}`");
            }
        } elseif ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list(`{$table}`)");
            $indexNames = array_column($indexes, 'name');
            if (in_array($indexName, $indexNames)) {
                DB::statement("DROP INDEX `{$indexName}`");
            }
        } elseif ($driver === 'pgsql') {
            $exists = DB::select('SELECT 1 FROM pg_indexes WHERE indexname = ?', [$indexName]);
            if (! empty($exists)) {
                DB::statement("DROP INDEX IF EXISTS `{$indexName}`");
            }
        }
    }
};
