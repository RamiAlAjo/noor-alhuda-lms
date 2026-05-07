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
        // Rename course_section_id to course_offering_id in enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });

        // Also rename in other tables that might have this column
        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_records', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });

        Schema::table('course_materials', function (Blueprint $table) {
            if (Schema::hasColumn('course_materials', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });

        Schema::table('course_feedback', function (Blueprint $table) {
            if (Schema::hasColumn('course_feedback', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });

        Schema::table('discussion_forums', function (Blueprint $table) {
            if (Schema::hasColumn('discussion_forums', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });

        Schema::table('excused_absences', function (Blueprint $table) {
            if (Schema::hasColumn('excused_absences', 'course_section_id')) {
                $table->renameColumn('course_section_id', 'course_offering_id');
            }
        });

        // Rename course_offerings table back to course_offerings if it was renamed
        Schema::rename('course_sections', 'course_offerings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to course_section_id
        Schema::table('enrollments', function (Blueprint $table) {
            if (Schema::hasColumn('enrollments', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            if (Schema::hasColumn('assessments', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_records', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });

        Schema::table('course_materials', function (Blueprint $table) {
            if (Schema::hasColumn('course_materials', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });

        Schema::table('course_feedback', function (Blueprint $table) {
            if (Schema::hasColumn('course_feedback', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });

        Schema::table('discussion_forums', function (Blueprint $table) {
            if (Schema::hasColumn('discussion_forums', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });

        Schema::table('excused_absences', function (Blueprint $table) {
            if (Schema::hasColumn('excused_absences', 'course_offering_id')) {
                $table->renameColumn('course_offering_id', 'course_section_id');
            }
        });

        // Rename table back
        Schema::rename('course_offerings', 'course_sections');
    }
};
