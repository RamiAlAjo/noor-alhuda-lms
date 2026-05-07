<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            // Add enrolled_count (renaming current_students if it exists)
            if (Schema::hasColumn('course_offerings', 'current_students')) {
                $table->renameColumn('current_students', 'enrolled_count');
            } elseif (! Schema::hasColumn('course_offerings', 'enrolled_count')) {
                $table->integer('enrolled_count')->default(0)->after('max_students');
            }

            // Add is_visible_to_students
            if (! Schema::hasColumn('course_offerings', 'is_visible_to_students')) {
                $table->boolean('is_visible_to_students')->default(true)->after('is_active');
            }

            // Add schedule_json
            if (! Schema::hasColumn('course_offerings', 'schedule_json')) {
                $table->json('schedule_json')->nullable()->after('schedule');
            }

            // Add meeting fields
            if (! Schema::hasColumn('course_offerings', 'meeting_link')) {
                $table->string('meeting_link')->nullable()->after('schedule_json');
            }
            if (! Schema::hasColumn('course_offerings', 'meeting_id')) {
                $table->string('meeting_id')->nullable()->after('meeting_link');
            }
            if (! Schema::hasColumn('course_offerings', 'meeting_password')) {
                $table->string('meeting_password')->nullable()->after('meeting_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn([
                'enrolled_count',
                'is_visible_to_students',
                'schedule_json',
                'meeting_link',
                'meeting_id',
                'meeting_password',
            ]);

            // Restore current_students if needed
            if (! Schema::hasColumn('course_offerings', 'current_students')) {
                $table->integer('current_students')->default(0);
            }
        });
    }
};
