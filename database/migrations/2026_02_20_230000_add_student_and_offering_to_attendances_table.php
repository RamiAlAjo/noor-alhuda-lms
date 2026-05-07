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
        Schema::table('attendances', function (Blueprint $table) {
            // Add student_id if not exists
            if (! Schema::hasColumn('attendances', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable();
            }

            // Add course_section_id if not exists
            if (! Schema::hasColumn('attendances', 'course_section_id')) {
                $table->unsignedBigInteger('course_section_id')->nullable();
            }
        });

        // Add foreign keys using raw SQL (more reliable)
        try {
            DB::statement('ALTER TABLE attendances ADD FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Foreign key might already exist
        }

        try {
            DB::statement('ALTER TABLE attendances ADD FOREIGN KEY (course_section_id) REFERENCES course_sections(id) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Foreign key might already exist
        }

        // Add index
        try {
            DB::statement('CREATE INDEX IF NOT EXISTS idx_student_offering_date ON attendances (student_id, course_section_id, date)');
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE attendances DROP FOREIGN KEY attendances_student_id_foreign');
        } catch (\Exception $e) {
        }

        try {
            DB::statement('ALTER TABLE attendances DROP FOREIGN KEY attendances_course_section_id_foreign');
        } catch (\Exception $e) {
        }

        try {
            DB::statement('DROP INDEX IF EXISTS idx_student_offering_date ON attendances');
        } catch (\Exception $e) {
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('student_id');
            $table->dropColumn('course_section_id');
        });
    }
};
