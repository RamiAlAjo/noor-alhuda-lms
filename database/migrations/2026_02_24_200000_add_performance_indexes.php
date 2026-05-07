<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add index for course_offerings - frequently queried by teacher
        $this->addIndexIfNotExists('course_offerings', ['teacher_id', 'is_active'], 'course_offerings_teacher_active');
        $this->addIndexIfNotExists('course_offerings', ['semester_id', 'is_active'], 'course_offerings_semester_active');

        // Add index for course_materials - check which column exists
        if (Schema::hasColumn('course_materials', 'course_offering_id')) {
            $this->addIndexIfNotExists('course_materials', ['course_offering_id', 'week'], 'course_materials_offering_week');
        } elseif (Schema::hasColumn('course_materials', 'course_section_id')) {
            $this->addIndexIfNotExists('course_materials', ['course_section_id', 'week'], 'course_materials_offering_week');
        }

        // Add index for assessments - check which column exists
        if (Schema::hasColumn('assessments', 'offering_id')) {
            $this->addIndexIfNotExists('assessments', ['offering_id', 'due_date'], 'assessments_offering_due');
        } elseif (Schema::hasColumn('assessments', 'course_section_id')) {
            $this->addIndexIfNotExists('assessments', ['course_section_id', 'due_date'], 'assessments_offering_due');
        }
        $this->addIndexIfNotExists('assessments', ['is_published', 'due_date'], 'assessments_published_due');

        // Add index for messages
        $this->addIndexIfNotExists('messages', ['receiver_id', 'is_read'], 'messages_receiver_read');
        $this->addIndexIfNotExists('messages', ['sender_id', 'created_at'], 'messages_sender_created');

        // Add index for notifications
        $this->addIndexIfNotExists('notifications', ['user_id', 'is_read'], 'notifications_user_read');

        // Add index for activity_logs
        $this->addIndexIfNotExists('activity_logs', ['user_id', 'created_at'], 'activity_logs_user_created');
        $this->addIndexIfNotExists('activity_logs', ['action', 'created_at'], 'activity_logs_action_created');

        // Add index for student_fees
        $this->addIndexIfNotExists('student_fees', ['student_id', 'status'], 'student_fees_user_status');

        // Add index for payments
        $this->addIndexIfNotExists('payments', ['student_id', 'status'], 'payments_user_status');
    }

    /**
     * Add index if it doesn't exist.
     */
    private function addIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();

            // Check if index exists based on database driver
            $exists = false;

            if ($driver === 'mysql') {
                $exists = collect($connection->select('SHOW INDEX FROM '.$table))->pluck('Key_name')->contains($indexName);
            } elseif ($driver === 'sqlite') {
                $indexes = $connection->select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name=?", [$table]);
                $exists = collect($indexes)->pluck('name')->contains($indexName);
            }

            if (! $exists) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
                    $tableBlueprint->index($columns, $indexName);
                });
            }
        } catch (\Exception $e) {
            // Index might already exist or table doesn't exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes - these are optional and can be removed if needed
    }
};
